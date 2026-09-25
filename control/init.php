<?php
include_once __DIR__ . "/security.php";

function appInit($headers = true){
    initSecureSession();
    if($headers){
        sendSecurityHeaders();
    }
}

function requireDb($mydb){
    $conn = $mydb->createConn();
    if($conn === false){
        http_response_code(503);
        die('Database unavailable. Check PostgreSQL environment variables and schema.');
    }
    return $conn;
}
?>
