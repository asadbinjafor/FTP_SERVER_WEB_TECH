<?php
include_once '../model/mydb.php';
include_once 'auth.php';
include_once 'init.php';

appInit();

if(isset($_SESSION["user_id"])){
    header("Location: ../view/Home.php");
    exit();
}

$errors = array();
if(isset($_POST["login"])){
    requireCsrfPost();
    $email    = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors["email"] = "Valid email required";
    }
    if($password === ""){
        $errors["password"] = "Password required";
    }

    if(empty($errors)){
        $mydb = new MyDB();
        $conn = requireDb($mydb);
        $result = $mydb->getUserByEmail($email, $conn);
        if($result->num_rows == 0){
            $errors["login"] = "Invalid email or password";
        } else {
            $user = $result->fetch_assoc();
            if(!password_verify($password, $user["password_hash"])){
                $errors["login"] = "Invalid email or password";
                $user = null;
            }
        }
        if(isset($user) && $user){
            setLoginSession($user);
            if(isset($_POST["remember"])){
                setRememberCookie($user["id"]);
            }
            $mydb->closeConn($conn);
            if($user["role"] === "admin"){
                header("Location: ../view/admin_dashboard.php");
            } elseif($user["role"] === "moderator"){
                header("Location: ../view/moderator_dashboard.php");
            } else {
                header("Location: ../view/Home.php");
            }
            exit();
        }
        if(isset($conn)){
            $mydb->closeConn($conn);
        }
    }
}
?>
