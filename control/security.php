<?php
function initSecureSession(){
    if(session_status() === PHP_SESSION_ACTIVE){
        return;
    }
    $secure = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off");
    session_set_cookie_params(array(
        "lifetime" => 0,
        "path"     => "/",
        "httponly" => true,
        "samesite" => "Lax",
        "secure"   => $secure
    ));
    session_start();
    if(empty($_SESSION["_init"])){
        session_regenerate_id(true);
        $_SESSION["_init"] = 1;
    }
}

function sendSecurityHeaders(){
    header("X-Content-Type-Options: nosniff");
    header("X-Frame-Options: SAMEORIGIN");
    header("Referrer-Policy: strict-origin-when-cross-origin");
}

function csrfToken(){
    initSecureSession();
    if(empty($_SESSION["csrf_token"])){
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
    }
    return $_SESSION["csrf_token"];
}

function csrfField(){
    return '<input type="hidden" name="csrf_token" value="' .
        htmlspecialchars(csrfToken(), ENT_QUOTES, "UTF-8") . '">';
}

function verifyCsrf($token){
    initSecureSession();
    return is_string($token) && $token !== "" &&
        !empty($_SESSION["csrf_token"]) &&
        hash_equals($_SESSION["csrf_token"], $token);
}

function requireCsrfPost(){
    if(!verifyCsrf($_POST["csrf_token"] ?? "")){
        http_response_code(403);
        die("Invalid security token. Refresh and try again.");
    }
}

function esc($v){
    return htmlspecialchars((string)$v, ENT_QUOTES, "UTF-8");
}

function sanitizeText($v, $max = 500){
    $v = trim((string)$v);
    return strlen($v) > $max ? substr($v, 0, $max) : $v;
}

function memberSessionId(){
    initSecureSession();
    if(empty($_SESSION["member_sid"])){
        $_SESSION["member_sid"] = bin2hex(random_bytes(16));
    }
    return $_SESSION["member_sid"];
}

function requesterIp(){
    return $_SERVER["REMOTE_ADDR"] ?? "0.0.0.0";
}
?>
