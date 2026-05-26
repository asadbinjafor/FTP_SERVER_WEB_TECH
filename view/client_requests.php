<?php include '../control/client_requests_process.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>My Requests</title><link rel="stylesheet" href="../css/task1_style.css"></head>
<body>
<?php $role = "client"; include 'nav_guest.php'; ?>
<div class="main-wrap">
    <h1 class="section-title">My Content Requests</h1>
    <p style="margin-bottom:16px;display:flex;gap:10px;flex-wrap:wrap;">
        <a class="btn-primary" href="Home.php#requestSection">Submit New Request</a>
        <a class="btn-secondary" href="editprofile.php">Update Profile</a>
    </p>
    <table class="data-table">
        <thead><tr><th>Title</th><th>Category</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
        <?php if($requests->num_rows == 0){ ?>
            <tr><td colspan="4">No requests yet.</td></tr>
        <?php } ?>
        <?php while($r = $requests->fetch_assoc()){ ?>
            <tr>
                <td><?php echo esc($r["content_title"]); ?></td>
                <td><?php echo esc($r["category_requested"]); ?></td>
                <td><span class="badge badge-<?php echo esc($r["status"]); ?>"><?php echo esc(ucfirst($r["status"])); ?></span></td>
                <td><?php echo date("d M Y", strtotime($r["created_at"])); ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
