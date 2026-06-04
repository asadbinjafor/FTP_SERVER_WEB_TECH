<?php
include_once '../model/mydb.php';
include_once 'security.php';

initSecureSession();
sendSecurityHeaders();
header("Content-Type: application/json");

if(!isset($_SESSION["user_id"]) || !in_array($_SESSION["role"], array("admin", "moderator"))){
    echo json_encode(array("success" => false, "message" => "Unauthorized"));
    exit();
}
if(!verifyCsrf($_POST["csrf_token"] ?? "")){
    echo json_encode(array("success" => false, "message" => "Invalid token"));
    exit();
}

$id     = (int)($_POST["request_id"] ?? 0);
$status = $_POST["status"] ?? "";
if($id <= 0 || !in_array($status, array("pending", "fulfilled", "rejected"))){
    echo json_encode(array("success" => false, "message" => "Invalid data"));
    exit();
}

$mydb = new MyDB();
$conn = $mydb->createConn();
$ok   = $mydb->updateRequestStatus($id, $status, $conn);
$mydb->closeConn($conn);

echo json_encode(array("success" => $ok, "message" => $ok ? "Status updated" : "Update failed"));
?>
