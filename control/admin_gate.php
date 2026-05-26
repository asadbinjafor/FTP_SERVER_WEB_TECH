<?php
include_once __DIR__ . "/security.php";

function requireAdmin(){
    initSecureSession();
    if(!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin"){
        header("Location: ../view/login.php");
        exit();
    }
}
?>
