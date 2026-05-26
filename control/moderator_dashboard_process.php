<?php
include_once '../model/mydb.php';
include_once 'moderator_gate.php';
include_once 'init.php';

requireModerator();

$mydb = new MyDB();
$conn = requireDb($mydb);

$pending    = $mydb->getPendingRequestCount($conn);
$contentN   = (int)$conn->query("SELECT COUNT(*) AS n FROM contents")->fetch_assoc()["n"];
$mydb->closeConn($conn);
?>
