<?php
include_once '../model/mydb.php';
include_once 'admin_gate.php';
include_once 'init.php';

requireAdmin();

$mydb = new MyDB();
$conn = requireDb($mydb);
$mods       = $mydb->getAllModerators($conn);
$mydb->closeConn($conn);
?>
