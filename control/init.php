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
        die("<div style='font-family:sans-serif;max-width:520px;margin:40px auto;padding:24px;border:1px solid #f87171;border-radius:12px;background:#1a1a2e;color:#fecaca;'>"
            . "<h2>Database Error</h2>"
            . "<p>Could not connect to <strong>isp_media</strong>. Please:</p>"
            . "<ol><li>Start MySQL in XAMPP</li><li>Import <code>database.sql</code> in phpMyAdmin</li>"
            . "<li>Check <code>model/database.php</code> credentials</li></ol></div>");
    }
    return $conn;
}
?>
