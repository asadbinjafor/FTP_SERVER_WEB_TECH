<?php
require_once __DIR__ . '/storage.php';
define("ROOT_DIR", dirname(__DIR__));
define("SITE_NAME", 'A$AD FTP');
define("PROFILE_UPLOAD_DIR", ROOT_DIR . "/uploads/profile/");
define("PROFILE_UPLOAD_WEB", "../uploads/profile/");
define("CONTENT_UPLOAD_DIR", ROOT_DIR . "/uploads/contents/");
define("CONTENT_UPLOAD_WEB", "../uploads/contents/");
define("REMEMBER_SECRET", getenv('REMEMBER_SECRET') ?: '');

define("ALLOWED_CONTENT_EXT", array("mp4", "mkv", "avi", "pdf", "zip", "rar", "exe", "msi", "mp3", "png", "jpg"));
define("MAX_CONTENT_SIZE", 50 * 1024 * 1024);

function ensureUploadDir($dir){
    if(!is_dir($dir)){
        mkdir($dir, 0755, true);
    }
}

function contentFilePath($storedName){
    return CONTENT_UPLOAD_DIR . basename((string)$storedName);
}

function contentFileExists($storedName){
    $path = contentFilePath($storedName);
    return $storedName !== "" && (is_file($path) || storageConfigured());
}
?>
