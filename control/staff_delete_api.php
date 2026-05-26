<?php
include_once '../model/mydb.php';
include_once 'admin_gate.php';
include_once 'init.php';

initSecureSession();
header("Content-Type: application/json");

requireAdmin();

if(!verifyCsrf($_POST["csrf_token"] ?? "")){
    echo json_encode(array("success" => false, "message" => "Invalid token"));
    exit();
}

$id   = (int)($_POST["staff_id"] ?? 0);
$role = $_POST["role"] ?? "";

if($id <= 0 || !in_array($role, array("admin", "moderator"))){
    echo json_encode(array("success" => false, "message" => "Invalid data"));
    exit();
}

if($id === (int)$_SESSION["user_id"]){
    echo json_encode(array("success" => false, "message" => "Cannot delete your own account"));
    exit();
}

$mydb = new MyDB();
$conn = requireDb($mydb);

if($role === "moderator"){
    $ok = $mydb->deleteModeratorCascade($id, $conn);
} else {
    $ok = $mydb->deleteStaffUser($id, "admin", $conn);
}

$mydb->closeConn($conn);
echo json_encode(array("success" => $ok));
?>
