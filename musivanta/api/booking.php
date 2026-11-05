<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $pdo = getDatabaseConnection();
        $rows = $pdo->query('SELECT * FROM bookings ORDER BY created_at DESC LIMIT 50')->fetchAll();
        echo json_encode(['success' => true, 'data' => $rows]);
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => 'Unable to load bookings.']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payload = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    try {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare('INSERT INTO bookings (id, customer_id, musician_id, booking_date, start_time, end_time, status, notes, created_at, updated_at) VALUES (:id, :customer_id, :musician_id, :booking_date, :start_time, :end_time, :status, :notes, NOW(), NOW())');
        $id = 'BKG' . str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        $stmt->execute([
            ':id' => $id,
            ':customer_id' => $payload['customer_id'] ?? '',
            ':musician_id' => $payload['musician_id'] ?? '',
            ':booking_date' => $payload['booking_date'] ?? date('Y-m-d'),
            ':start_time' => $payload['start_time'] ?? '18:00',
            ':end_time' => $payload['end_time'] ?? '20:00',
            ':status' => $payload['status'] ?? 'pending',
            ':notes' => $payload['notes'] ?? '',
        ]);
        echo json_encode(['success' => true, 'id' => $id]);
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => 'Unable to create booking.']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
