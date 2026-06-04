<?php
include_once '../model/mydb.php';
include_once 'moderator_gate.php';
include_once 'init.php';
requireModerator();

$filterCat = (int)($_GET["category"] ?? 0);
$mydb = new MyDB();
$conn = requireDb($mydb);
$contents   = $mydb->getContentsFiltered($filterCat, $conn);
$catList = array();
$catRes = $mydb->getTopCategories($conn);
while($row = $catRes->fetch_assoc()){
    $catList[] = $row;
}
$mydb->closeConn($conn);
?>
