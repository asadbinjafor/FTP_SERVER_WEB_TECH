<?php
include_once '../model/mydb.php';
include_once 'app.php';
include_once 'moderator_gate.php';
include_once 'init.php';

requireModerator();
ensureUploadDir(CONTENT_UPLOAD_DIR);

$mydb = new MyDB();
$conn = requireDb($mydb);
$categoryList = array();
$catRes = $mydb->getAllCategoriesFlat($conn);
while($row = $catRes->fetch_assoc()){
    $categoryList[] = $row;
}
$errors = array();

if(isset($_POST["save"])){
    requireCsrfPost();
    $title = sanitizeText($_POST["title"] ?? "", 200);
    $desc  = sanitizeText($_POST["description"] ?? "", 2000);
    $catId = (int)($_POST["category_id"] ?? 0);

    if($title === ""){ $errors["title"] = "Title required"; }
    if($catId <= 0){ $errors["category_id"] = "Category required"; }

    $path = "";
    $ftype = "";
    if(!empty($_FILES["content_file"]["name"])){
        $ext = strtolower(pathinfo($_FILES["content_file"]["name"], PATHINFO_EXTENSION));
        if(!in_array($ext, ALLOWED_CONTENT_EXT)){
            $errors["file"] = "File type not allowed";
        } elseif($_FILES["content_file"]["size"] > MAX_CONTENT_SIZE){
            $errors["file"] = "File too large";
        } else {
            $safe = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "", $_FILES["content_file"]["name"]);
            if(move_uploaded_file($_FILES["content_file"]["tmp_name"], CONTENT_UPLOAD_DIR . $safe)){
                $path = $safe;
                $ftype = $ext;
            } else {
                $errors["file"] = "Upload failed";
            }
        }
    } else {
        $errors["file"] = "File required";
    }

    if(empty($errors)){
        $mydb->insertContent($title, $desc, $path, $ftype, $catId, $_SESSION["user_id"], $conn);
        $mydb->closeConn($conn);
        header("Location: ../view/moderator_contents.php?uploaded=1");
        exit();
    }
}
$mydb->closeConn($conn);
?>
