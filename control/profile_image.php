<?php
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/../model/mydb.php';
require_once __DIR__ . '/app.php';
initSecureSession();
if (empty($_SESSION['user_id'])) { http_response_code(403); exit(); }
$db = new MyDB();
$conn = $db->createConn();
if (!$conn) { http_response_code(503); exit(); }
$result = $db->getUserById((int)$_SESSION['user_id'], $conn);
$user = $result->fetch_assoc();
$name = $user['profile_picture'] ?? '';
$file = $name ? storedFilePath('profile', $name) : false;
if (!$file) { http_response_code(404); exit(); }
header('Content-Type: ' . (mime_content_type($file) ?: 'application/octet-stream'));
header('Cache-Control: private, max-age=300');
readfile($file);
if ($file !== PROFILE_UPLOAD_DIR . basename($name)) { unlink($file); }
