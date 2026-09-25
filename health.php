<?php
require_once __DIR__ . '/model/mydb.php';
header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store');
$db = new MyDB();
$conn = $db->createConn();
if (!$conn) {
    http_response_code(503);
    echo json_encode(array('status' => 'unavailable', 'database' => 'unavailable'));
    exit();
}
try {
    $conn->query('SELECT 1');
    echo json_encode(array('status' => 'ok', 'database' => 'ok'));
} catch (Throwable $e) {
    error_log('Health check database query failed: ' . $e->getMessage());
    http_response_code(503);
    echo json_encode(array('status' => 'unavailable', 'database' => 'unavailable'));
}
