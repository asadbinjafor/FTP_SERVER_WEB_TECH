<?php
include_once __DIR__ . "/app.php";

function setLoginSession($user){
    $_SESSION["user_id"] = (int)$user["id"];
    $_SESSION["name"]    = $user["name"];
    $_SESSION["role"]    = trim((string)($user["role"] ?? "client"));
    if($_SESSION["role"] === ""){
        $_SESSION["role"] = "client";
    }
}

function refreshSessionUser($mydb, $conn){
    if(!isset($_SESSION["user_id"])){
        return;
    }
    $result = $mydb->getUserById((int)$_SESSION["user_id"], $conn);
    if($result->num_rows > 0){
        setLoginSession($result->fetch_assoc());
    }
}

function getSessionRole(){
    if(!isset($_SESSION["user_id"])){
        return "guest";
    }
    $role = trim((string)($_SESSION["role"] ?? ""));
    if(in_array($role, array("admin", "moderator", "client"), true)){
        return $role;
    }
    return "client";
}

function setRememberCookie($userId){
    if (REMEMBER_SECRET === '') { return; }
    $expires = time() + 604800;
    $value = $userId . ':' . $expires;
    $token = hash_hmac('sha256', $value, REMEMBER_SECRET);
    setcookie("remember_me", $value . ':' . $token, array(
        "expires"  => $expires,
        "path"     => "/",
        "httponly" => true,
        "secure" => requestIsHttps(),
        "samesite" => "Strict"
    ));
}

function clearRememberCookie(){
    setcookie("remember_me", "", array("expires" => time() - 3600, "path" => "/", "httponly" => true, "secure" => requestIsHttps(), "samesite" => "Strict"));
}

function tryRememberLogin($mydb, $conn){
    if (REMEMBER_SECRET === '') { return; }
    if(isset($_SESSION["user_id"]) || !isset($_COOKIE["remember_me"])){
        return;
    }
    $parts = explode(":", $_COOKIE["remember_me"]);
    if(count($parts) !== 3 || !ctype_digit($parts[0]) || !ctype_digit($parts[1]) || (int)$parts[1] < time()){
        return;
    }
    $uid  = (int)$parts[0];
    $exp  = hash_hmac('sha256', $parts[0] . ':' . $parts[1], REMEMBER_SECRET);
    if(!hash_equals($exp, $parts[2])){
        return;
    }
    $r = $mydb->getUserById($uid, $conn);
    if($r->num_rows > 0){
        session_regenerate_id(true);
        setLoginSession($r->fetch_assoc());
    }
}
?>
