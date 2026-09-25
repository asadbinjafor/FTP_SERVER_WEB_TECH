<?php
include_once '../model/mydb.php';
include_once 'app.php';
include_once 'admin_gate.php';
include_once 'init.php';

requireAdmin();
ensureUploadDir(CONTENT_UPLOAD_DIR);

$contentId = (int)($_GET["id"] ?? $_POST["content_id"] ?? 0);
$errors = array();

$mydb = new MyDB();
$conn = requireDb($mydb);

if($contentId <= 0){
    header("Location: ../view/admin_contents.php");
    exit();
}

$res = $mydb->getContentById($contentId, $conn);
if($res->num_rows == 0){
    header("Location: ../view/admin_contents.php");
    exit();
}
$content = $res->fetch_assoc();

$categoryList = array();
$catRes = $mydb->getAllCategoriesFlat($conn);
while($row = $catRes->fetch_assoc()){
    $categoryList[] = $row;
}

if(isset($_POST["save"])){
    requireCsrfPost();
    $title = sanitizeText($_POST["title"] ?? "", 200);
    $desc  = sanitizeText($_POST["description"] ?? "", 2000);
    $catId = (int)($_POST["category_id"] ?? 0);
    $path  = "";
    $ftype = $content["file_type"];

    if($title === ""){ $errors["title"] = "Title required"; }
    if($catId <= 0){ $errors["category_id"] = "Category required"; }

    if(!empty($_FILES["content_file"]["name"])){
        $ext = strtolower(pathinfo($_FILES["content_file"]["name"], PATHINFO_EXTENSION));
        if(!in_array($ext, ALLOWED_CONTENT_EXT)){
            $errors["file"] = "File type not allowed";
        } elseif($_FILES["content_file"]["size"] > MAX_CONTENT_SIZE){
            $errors["file"] = "File too large";
        } else {
            $safe = time() . "_" . bin2hex(random_bytes(4)) . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "", $_FILES["content_file"]["name"]);
            if(saveUploadedFile($_FILES["content_file"], 'contents', $safe)){
                $mydb->deleteContentFile($content["file_path"]);
                $path = $safe;
                $ftype = $ext;
            } else {
                $errors["file"] = "Upload failed";
            }
        }
    }

    if(empty($errors)){
        $mydb->updateContent($contentId, $title, $desc, $path, $ftype, $catId, $conn);
        $mydb->closeConn($conn);
        header("Location: ../view/admin_contents.php?updated=1");
        exit();
    }
    $content["title"] = $title;
    $content["description"] = $desc;
    $content["category_id"] = $catId;
}
$mydb->closeConn($conn);
?>
