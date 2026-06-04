<?php
include_once __DIR__ . "/security.php";

function requireModerator(){
    initSecureSession();
    if(!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "moderator"){
        header("Location: ../view/login.php");
        exit();
    }
}
?>
