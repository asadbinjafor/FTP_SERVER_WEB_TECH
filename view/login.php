<?php
include '../control/login_process.php';
include_once '../control/paths.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — <?php echo esc(SITE_NAME); ?></title>
    <base href="<?php echo esc(viewBase()); ?>">
    <link rel="stylesheet" href="../css/task1_style.css?v=4">
</head>
<body>
<?php $role = "guest"; include 'nav_guest.php'; ?>
<div class="main-wrap auth-wrap">
    <div class="card">
        <h2 class="section-title">Login</h2>
        <p style="color:var(--muted);margin-bottom:14px;font-size:0.9rem;">Client, Admin, or Moderator</p>
        <?php if(!empty($errors["login"])){ ?><div class="msg-error"><?php echo esc(trim($errors["login"])); ?></div><?php } ?>
        <?php if(!empty($errors["email"])){ ?><div class="msg-error"><?php echo esc($errors["email"]); ?></div><?php } ?>
        <form method="post">
            <?php echo csrfField(); ?>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
                <span class="error"><?php echo esc($errors["email"] ?? ""); ?></span>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="remember"> Remember Me</label>
            </div>
            <button type="submit" name="login" class="btn-primary">Login</button>
        </form>
        <p style="margin-top:16px;color:var(--muted);font-size:0.9rem;">New client? <a href="registration.php" style="color:var(--accent)">Register here</a></p>
    </div>
</div>
</body>
</html>
