<?php
include_once '../model/mydb.php';
include_once 'auth.php';
include_once 'admin_gate.php';
include_once 'init.php';

requireAdmin();

$mydb = new MyDB();
$conn = requireDb($mydb);
tryRememberLogin($mydb, $conn);
$counts = $mydb->getAdminDashboardCounts($conn);
$mydb->closeConn($conn);
?>
