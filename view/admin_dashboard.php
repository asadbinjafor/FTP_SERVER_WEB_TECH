<?php include '../control/admin_dashboard_process.php'; $navActive = "dashboard"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/task1_style.css">
</head>
<body>
<?php include 'nav_staff.php'; ?>
<div class="main-wrap">
    <h1 class="section-title">Admin Dashboard</h1>
    <div class="stat-grid">
        <div class="stat-card"><h3><?php echo $counts["contents"]; ?></h3><p>Total Contents</p></div>
        <div class="stat-card"><h3><?php echo $counts["categories"]; ?></h3><p>Categories</p></div>
        <div class="stat-card"><h3><?php echo $counts["moderators"]; ?></h3><p>Moderators</p></div>
        <div class="stat-card"><h3><?php echo $counts["requests"]; ?></h3><p>Client Requests (pending)</p></div>
    </div>
    <div class="dash-actions">
        <a class="dash-card" href="admin_staff.php">
            <span class="dash-card-title">Staff</span>
            <span class="dash-card-desc">Add Admin &amp; Moderator accounts</span>
            <span class="dash-card-btn btn-primary">Manage Staff</span>
        </a>
        <a class="dash-card" href="admin_contents.php">
            <span class="dash-card-title">Contents</span>
            <span class="dash-card-desc">Upload, edit, delete media</span>
            <span class="dash-card-btn btn-secondary">Manage Contents</span>
        </a>
        <a class="dash-card" href="admin_requests.php">
            <span class="dash-card-title">Client Requests <?php if($counts["requests"] > 0){ ?><span class="nav-badge"><?php echo $counts["requests"]; ?></span><?php } ?></span>
            <span class="dash-card-desc">Review content requests from clients</span>
            <span class="dash-card-btn btn-secondary">View Requests</span>
        </a>
    </div>
</div>
</body>
</html>
