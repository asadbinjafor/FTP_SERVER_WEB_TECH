<?php
include '../control/registration_process.php';
include_once '../control/paths.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Registration — <?php echo esc(SITE_NAME); ?></title>
    <base href="<?php echo esc(viewBase()); ?>">
    <link rel="stylesheet" href="../css/task1_style.css?v=4">
</head>
<body>
<?php $role = "guest"; include 'nav_guest.php'; ?>
<div class="main-wrap auth-wrap">
    <div class="card">
        <h2 class="section-title">Client Registration</h2>
        <p style="color:var(--muted);margin-bottom:16px;font-size:0.9rem;">Create a client account to request new content. Admin and Moderator accounts are created by Admin only.</p>
        <?php if(!empty($success)){ ?><div class="msg-success"><?php echo esc($success); ?> <a href="login.php">Login now</a></div><?php } ?>
        <?php if(!empty($errors["database"])){ ?><div class="msg-error"><?php echo esc($errors["database"]); ?></div><?php } ?>
        <form method="post" onsubmit="return validateReg()">
            <?php echo csrfField(); ?>
            <div class="form-group"><label>Full Name</label><input type="text" name="name" required><span class="error"><?php echo esc($errors["name"] ?? ""); ?></span></div>
            <div class="form-group"><label>Email</label><input type="email" name="email" required><span class="error"><?php echo esc($errors["email"] ?? ""); ?></span></div>
            <div class="form-group"><label>Password (8+ chars)</label><input type="password" name="password" id="regPass" required><span class="error"><?php echo esc($errors["password"] ?? ""); ?></span></div>
            <div class="form-group"><label>Confirm Password</label><input type="password" name="confirm_password" id="regConfirm" required><span class="error"><?php echo esc($errors["confirm_password"] ?? ""); ?></span></div>
            <button type="submit" name="register" class="btn-primary">Register as Client</button>
        </form>
        <p style="margin-top:16px;color:var(--muted);font-size:0.9rem;">Already have an account? <a href="login.php" style="color:var(--accent)">Login</a></p>
    </div>
</div>
<script src="../js/task1_script.js"></script>
</body>
</html>
