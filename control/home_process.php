<?php
include_once '../model/mydb.php';
include_once 'auth.php';
include_once 'init.php';
include_once 'app.php';

appInit();

$mydb = new MyDB();
$conn = requireDb($mydb);
tryRememberLogin($mydb, $conn);
if(isset($_SESSION["user_id"])){
    refreshSessionUser($mydb, $conn);
}

$role = getSessionRole();
$name = $_SESSION["name"] ?? "";

$topCategories = array();
$catResult = $mydb->getTopCategories($conn);
while($row = $catResult->fetch_assoc()){
    $topCategories[] = $row;
}

$highlighted = $mydb->getHighlightedContents(6, $conn);

$activeCategory = (int)($_GET["category"] ?? 0);
$activeSub      = (int)($_GET["sub"] ?? 0);
$fileTypeFilter = trim($_GET["file_type"] ?? "");
$searchQ        = trim($_GET["q"] ?? "");

$subcategories = array();
if($activeCategory > 0){
    $subRes = $mydb->getSubcategories($activeCategory, $conn);
    while($s = $subRes->fetch_assoc()){
        $subcategories[] = $s;
    }
}

if($searchQ !== ""){
    $contents = $mydb->searchContents($searchQ, $activeCategory, $activeSub, $fileTypeFilter, $conn);
} elseif($activeCategory > 0){
    $contents = $mydb->getContentsByCategory($activeCategory, $activeSub, $fileTypeFilter, $conn);
} else {
    $contents = null;
}

$categoryOptions = array();
if($role === "client"){
    $flatRes = $mydb->getAllCategoriesFlat($conn);
    while($row = $flatRes->fetch_assoc()){
        $categoryOptions[] = $row;
    }
}

$mydb->closeConn($conn);
?>
