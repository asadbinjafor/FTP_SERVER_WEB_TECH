<?php
include_once '../model/mydb.php';
include_once 'admin_gate.php';
include_once 'init.php';

requireAdmin();

$mydb = new MyDB();
$conn = requireDb($mydb);
$requests   = $mydb->getAllRequests($conn);
$pendingNav = $mydb->getPendingRequestCount($conn);
$mydb->closeConn($conn);
?>
