<?php
require_once __DIR__ . '/config.php';

// Load PHPMailer files directly from includes/PHPMailer or vendor if available
if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    $vendorAutoload = __DIR__ . '/../vendor/autoload.php';
    if (file_exists($vendorAutoload)) {
        require_once $vendorAutoload;
    } else {
        $phpmailerDir = __DIR__ . '/PHPMailer/';
        if (file_exists($phpmailerDir . 'Exception.php') && file_exists($phpmailerDir . 'PHPMailer.php') && file_exists($phpmailerDir . 'SMTP.php')) {
            require_once $phpmailerDir . 'Exception.php';
            require_once $phpmailerDir . 'PHPMailer.php';
            require_once $phpmailerDir . 'SMTP.php';
        }
    }
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Configure and instantiate PHPMailer instance for Aruba SMTP
 */
function createPHPMailerInstance(): ?PHPMailer {
    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        return null;
    }

    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST; // Default: smtps.aruba.it
        $mail->SMTPAuth   = (!empty(MAIL_USER) && !empty(MAIL_PASS));
        $mail->Username   = MAIL_USER; // Aruba email: appointments@hkgarage.it
        $mail->Password   = MAIL_PASS;
        
        $port = intval(MAIL_PORT);
        $mail->Port = $port;

        if ($port === 465) {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        $mail->CharSet = 'UTF-8';
        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME); // HK Garage <appointments@hkgarage.it>
        $mail->addReplyTo(GARAGE_NOTIFICATION_EMAIL, MAIL_FROM_NAME);

        return $mail;
    } catch (Exception $e) {
        error_log("Failed to create PHPMailer instance: " . $e->getMessage());
        return null;
    }
}

/**
 * Send email helper function (PHPMailer with Aruba SMTP, with fallback)
 */
function sendEmail(string $toEmail, string $toName, string $subject, string $htmlBody): bool {
    $mail = createPHPMailerInstance();
    if ($mail !== null) {
        try {
            $mail->clearAddresses();
            $mail->addAddress($toEmail, $toName);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            return $mail->send();
        } catch (Exception $e) {
            error_log("PHPMailer Aruba SMTP Error [$toEmail]: " . $e->getMessage());
        }
    }

    // Fallback: Custom Socket SMTP if PHPMailer fails
    try {
        return sendSocketSMTP($toEmail, $toName, $subject, $htmlBody);
    } catch (Exception $e) {
        error_log("Socket SMTP Error [$toEmail]: " . $e->getMessage());
        return false;
    }
}

/**
 * Send dual notifications after a successful booking:
 * 1. Booking Confirmation to Customer
 * 2. New Booking Notification to Garage (appointments@hkgarage.it)
 * 
 * Never throws exceptions - error is logged silently if sending fails.
 */
function sendBookingNotificationEmails(array $details): array {
    $results = [
        'customer_email_sent' => false,
        'garage_email_sent'   => false
    ];

    $appointmentId = $details['appointment_id'];
    $customerName  = $details['customer_name'];
    $customerEmail = $details['customer_email'];
    $phone         = $details['phone'];
    $serviceName   = $details['service_name'];
    $bookingDate   = $details['booking_date_formatted'];
    $bookingTime   = $details['booking_time'];
    $vehicle       = $details['vehicle_brand'] . ' ' . $details['vehicle_model'] . ' (' . strtoupper($details['vehicle_registration']) . ')';
    $notes         = !empty($details['notes']) ? htmlspecialchars($details['notes']) : 'Nessuna nota aggiuntiva';

    // Build Responsive HTML Email Template for Customer
    $customerSubject = "Conferma Prenotazione #" . $appointmentId . " - HK Garage";
    $customerBody = generateCustomerEmailHTML([
        'appointment_id' => $appointmentId,
        'customer_name'  => $customerName,
        'service_name'   => $serviceName,
        'booking_date'   => $bookingDate,
        'booking_time'   => $bookingTime,
        'vehicle'        => $vehicle,
        'notes'          => $notes
    ]);

    // Build Responsive HTML Email Template for Garage Admin
    $garageEmail   = GARAGE_NOTIFICATION_EMAIL;
    $garageSubject = "[Nuova Prenotazione #" . $appointmentId . "] " . $customerName . " - " . $serviceName;
    $garageBody = generateGarageEmailHTML([
        'appointment_id' => $appointmentId,
        'customer_name'  => $customerName,
        'customer_email' => $customerEmail,
        'phone'          => $phone,
        'service_name'   => $serviceName,
        'booking_date'   => $bookingDate,
        'booking_time'   => $bookingTime,
        'vehicle'        => $vehicle,
        'notes'          => $notes
    ]);

    // Send Customer Email
    try {
        $results['customer_email_sent'] = sendEmail($customerEmail, $customerName, $customerSubject, $customerBody);
    } catch (Throwable $e) {
        error_log("Failed sending booking email to customer ($customerEmail): " . $e->getMessage());
    }

    // Send Garage Notification Email (appointments@hkgarage.it)
    try {
        $results['garage_email_sent'] = sendEmail($garageEmail, "HK Garage Management", $garageSubject, $garageBody);
    } catch (Throwable $e) {
        error_log("Failed sending booking email to garage ($garageEmail): " . $e->getMessage());
    }

    return $results;
}

/**
 * Generate Responsive HTML Email for Customer
 */
function generateCustomerEmailHTML(array $data): string {
    $logoUrl = SITE_URL . '/assets/img/logo.png';
    return "
    <!DOCTYPE html>
    <html lang='it'>
    <head>
      <meta charset='UTF-8'>
      <meta name='viewport' content='width=device-width, initial-scale=1.0'>
      <title>Conferma Prenotazione - HK Garage</title>
    </head>
    <body style='margin:0; padding:0; background-color:#f4f6f9; font-family: Arial, Helvetica, sans-serif; -webkit-font-smoothing:antialiased;'>
      <table role='presentation' width='100%' cellspacing='0' cellpadding='0' style='background-color:#f4f6f9; padding:20px 0;'>
        <tr>
          <td align='center'>
            <table role='presentation' width='600' cellspacing='0' cellpadding='0' style='background-color:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 4px 15px rgba(0,0,0,0.08); border-top:6px solid #d32f2f;'>
              
              <!-- Header -->
              <tr>
                <td style='background-color:#1a1c1e; padding:30px; text-align:center;'>
                  <h1 style='color:#ffffff; margin:0; font-size:26px; font-weight:900; letter-spacing:1px; text-transform:uppercase;'>
                    HK <span style='color:#d32f2f;'>GARAGE</span>
                  </h1>
                  <p style='color:#9e9e9e; margin:5px 0 0 0; font-size:12px; text-transform:uppercase; font-weight:bold; letter-spacing:0.5px;'>
                    Officina Meccanica &amp; Diagnosi Elettronica
                  </p>
                </td>
              </tr>

              <!-- Hero Banner -->
              <tr>
                <td style='padding:30px 30px 10px 30px; text-align:center;'>
                  <h2 style='color:#1a1c1e; margin:0 0 10px 0; font-size:22px; font-weight:800;'>
                    Prenotazione Ricevuta con Successo!
                  </h2>
                  <p style='color:#555555; font-size:15px; line-height:1.5; margin:0;'>
                    Ciao <strong>" . htmlspecialchars($data['customer_name']) . "</strong>, ti confermiamo che abbiamo ricevuto la tua richiesta di appuntamento per la tua vettura.
                  </p>
                </td>
              </tr>

              <!-- Details Card -->
              <tr>
                <td style='padding:20px 30px;'>
                  <table role='presentation' width='100%' cellspacing='0' cellpadding='0' style='background-color:#f8f9fa; border-radius:8px; padding:20px; border:1px solid #e9ecef;'>
                    <tr>
                      <td style='padding:8px 0; border-bottom:1px solid #e2e8f0; font-size:14px; color:#4a5568;'>
                        <strong>Codice Prenotazione:</strong>
                      </td>
                      <td style='padding:8px 0; border-bottom:1px solid #e2e8f0; font-size:16px; color:#d32f2f; font-weight:bold; text-align:right;'>
                        #" . htmlspecialchars($data['appointment_id']) . "
                      </td>
                    </tr>
                    <tr>
                      <td style='padding:8px 0; border-bottom:1px solid #e2e8f0; font-size:14px; color:#4a5568;'>
                        <strong>Servizio Richiesto:</strong>
                      </td>
                      <td style='padding:8px 0; border-bottom:1px solid #e2e8f0; font-size:14px; color:#1a1c1e; font-weight:bold; text-align:right;'>
                        " . htmlspecialchars($data['service_name']) . "
                      </td>
                    </tr>
                    <tr>
                      <td style='padding:8px 0; border-bottom:1px solid #e2e8f0; font-size:14px; color:#4a5568;'>
                        <strong>Data Appuntamento:</strong>
                      </td>
                      <td style='padding:8px 0; border-bottom:1px solid #e2e8f0; font-size:14px; color:#1a1c1e; font-weight:bold; text-align:right;'>
                        " . htmlspecialchars($data['booking_date']) . "
                      </td>
                    </tr>
                    <tr>
                      <td style='padding:8px 0; border-bottom:1px solid #e2e8f0; font-size:14px; color:#4a5568;'>
                        <strong>Orario:</strong>
                      </td>
                      <td style='padding:8px 0; border-bottom:1px solid #e2e8f0; font-size:14px; color:#1a1c1e; font-weight:bold; text-align:right;'>
                        " . htmlspecialchars($data['booking_time']) . "
                      </td>
                    </tr>
                    <tr>
                      <td style='padding:8px 0; border-bottom:1px solid #e2e8f0; font-size:14px; color:#4a5568;'>
                        <strong>Veicolo:</strong>
                      </td>
                      <td style='padding:8px 0; border-bottom:1px solid #e2e8f0; font-size:14px; color:#1a1c1e; font-weight:bold; text-align:right;'>
                        " . htmlspecialchars($data['vehicle']) . "
                      </td>
                    </tr>
                    <tr>
                      <td style='padding:8px 0; font-size:14px; color:#4a5568;'>
                        <strong>Stato:</strong>
                      </td>
                      <td style='padding:8px 0; font-size:14px; color:#d32f2f; font-weight:bold; text-align:right;'>
                        In Attesa di Conferma
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>

              <!-- Contact & Location Box -->
              <tr>
                <td style='padding:10px 30px 30px 30px;'>
                  <table role='presentation' width='100%' cellspacing='0' cellpadding='0' style='background-color:#1a1c1e; border-radius:8px; padding:20px; color:#ffffff;'>
                    <tr>
                      <td>
                        <h4 style='margin:0 0 10px 0; color:#d32f2f; font-size:14px; text-transform:uppercase; letter-spacing:0.5px;'>
                          Dove Trovarci &amp; Contatti
                        </h4>
                        <p style='margin:0 0 5px 0; font-size:13px; color:#e2e8f0;'>
                          📍 <strong>HK Garage SNC</strong> – Via Consortile della Conta, 3 - 24060 Costa di Mezzate (BG)
                        </p>
                        <p style='margin:0 0 5px 0; font-size:13px; color:#e2e8f0;'>
                          📞 <strong>Telefono / WhatsApp:</strong> +39 035 123 4567
                        </p>
                        <p style='margin:0; font-size:13px; color:#e2e8f0;'>
                          ✉️ <strong>Email:</strong> appointments@hkgarage.it
                        </p>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>

              <!-- Footer -->
              <tr>
                <td style='background-color:#f0f2f5; padding:20px; text-align:center; font-size:12px; color:#718096; border-top:1px solid #e2e8f0;'>
                  <p style='margin:0 0 5px 0;'>&copy; " . date('Y') . " HK Garage SNC di Harshit &amp; Karan. Tutti i diritti riservati.</p>
                  <p style='margin:0;'>Ricevi questa email in seguito a una richiesta di prenotazione su hkgarage.it</p>
                </td>
              </tr>

            </table>
          </td>
        </tr>
      </table>
    </body>
    </html>
    ";
}

/**
 * Generate Responsive HTML Email for Garage Admin
 */
function generateGarageEmailHTML(array $data): string {
    return "
    <!DOCTYPE html>
    <html lang='it'>
    <head>
      <meta charset='UTF-8'>
      <meta name='viewport' content='width=device-width, initial-scale=1.0'>
      <title>Nuova Prenotazione Garage</title>
    </head>
    <body style='margin:0; padding:0; background-color:#f4f6f9; font-family: Arial, Helvetica, sans-serif;'>
      <table role='presentation' width='100%' cellspacing='0' cellpadding='0' style='padding:20px 0;'>
        <tr>
          <td align='center'>
            <table role='presentation' width='600' cellspacing='0' cellpadding='0' style='background-color:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 4px 15px rgba(0,0,0,0.08); border-top:6px solid #1a1c1e;'>
              
              <tr style='background-color:#1a1c1e;'>
                <td style='padding:25px; text-align:center;'>
                  <h2 style='color:#ffffff; margin:0; font-size:22px; text-transform:uppercase;'>
                    🔔 NUOVA PRENOTAZIONE APPUNTAMENTO
                  </h2>
                </td>
              </tr>

              <tr>
                <td style='padding:30px;'>
                  <p style='font-size:15px; color:#2d3748; margin-top:0;'>
                    È stata inserita una nuova prenotazione dal sito web. Di seguito tutti i dettagli:
                  </p>

                  <table role='presentation' width='100%' cellspacing='0' cellpadding='0' style='background-color:#f8f9fa; border-radius:8px; padding:20px; border:1px solid #cbd5e0; margin-bottom:20px;'>
                    <tr>
                      <td style='padding:8px 0; border-bottom:1px solid #e2e8f0; font-size:14px;'><strong>ID Prenotazione:</strong></td>
                      <td style='padding:8px 0; border-bottom:1px solid #e2e8f0; font-size:16px; color:#d32f2f; font-weight:bold; text-align:right;'>#" . htmlspecialchars($data['appointment_id']) . "</td>
                    </tr>
                    <tr>
                      <td style='padding:8px 0; border-bottom:1px solid #e2e8f0; font-size:14px;'><strong>Cliente:</strong></td>
                      <td style='padding:8px 0; border-bottom:1px solid #e2e8f0; font-size:14px; font-weight:bold; text-align:right;'>" . htmlspecialchars($data['customer_name']) . "</td>
                    </tr>
                    <tr>
                      <td style='padding:8px 0; border-bottom:1px solid #e2e8f0; font-size:14px;'><strong>Email:</strong></td>
                      <td style='padding:8px 0; border-bottom:1px solid #e2e8f0; font-size:14px; text-align:right;'><a href='mailto:" . htmlspecialchars($data['customer_email']) . "'>" . htmlspecialchars($data['customer_email']) . "</a></td>
                    </tr>
                    <tr>
                      <td style='padding:8px 0; border-bottom:1px solid #e2e8f0; font-size:14px;'><strong>Telefono:</strong></td>
                      <td style='padding:8px 0; border-bottom:1px solid #e2e8f0; font-size:14px; text-align:right;'><a href='tel:" . htmlspecialchars($data['phone']) . "'>" . htmlspecialchars($data['phone']) . "</a></td>
                    </tr>
                    <tr>
                      <td style='padding:8px 0; border-bottom:1px solid #e2e8f0; font-size:14px;'><strong>Servizio:</strong></td>
                      <td style='padding:8px 0; border-bottom:1px solid #e2e8f0; font-size:14px; font-weight:bold; text-align:right;'>" . htmlspecialchars($data['service_name']) . "</td>
                    </tr>
                    <tr>
                      <td style='padding:8px 0; border-bottom:1px solid #e2e8f0; font-size:14px;'><strong>Data &amp; Orario:</strong></td>
                      <td style='padding:8px 0; border-bottom:1px solid #e2e8f0; font-size:14px; font-weight:bold; text-align:right;'>" . htmlspecialchars($data['booking_date']) . " @ " . htmlspecialchars($data['booking_time']) . "</td>
                    </tr>
                    <tr>
                      <td style='padding:8px 0; border-bottom:1px solid #e2e8f0; font-size:14px;'><strong>Veicolo:</strong></td>
                      <td style='padding:8px 0; border-bottom:1px solid #e2e8f0; font-size:14px; font-weight:bold; text-align:right;'>" . htmlspecialchars($data['vehicle']) . "</td>
                    </tr>
                    <tr>
                      <td style='padding:8px 0; font-size:14px;'><strong>Note Cliente:</strong></td>
                      <td style='padding:8px 0; font-size:14px; text-align:right;'>" . $data['notes'] . "</td>
                    </tr>
                  </table>

                  <div style='text-align:center; margin-top:25px;'>
                    <a href='" . SITE_URL . "/admin/appointment-view.php?id=" . htmlspecialchars($data['appointment_id']) . "' style='background-color:#d32f2f; color:#ffffff; text-decoration:none; padding:12px 25px; border-radius:6px; font-weight:bold; display:inline-block; font-size:14px; text-transform:uppercase;'>
                      Gestisci nel Pannello Admin
                    </a>
                  </div>
                </td>
              </tr>

              <tr style='background-color:#f0f2f5;'>
                <td style='padding:15px; text-align:center; font-size:12px; color:#718096;'>
                  HK Garage Admin Notification System &bull; appointments@hkgarage.it
                </td>
              </tr>

            </table>
          </td>
        </tr>
      </table>
    </body>
    </html>
    ";
}

/**
 * Pure PHP Socket SMTP Mailer Fallback
 */
function sendSocketSMTP(string $to, string $toName, string $subject, string $htmlBody): bool {
    $host     = MAIL_HOST;
    $port     = intval(MAIL_PORT);
    $user     = MAIL_USER;
    $pass     = MAIL_PASS;
    $from     = MAIL_FROM;
    $fromName = MAIL_FROM_NAME;

    $context = stream_context_create([
        'ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true
        ]
    ]);

    $prefix = ($port === 465) ? 'ssl://' : '';
    $socket = @stream_socket_client($prefix . $host . ':' . $port, $errno, $errstr, 15, STREAM_CLIENT_CONNECT, $context);

    if (!$socket) {
        error_log("SMTP connection failed to $host:$port - $errstr ($errno)");
        return false;
    }

    $response = fgets($socket, 512);

    fputs($socket, "EHLO " . gethostname() . "\r\n");
    while ($line = fgets($socket, 512)) {
        if (substr($line, 3, 1) === ' ') break;
    }

    if ($port === 587 || $port === 25) {
        fputs($socket, "STARTTLS\r\n");
        $tlsResp = fgets($socket, 512);
        if (strpos($tlsResp, '220') === 0) {
            $crypto = @stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT);
            if ($crypto) {
                fputs($socket, "EHLO " . gethostname() . "\r\n");
                while ($line = fgets($socket, 512)) {
                    if (substr($line, 3, 1) === ' ') break;
                }
            }
        }
    }

    if (!empty($user) && !empty($pass)) {
        fputs($socket, "AUTH LOGIN\r\n");
        fgets($socket, 512);
        fputs($socket, base64_encode($user) . "\r\n");
        fgets($socket, 512);
        fputs($socket, base64_encode($pass) . "\r\n");
        $authResp = fgets($socket, 512);
        if (strpos($authResp, '235') !== 0) {
            error_log("Aruba SMTP auth error: " . trim($authResp));
            fclose($socket);
            return false;
        }
    }

    fputs($socket, "MAIL FROM: <$from>\r\n");
    fgets($socket, 512);
    fputs($socket, "RCPT TO: <$to>\r\n");
    fgets($socket, 512);
    fputs($socket, "DATA\r\n");
    fgets($socket, 512);

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: $fromName <$from>\r\n";
    $headers .= "To: $toName <$to>\r\n";
    $headers .= "Subject: $subject\r\n";

    fputs($socket, $headers . "\r\n" . $htmlBody . "\r\n.\r\n");
    $dataResp = fgets($socket, 512);

    fputs($socket, "QUIT\r\n");
    fclose($socket);

    return (strpos($dataResp, '250') === 0);
}
