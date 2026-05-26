<?php
include_once '../model/mydb.php';
include_once 'client_gate.php';
include_once 'init.php';

requireClient();

$mydb = new MyDB();
$conn = requireDb($mydb);
$requests = $mydb->getClientRequests($_SESSION["user_id"], $conn);
$mydb->closeConn($conn);
?>
