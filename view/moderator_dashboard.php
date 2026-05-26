<?php include '../control/moderator_dashboard_process.php'; $navActive = "dashboard"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Moderator Dashboard</title>
    <link rel="stylesheet" href="../css/task1_style.css">
</head>
<body>
<?php include 'nav_staff.php'; ?>
<div class="main-wrap">
    <h1 class="section-title">Moderator Dashboard</h1>
    <p class="page-sub">Manage content and review client requests.</p>
    <div class="stat-grid">
        <div class="stat-card"><h3><?php echo $contentN; ?></h3><p>Total Contents</p></div>
        <div class="stat-card"><h3><?php echo $pending; ?></h3><p>Pending Client Requests</p></div>
    </div>
    <div class="dash-actions">
        <a class="dash-card" href="moderator_contents.php">
            <span class="dash-card-title">Contents</span>
            <span class="dash-card-desc">Add, view, delete media files</span>
            <span class="dash-card-btn btn-primary">Open Contents</span>
        </a>
        <a class="dash-card" href="moderator_requests.php">
            <span class="dash-card-title">Client Requests <?php if($pending > 0){ ?><span class="nav-badge"><?php echo $pending; ?></span><?php } ?></span>
            <span class="dash-card-desc">Review requests submitted by clients</span>
            <span class="dash-card-btn btn-secondary">Open Requests</span>
        </a>
    </div>
</div>
</body>
</html>
