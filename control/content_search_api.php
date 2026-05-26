<?php
include_once '../model/mydb.php';
include_once 'init.php';
include_once 'app.php';

appInit(false);
header("Content-Type: application/json");

$q        = sanitizeText($_GET["q"] ?? "", 100);
$category = (int)($_GET["category"] ?? 0);
$sub      = (int)($_GET["sub"] ?? 0);
$fileType = sanitizeText($_GET["file_type"] ?? "", 20);

if(strlen($q) > 0 && strlen($q) < 2){
    echo json_encode(array("success" => false, "message" => "Search must be at least 2 characters"));
    exit();
}

$mydb = new MyDB();
$conn = requireDb($mydb);

if($q !== ""){
    $result = $mydb->searchContents($q, $category, $sub, $fileType, $conn);
} else {
    $result = $mydb->getContentsByCategory($category, $sub, $fileType, $conn);
}

$items = array();
while($row = $result->fetch_assoc()){
    $items[] = array(
        "id"             => (int)$row["id"],
        "title"          => $row["title"],
        "description"    => $row["description"],
        "category_name"  => $row["category_name"],
        "file_type"      => $row["file_type"],
        "download_count" => (int)$row["download_count"],
        "uploader_name"  => $row["uploader_name"],
        "download_url"   => contentFileExists($row["file_path"] ?? "")
            ? "../control/download_process.php?id=" . (int)$row["id"]
            : "",
        "file_available" => contentFileExists($row["file_path"] ?? "")
    );
}

$mydb->closeConn($conn);
echo json_encode(array("success" => true, "items" => $items, "count" => count($items)));
?>
