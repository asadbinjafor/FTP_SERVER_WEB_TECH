<?php include '../control/admin_staff_process.php'; $navActive = "staff"; ?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Manage Staff — Admin</title><link rel="stylesheet" href="../css/task1_style.css"></head>
<body>
<?php include 'nav_staff.php'; ?>
<div class="main-wrap">
    <h1 class="section-title">Add Admin &amp; Moderator</h1>
    <p class="page-sub">Only Admin can create staff accounts. Clients register themselves from the public page.</p>

    <?php if($success){ ?><div class="msg-success"><?php echo esc($success); ?></div><?php } ?>
    <?php foreach($errors as $e){ if($e){ ?><div class="msg-error"><?php echo esc($e); ?></div><?php } } ?>

    <div class="card" style="margin-bottom:24px;">
        <h2 class="section-title">Create Staff Account</h2>
        <form method="post" onsubmit="return validateReg()">
            <?php echo csrfField(); ?>
            <div class="form-group"><label>Name</label><input type="text" name="name" required></div>
            <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
            <div class="form-group"><label>Password</label><input type="password" name="password" id="regPass" required></div>
            <div class="form-group"><label>Confirm Password</label><input type="password" name="confirm_password" id="regConfirm" required></div>
            <div class="form-group">
                <label>Role</label>
                <select name="role" required>
                    <option value="">Select role</option>
                    <option value="admin">Admin</option>
                    <option value="moderator">Moderator</option>
                </select>
            </div>
            <button type="submit" name="add_staff" class="btn-primary">Create Staff Account</button>
        </form>
    </div>

    <h2 class="section-title">Staff List</h2>
    <input type="hidden" id="csrfStaff" value="<?php echo esc(csrfToken()); ?>">
    <table class="data-table">
        <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Joined</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach($staffList as $s){
            if((int)$s["id"] === (int)$_SESSION["user_id"]){ continue; }
        ?>
            <tr>
                <td><?php echo esc($s["name"]); ?></td>
                <td><?php echo esc($s["email"]); ?></td>
                <td><?php echo esc(ucfirst($s["role"])); ?></td>
                <td><?php echo esc($s["created_at"]); ?></td>
                <td>
                    <?php if($s["role"] === "moderator"){ ?>
                        <button type="button" class="btn-danger" onclick="deleteStaff(<?php echo (int)$s['id']; ?>,'moderator')">Delete</button>
                    <?php } elseif($s["role"] === "admin"){ ?>
                        <button type="button" class="btn-danger" onclick="deleteStaff(<?php echo (int)$s['id']; ?>,'admin')">Delete</button>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<script src="../js/task1_script.js"></script>
<script>
function deleteStaff(id, role){
    if(!confirm("Delete this " + role + " account?")){ return; }
    var x = new XMLHttpRequest();
    x.open("POST", "../control/staff_delete_api.php", true);
    x.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    x.onload = function(){ location.reload(); };
    x.send("staff_id=" + id + "&role=" + encodeURIComponent(role) + "&csrf_token=" + encodeURIComponent(document.getElementById("csrfStaff").value));
}
</script>
</body>
</html>
