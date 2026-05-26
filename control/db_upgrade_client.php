<?php
/**
 * Run once: http://localhost/WTProject_04/control/db_upgrade_client.php
 * Adds user_id to content_requests and client role support.
 */
include_once __DIR__ . "/../model/database.php";

header("Content-Type: text/html; charset=UTF-8");

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if($conn->connect_error){
    die("DB connect failed: " . htmlspecialchars($conn->connect_error));
}

$steps = array();

$col = $conn->query("SHOW COLUMNS FROM content_requests LIKE 'user_id'");
if($col && $col->num_rows === 0){
    if($conn->query("ALTER TABLE content_requests ADD COLUMN user_id INT NULL AFTER id")){
        $steps[] = "Added content_requests.user_id";
    } else {
        $steps[] = "Failed user_id: " . $conn->error;
    }
} else {
    $steps[] = "content_requests.user_id already exists";
}

$fk = $conn->query("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = '" . $conn->real_escape_string(DB_NAME) . "'
    AND TABLE_NAME = 'content_requests' AND COLUMN_NAME = 'user_id' AND REFERENCED_TABLE_NAME = 'users'");
if($fk && $fk->num_rows === 0){
    @$conn->query("ALTER TABLE content_requests ADD CONSTRAINT fk_cr_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL");
    $steps[] = $conn->error ? "FK skip or exists: " . $conn->error : "Added foreign key fk_cr_user";
} else {
    $steps[] = "Foreign key on user_id OK or skipped";
}

if($conn->query("ALTER TABLE users MODIFY role ENUM('admin','moderator','client') NOT NULL")){
    $steps[] = "Updated users.role enum (admin, moderator, client)";
} else {
    $steps[] = "Role enum: " . $conn->error;
}

$conn->close();
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>DB Upgrade</title></head>
<body style="font-family:sans-serif;max-width:560px;margin:40px auto;padding:20px;">
<h2>Database upgrade complete</h2>
<ul>
<?php foreach($steps as $s){ echo "<li>" . htmlspecialchars($s) . "</li>"; } ?>
</ul>
<p><a href="../view/admin_requests.php">Open Admin Requests</a> | <a href="../view/Home.php">Home</a></p>
</body></html>
