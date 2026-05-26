<?php include '../control/moderator_requests_process.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Requests — Moderator</title><link rel="stylesheet" href="../css/task1_style.css"></head>
<body>
<?php $navActive = "requests"; include 'nav_staff.php'; ?>
<div class="main-wrap">
    <h1 class="section-title">Content Requests</h1>
    <input type="hidden" id="csrfReq" value="<?php echo esc(csrfToken()); ?>">
    <table class="data-table">
        <thead><tr><th>Title</th><th>Category</th><th>Message</th><th>Status</th><th>Action</th></tr></thead>
        <tbody>
        <?php while($r = $requests->fetch_assoc()){ ?>
        <tr>
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
function updateReq(id,s){
    var x=new XMLHttpRequest();
    x.open("POST","../control/request_status_api.php",true);
    x.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
    x.onload=function(){location.reload();};
    x.send("request_id="+id+"&status="+s+"&csrf_token="+encodeURIComponent(document.getElementById("csrfReq").value));
}
</script>
</body>
</html>
