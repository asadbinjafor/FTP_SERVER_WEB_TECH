<?php
include_once '../model/mydb.php';
include_once 'admin_gate.php';
include_once 'security.php';

initSecureSession();
header("Content-Type: application/json");

requireAdmin();

if(!verifyCsrf($_POST["csrf_token"] ?? "")){
    echo json_encode(array("success" => false, "message" => "Invalid token"));
    exit();
}

$id = (int)($_POST["moderator_id"] ?? 0);
if($id <= 0){
    echo json_encode(array("success" => false, "message" => "Invalid moderator"));
    exit();
}

$mydb = new MyDB();
$conn = $mydb->createConn();
$ok = $mydb->deleteModeratorCascade($id, $conn);
$mydb->closeConn($conn);

echo json_encode(array("success" => $ok, "message" => $ok ? "Moderator deleted" : "Delete failed"));
?>
