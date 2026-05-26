<?php
include_once '../model/mydb.php';
include_once 'init.php';
include_once 'app.php';

appInit();

if(isset($_SESSION["user_id"])){
    header("Location: ../view/Home.php");
    exit();
}

$errors  = array();
$success = "";

if(isset($_POST["register"])){
    requireCsrfPost();
    $name     = sanitizeText($_POST["name"] ?? "", 100);
    $email    = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm  = $_POST["confirm_password"] ?? "";

    if($name === ""){ $errors["name"] = "Name required"; }
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){ $errors["email"] = "Valid email required"; }
    if(strlen($password) < 8){ $errors["password"] = "Password must be at least 8 characters"; }
    if($password !== $confirm){ $errors["confirm_password"] = "Passwords do not match"; }

    if(empty($errors)){
        $mydb = new MyDB();
        $conn = requireDb($mydb);
        if($mydb->emailExists($email, $conn)){
            $errors["email"] = "Email already registered";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            if($mydb->registerUser($name, $email, $hash, "client", $conn)){
                $success = "Client account created. Please login.";
            } else {
                $errors["database"] = "Registration failed. Run database_upgrade_client.sql if needed.";
            }
        }
        $mydb->closeConn($conn);
    }
}
?>
