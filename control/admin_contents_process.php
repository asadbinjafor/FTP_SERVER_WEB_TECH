<?php
include_once '../model/mydb.php';
include_once 'admin_gate.php';
include_once 'init.php';
appInit(false);
requireAdmin();
$mydb = new MyDB();
$conn = requireDb($mydb);
$contents = $mydb->getAllContents($conn);
$mydb->closeConn($conn);
?>
