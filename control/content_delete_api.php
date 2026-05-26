<?php
include_once '../model/mydb.php';
include_once 'security.php';

initSecureSession();
header("Content-Type: application/json");

if(!isset($_SESSION["user_id"]) || !in_array($_SESSION["role"], array("admin", "moderator"))){
    echo json_encode(array("success" => false, "message" => "Unauthorized"));
    exit();
}
if(!verifyCsrf($_POST["csrf_token"] ?? "")){
    echo json_encode(array("success" => false, "message" => "Invalid token"));
    exit();
}

$id = (int)($_POST["content_id"] ?? 0);
if($id <= 0){
    echo json_encode(array("success" => false, "message" => "Invalid content"));
    exit();
}

$mydb = new MyDB();
$conn = $mydb->createConn();
$ok = $mydb->deleteContentWithFile($id, $conn);
$mydb->closeConn($conn);

echo json_encode(array("success" => $ok, "message" => $ok ? "Content deleted" : "Delete failed"));
?>
