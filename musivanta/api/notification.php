<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

try {
    $pdo = getDatabaseConnection();
    $rows = $pdo->query('SELECT * FROM notifications ORDER BY created_at DESC LIMIT 50')->fetchAll();
    echo json_encode(['success' => true, 'data' => $rows]);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'message' => 'Unable to load notifications.']);
}
