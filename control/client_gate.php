<?php
include_once __DIR__ . "/security.php";

function requireClient(){
    initSecureSession();
    if(!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "client"){
        header("Location: ../view/login.php");
        exit();
    }
}

function requireClientApi(){
    initSecureSession();
    header("Content-Type: application/json");
    if(!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "client"){
        echo json_encode(array("success" => false, "message" => "Client login required"));
        exit();
    }
}
?>
