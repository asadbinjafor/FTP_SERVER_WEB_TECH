<?php include '../control/admin_requests_process.php'; $navActive = "requests"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Content Requests — Admin</title>
    <link rel="stylesheet" href="../css/task1_style.css">
</head>
<body>
<?php include 'nav_staff.php'; ?>
<div class="main-wrap">
    <h1 class="section-title">Client Content Requests</h1>
    <input type="hidden" id="csrfReq" value="<?php echo esc(csrfToken()); ?>">
    <table class="data-table">
        <thead><tr><th>ID</th><th>Client</th><th>Title</th><th>Category</th><th>Message</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php while($r = $requests->fetch_assoc()){ ?>
            <tr>
                <td><?php echo (int)$r["id"]; ?></td>
                <td><?php echo esc($r["client_name"] ?? "—"); ?><br><small style="color:var(--muted)"><?php echo esc($r["client_email"] ?? ""); ?></small></td>
                <td><?php echo esc($r["content_title"]); ?></td>
                <td><?php echo esc($r["category_requested"]); ?></td>
                <td><?php echo esc($r["message"]); ?></td>
                <td><span class="badge badge-<?php echo esc($r["status"]); ?>"><?php echo esc($r["status"]); ?></span></td>
                <td>
                    <button class="btn-secondary" onclick="updateReq(<?php echo (int)$r['id']; ?>,'fulfilled')">Fulfilled</button>
                    <button class="btn-danger" onclick="updateReq(<?php echo (int)$r['id']; ?>,'rejected')">Reject</button>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<script>
function updateReq(id, status){
    var x=new XMLHttpRequest();
    x.open("POST","../control/request_status_api.php",true);
    x.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
    x.onreadystatechange=function(){ if(x.readyState===4 && x.status===200){ location.reload(); }};
    x.send("request_id="+id+"&status="+status+"&csrf_token="+encodeURIComponent(document.getElementById("csrfReq").value));
}
</script>
</body>
</html>
