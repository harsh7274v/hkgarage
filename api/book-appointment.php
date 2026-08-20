<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';

// Support JSON input or POST form data
$rawInput = file_get_contents('php://input');
$json = json_decode($rawInput, true);

$data = is_array($json) ? $json : $_POST;

$customerName = trim($data['customer_name'] ?? '');
$phone        = trim($data['phone'] ?? '');
$email        = trim(filter_var($data['email'] ?? '', FILTER_SANITIZE_EMAIL));
$vehicleBrand = trim($data['vehicle_brand'] ?? '');
$vehicleModel = trim($data['vehicle_model'] ?? '');
$registration = trim($data['vehicle_registration'] ?? '');
$serviceIdRaw  = $data['service_id'] ?? '';
$customService = trim($data['custom_service'] ?? '');
$bookingDate   = trim($data['booking_date'] ?? '');
$bookingTime   = trim($data['booking_time'] ?? '');
$notes         = trim($data['notes'] ?? '');

$isOtherService = ($serviceIdRaw === 'other' || $serviceIdRaw === '6' || $serviceIdRaw == 6);

// ── Validation ────────────────────────────────────────────────────────────────
if (empty($customerName) || empty($phone) || empty($email) || empty($vehicleBrand) ||
    empty($vehicleModel) || empty($registration) || empty($serviceIdRaw) ||
    empty($bookingDate) || empty($bookingTime)) {
    echo json_encode(['success' => false, 'message' => 'Compila tutti i campi obbligatori del modulo.']);
    exit;
}

if ($isOtherService && empty($customService)) {
    echo json_encode(['success' => false, 'message' => 'Specificare il servizio richiesto nel campo "Altro".']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Indirizzo email non valido.']);
    exit;
}

$bookingTimestamp = strtotime($bookingDate);
if (!$bookingTimestamp) {
    echo json_encode(['success' => false, 'message' => 'Data selezionata non valida.']);
    exit;
}

if (date('N', $bookingTimestamp) == 7) {
    echo json_encode(['success' => false, 'message' => "L'officina è chiusa di domenica. Scegli un'altra data."]);
    exit;
}

if (!preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]$/', $bookingTime)) {
    echo json_encode(['success' => false, 'message' => 'Orario selezionato non valido.']);
    exit;
}

// ── Database booking with transaction + row-level lock ────────────────────────
try {
    $db = getDBConnection();

    if ($isOtherService) {
        $serviceId = 1; // Fallback valid FK for DB constraint
        $service = [
            'name' => 'Altro: ' . $customService,
            'duration' => 30,
            'price' => 0.00
        ];
        $notes = "[Servizio Personalizzato: " . $customService . "]" . (!empty($notes) ? "\n" . $notes : "");
    } else {
        $serviceId = intval($serviceIdRaw);
        $stmtService = $db->prepare("SELECT name, duration, price FROM services WHERE id = :id AND active = 1");
        $stmtService->execute([':id' => $serviceId]);
        $service = $stmtService->fetch();

        if (!$service) {
            echo json_encode(['success' => false, 'message' => 'Servizio selezionato non valido o non disponibile.']);
            exit;
        }
    }

    // ── Begin atomic transaction ─────────────────────────────────────────────
    $db->beginTransaction();

    try {
        // Lock the slot row for this date+time (SELECT FOR UPDATE prevents race conditions).
        // This ensures that even if two users submit at exactly the same millisecond,
        // only one transaction can hold the lock and proceed.
        $stmtLock = $db->prepare(
            "SELECT id FROM appointments
             WHERE booking_date = :date
               AND TIME_FORMAT(booking_time, '%H:%i') = :time
               AND status != 'Cancelled'
             FOR UPDATE"
        );
        $stmtLock->execute([':date' => $bookingDate, ':time' => $bookingTime]);
        $existing = $stmtLock->fetch();

        // ── Slot already taken — give clear fallback to second user ──────────
        if ($existing) {
            $db->rollBack();
            echo json_encode([
                'success' => false,
                'slot_taken' => true,
                'message' => 'Spiacenti, lo slot delle ' . htmlspecialchars($bookingTime) .
                             ' del ' . date('d/m/Y', $bookingTimestamp) .
                             ' è appena stato prenotato da un altro cliente. ' .
                             'Seleziona un orario diverso per proseguire.'
            ]);
            exit;
        }

        // ── Slot is free — insert into database FIRST ────────────────────────
        $stmtInsert = $db->prepare("
            INSERT INTO appointments
            (customer_name, phone, email, vehicle_brand, vehicle_model, vehicle_registration,
             service_id, booking_date, booking_time, status, notes)
            VALUES
            (:name, :phone, :email, :brand, :model, :reg, :service_id, :date, :time, 'Pending', :notes)
        ");

        $stmtInsert->execute([
            ':name'       => $customerName,
            ':phone'      => $phone,
            ':email'      => $email,
            ':brand'      => $vehicleBrand,
            ':model'      => $vehicleModel,
            ':reg'        => strtoupper($registration),
            ':service_id' => $serviceId,
            ':date'       => $bookingDate,
            ':time'       => $bookingTime,
            ':notes'      => $notes
        ]);

        $appointmentId = $db->lastInsertId();

        // ── Commit the transaction — booking is now saved in DB ──────────────
        $db->commit();

    } catch (Exception $txException) {
        $db->rollBack();

        // Catch MySQL unique key duplicate entry (duplicate booking_date + booking_time)
        // Error code 23000 = Integrity constraint violation (covers UNIQUE KEY violations)
        $errorCode = $txException instanceof PDOException ? $txException->getCode() : 0;
        if ($errorCode == 23000 || strpos($txException->getMessage(), 'Duplicate entry') !== false) {
            echo json_encode([
                'success'    => false,
                'slot_taken' => true,
                'message'    => 'Spiacenti, lo slot delle ' . htmlspecialchars($bookingTime) .
                                ' del ' . date('d/m/Y', $bookingTimestamp) .
                                ' è appena stato prenotato da un altro cliente. ' .
                                'Seleziona un orario diverso per proseguire.'
            ]);
        } else {
            error_log("Booking transaction error: " . $txException->getMessage());
            echo json_encode(['success' => false, 'message' => 'Errore durante il salvataggio. Riprova.']);
        }
        exit;
    }

    // ── DB committed successfully — NOW send emails (non-blocking) ───────────
    // Email failure NEVER affects the confirmed booking.
    $emailResults = ['customer_email_sent' => false, 'garage_email_sent' => false];
    try {
        require_once __DIR__ . '/../includes/mailer.php';
        $emailResults = sendBookingNotificationEmails([
            'appointment_id'         => $appointmentId,
            'customer_name'          => $customerName,
            'customer_email'         => $email,
            'phone'                  => $phone,
            'service_name'           => $service['name'],
            'booking_date_formatted' => date('d/m/Y', $bookingTimestamp),
            'booking_time'           => $bookingTime,
            'vehicle_brand'          => $vehicleBrand,
            'vehicle_model'          => $vehicleModel,
            'vehicle_registration'   => $registration,
            'notes'                  => $notes
        ]);
    } catch (Throwable $mailError) {
        error_log("Email notification failed for booking #$appointmentId: " . $mailError->getMessage());
        // Booking is already saved — we just log and continue
    }

    // ── Return success to the user ────────────────────────────────────────────
    echo json_encode([
        'success'         => true,
        'appointment_id'  => $appointmentId,
        'message'         => 'Richiesta inviata con successo! Codice: #' . $appointmentId . '. Stato: In Attesa (Pending). Riceverai a breve un\'email di conferma da HK Garage.',
        'email_sent'      => $emailResults['customer_email_sent'],
        'garage_notified' => $emailResults['garage_email_sent']
    ]);

} catch (Exception $e) {
    error_log("Booking error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Errore interno durante la prenotazione. Riprova.']);
}
