<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Non autorizzato']);
    exit;
}

try {
    $db = getDBConnection();
    $stmt = $db->query("
        SELECT a.*, s.name as service_name, s.price as service_price
        FROM appointments a 
        LEFT JOIN services s ON a.service_id = s.id 
        ORDER BY a.created_at DESC LIMIT 6
    ");
    $appointments = $stmt->fetchAll();

    $formatted = [];
    foreach ($appointments as $app) {
        $formatted[] = [
            'id' => $app['id'],
            'service_name' => $app['service_name'] ?? 'Servizio Personalizzato',
            'vehicle_brand' => $app['vehicle_brand'],
            'vehicle_model' => $app['vehicle_model'],
            'vehicle_registration' => $app['vehicle_registration'],
            'customer_name' => $app['customer_name'],
            'raw_booking_date' => $app['booking_date'],
            'raw_booking_time' => $app['booking_time'],
            'booking_date' => date('d/m/Y', strtotime($app['booking_date'])),
            'booking_time' => date('H:i', strtotime($app['booking_time'])),
            'status' => $app['status']
        ];
    }

    echo json_encode(['success' => true, 'appointments' => $formatted]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
