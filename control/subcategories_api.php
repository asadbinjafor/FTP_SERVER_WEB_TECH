<?php
include_once '../model/mydb.php';
include_once 'security.php';

initSecureSession();
header("Content-Type: application/json");

$parentId = (int)($_GET["parent_id"] ?? 0);
$mydb = new MyDB();
$conn = $mydb->createConn();
$result = $mydb->getSubcategories($parentId, $conn);
$items = array();
while($row = $result->fetch_assoc()){
    $items[] = array("id" => (int)$row["id"], "name" => $row["name"]);
}
$mydb->closeConn($conn);
echo json_encode(array("success" => true, "items" => $items));
?>
