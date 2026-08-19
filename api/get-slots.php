<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';

$date = $_GET['date'] ?? '';

if (empty($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    echo json_encode(['success' => false, 'message' => 'Data non valida.']);
    exit;
}

$timestamp = strtotime($date);
$dayOfWeek = date('N', $timestamp); // 1 = Mon, 7 = Sun

// Check if Sunday
if ($dayOfWeek == 7) {
    echo json_encode([
        'success' => true,
        'date' => $date,
        'is_closed' => true,
        'message' => 'Domenica l\'officina è chiusa.',
        'slots' => []
    ]);
    exit;
}

// Set business start and end times
// Mon-Fri (1-5): 08:00 to 18:00 (Last start slot 17:30)
// Sat (6): 08:00 to 12:00 (Last start slot 11:30)
$startHour = 8;
$startMin = 0;
$endHour = ($dayOfWeek == 6) ? 12 : 18;

// Generate 30-minute interval slots
$allSlots = [];
$currentTime = strtotime("{$date} {$startHour}:{$startMin}:00");
$endTime = strtotime("{$date} {$endHour}:00:00");

while ($currentTime < $endTime) {
    $allSlots[] = date('H:i', $currentTime);
    $currentTime = strtotime('+30 minutes', $currentTime);
}

// Fetch booked slots from DB
$bookedSlots = [];
try {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT TIME_FORMAT(booking_time, '%H:%i') as booked_time FROM appointments WHERE booking_date = :date AND status != 'Cancelled'");
    $stmt->execute([':date' => $date]);
    $bookedSlots = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    error_log("Fetch slots DB error: " . $e->getMessage());
}

// Map slots with availability flag
$slotsResult = [];
foreach ($allSlots as $slot) {
    $isBooked = in_array($slot, $bookedSlots);
    $slotsResult[] = [
        'time' => $slot,
        'available' => !$isBooked
    ];
}

echo json_encode([
    'success' => true,
    'date' => $date,
    'day_of_week' => $dayOfWeek,
    'is_closed' => false,
    'slots' => $slotsResult
]);
