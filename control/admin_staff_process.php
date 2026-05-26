<?php
include_once '../model/mydb.php';
include_once 'admin_gate.php';
include_once 'init.php';

requireAdmin();

$errors  = array();
$success = "";
$mydb    = new MyDB();
$conn    = requireDb($mydb);

if(isset($_POST["add_staff"])){
    requireCsrfPost();
    $name     = sanitizeText($_POST["name"] ?? "", 100);
    $email    = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm  = $_POST["confirm_password"] ?? "";
    $role     = $_POST["role"] ?? "";

    if($name === ""){ $errors["name"] = "Name required"; }
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){ $errors["email"] = "Valid email required"; }
    if(strlen($password) < 8){ $errors["password"] = "Password must be at least 8 characters"; }
    if($password !== $confirm){ $errors["confirm_password"] = "Passwords do not match"; }
    if(!in_array($role, array("admin", "moderator"))){ $errors["role"] = "Select Admin or Moderator"; }
    if($role === "admin" && (int)$_SESSION["user_id"] > 0){
        // only admin can add admin - already gated
    }

    if(empty($errors)){
        if($mydb->emailExists($email, $conn)){
            $errors["email"] = "Email already exists";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            if($mydb->registerUser($name, $email, $hash, $role, $conn)){
                $success = ucfirst($role) . " account created successfully.";
            } else {
                $errors["database"] = "Could not create account";
            }
        }
    }
}

$staff = $mydb->getAllStaff($conn);
$staffList = array();
while($row = $staff->fetch_assoc()){
    $staffList[] = $row;
}
$mydb->closeConn($conn);
?>
