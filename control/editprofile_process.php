<?php
include_once '../model/mydb.php';
include_once 'auth.php';
include_once 'init.php';
include_once 'app.php';

appInit();

if(!isset($_SESSION["user_id"])){
    header("Location: ../view/login.php");
    exit();
}

$mydb = new MyDB();
$conn = requireDb($mydb);
tryRememberLogin($mydb, $conn);

$userResult = $mydb->getUserById($_SESSION["user_id"], $conn);
if($userResult->num_rows == 0){
    header("Location: ../control/logout_process.php");
    exit();
}

$user = $userResult->fetch_assoc();
$errors = array();
$success = "";

if(isset($_POST["update_profile"])){
    requireCsrfPost();
    $name  = sanitizeText($_POST["name"] ?? "", 100);
    $email = trim($_POST["email"] ?? "");
    $picture = $user["profile_picture"];

    if($name === ""){ $errors["name"] = "Name required"; }
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){ $errors["email"] = "Valid email required"; }
    elseif($mydb->emailExists($email, $conn, $_SESSION["user_id"])){ $errors["email"] = "Email already in use"; }

    if(!empty($_FILES["profile_picture"]["name"])){
        $ext = strtolower(pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION));
        if(!in_array($ext, array("jpg", "jpeg", "png", "gif", "webp"))){
            $errors["picture"] = "Image must be JPG, PNG, GIF or WEBP";
        } elseif($_FILES["profile_picture"]["size"] > 2 * 1024 * 1024){
            $errors["picture"] = "Image max 2MB";
        } else {
            ensureUploadDir(PROFILE_UPLOAD_DIR);
            $safe = time() . "_profile." . $ext;
            if(move_uploaded_file($_FILES["profile_picture"]["tmp_name"], PROFILE_UPLOAD_DIR . $safe)){
                if($picture !== "" && is_file(PROFILE_UPLOAD_DIR . $picture)){
                    unlink(PROFILE_UPLOAD_DIR . $picture);
                }
                $picture = $safe;
            } else {
                $errors["picture"] = "Upload failed";
            }
        }
    }

    if(empty($errors)){
        if($mydb->updateProfile($_SESSION["user_id"], $name, $email, $picture, $conn)){
            $_SESSION["name"] = $name;
            $user["name"] = $name;
            $user["email"] = $email;
            $user["profile_picture"] = $picture;
            $success = "Profile updated successfully.";
        } else {
            $errors["database"] = "Could not save profile. Please try again.";
        }
    }
}

if(isset($_POST["change_password"])){
    requireCsrfPost();
    $current = $_POST["current_password"] ?? "";
    $newPass = $_POST["new_password"] ?? "";
    $confirm = $_POST["confirm_password"] ?? "";

    if(!$mydb->verifyCurrentPassword($_SESSION["user_id"], $current, $conn)){
        $errors["current_password"] = "Current password is wrong";
    }
    if(strlen($newPass) < 8){ $errors["new_password"] = "New password must be 8+ characters"; }
    if($newPass !== $confirm){ $errors["confirm_password"] = "Passwords do not match"; }

    if(empty($errors)){
        $hash = password_hash($newPass, PASSWORD_DEFAULT);
        $mydb->updatePassword($_SESSION["user_id"], $hash, $conn);
        $success = "Password changed successfully.";
    }
}

$mydb->closeConn($conn);
?>
