<?php
include_once '../model/mydb.php';
include_once 'client_gate.php';

requireClientApi();

if(!verifyCsrf($_POST["csrf_token"] ?? "")){
    echo json_encode(array("success" => false, "message" => "Invalid security token"));
    exit();
}

$title    = sanitizeText($_POST["content_title"] ?? "", 200);
$category = sanitizeText($_POST["category_requested"] ?? "", 100);
$message  = sanitizeText($_POST["message"] ?? "", 1000);

if($title === ""){
    echo json_encode(array("success" => false, "message" => "Content title is required"));
    exit();
}
if($category === ""){
    echo json_encode(array("success" => false, "message" => "Category is required"));
    exit();
}

$mydb = new MyDB();
$conn = requireDb($mydb);

$ok = $mydb->addContentRequest(
    $title,
    $category,
    $message,
    (int)$_SESSION["user_id"],
    requesterIp(),
    memberSessionId(),
    $conn
);

$mydb->closeConn($conn);

if($ok){
    echo json_encode(array("success" => true, "message" => "Request submitted! Admin will review it."));
} else {
    echo json_encode(array("success" => false, "message" => "Could not save request"));
}
?>
