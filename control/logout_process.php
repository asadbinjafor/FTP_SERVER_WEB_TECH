<?php
include_once 'auth.php';
include_once 'security.php';

initSecureSession();
clearRememberCookie();
$_SESSION = array();
session_destroy();
header("Location: ../view/Home.php");
exit();
?>
