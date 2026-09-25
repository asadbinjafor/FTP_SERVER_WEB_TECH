<?php
include_once '../model/mydb.php';
include_once 'security.php';
include_once 'app.php';

initSecureSession();

$id = (int)($_GET["id"] ?? 0);
if($id <= 0){
    http_response_code(404);
    die("Invalid file");
}

$mydb = new MyDB();
$conn = $mydb->createConn();
$result = $mydb->getContentById($id, $conn);

if($result->num_rows == 0){
    $mydb->closeConn($conn);
    http_response_code(404);
    die("Content not found");
}

$row = $result->fetch_assoc();
$mydb->incrementDownload($id, $conn);
$mydb->closeConn($conn);

$file = storedFilePath('contents', $row["file_path"]);
if(!$file || !is_file($file)){
    http_response_code(404);
    header("Content-Type: text/html; charset=UTF-8");
    echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>File Not Found</title>";
    echo "<style>body{font-family:sans-serif;max-width:520px;margin:40px auto;padding:24px;background:#111827;color:#e5e7eb;}";
    echo "a{color:#38bdf8;}</style></head><body>";
    echo "<h2>File not found on server</h2>";
    echo "<p>The database has a record for <strong>" . htmlspecialchars($row["title"]) . "</strong>, ";
    echo "but the uploaded file (<code>" . htmlspecialchars(basename($row["file_path"])) . "</code>) is missing from <code>uploads/contents/</code>.</p>";
    echo "<p>Please ask Admin/Moderator to upload this content again.</p>";
    echo "<p><a href='../view/Home.php'>&larr; Back to Home</a></p></body></html>";
    exit();
}

$mime = mime_content_type($file);
if(!$mime){
    $mime = "application/octet-stream";
}

header("Content-Type: " . $mime);
header("Content-Disposition: attachment; filename=\"" . basename($row["file_path"]) . "\"");
header("Content-Length: " . filesize($file));
readfile($file);
if ($file !== contentFilePath($row["file_path"])) { unlink($file); }
exit();
?>
