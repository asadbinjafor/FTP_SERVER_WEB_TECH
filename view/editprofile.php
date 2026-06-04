<?php include '../control/editprofile_process.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile — <?php echo esc(SITE_NAME); ?></title>
    <link rel="stylesheet" href="../css/task1_style.css">
</head>
<body>
<?php
if($_SESSION["role"] === "client"){
    $role = "client";
    include 'nav_guest.php';
} else {
    include 'nav_staff.php';
}
?>
<div class="main-wrap">
    <h1 class="section-title"><?php echo $_SESSION["role"] === "client" ? "My Profile" : "Edit Profile"; ?></h1>
    <?php if($_SESSION["role"] === "client"){ ?>
        <p class="page-sub">Update your name, email, or photo. You can also <a href="Home.php#requestSection">request new content</a> from the home page.</p>
        <p style="margin-bottom:16px;">
            <a class="btn-secondary" href="Home.php#requestSection">Request New Content</a>
            <a class="btn-secondary" href="client_requests.php">View My Requests</a>
        </p>
    <?php } ?>
    <?php if($success){ ?><div class="msg-success"><?php echo esc($success); ?></div><?php } ?>
    <?php if(!empty($errors["database"])){ ?><div class="msg-error"><?php echo esc($errors["database"]); ?></div><?php } ?>

    <div class="card">
        <h2 class="section-title">Profile Information</h2>
        <form method="post" enctype="multipart/form-data" onsubmit="return validateProfileForm()">
            <?php echo csrfField(); ?>
            <div class="profile-layout" style="display:flex;gap:20px;flex-wrap:wrap;margin-bottom:16px;">
                <?php if(!empty($user["profile_picture"])){ ?>
                    <img src="<?php echo PROFILE_UPLOAD_WEB . esc($user["profile_picture"]); ?>" alt="Profile" style="width:90px;height:90px;border-radius:50%;object-fit:cover;border:2px solid var(--border);">
                <?php } else { ?>
                    <div style="width:90px;height:90px;border-radius:50%;background:var(--surface2);display:flex;align-items:center;justify-content:center;font-size:2rem;color:var(--muted);"><?php echo esc(strtoupper(substr($user["name"], 0, 1))); ?></div>
                <?php } ?>
                <div style="flex:1;">
                    <div class="form-group"><label>Name</label><input type="text" name="name" value="<?php echo esc($user["name"]); ?>" required><span class="error"><?php echo esc($errors["name"] ?? ""); ?></span></div>
                    <div class="form-group"><label>Email</label><input type="email" name="email" value="<?php echo esc($user["email"]); ?>" required><span class="error"><?php echo esc($errors["email"] ?? ""); ?></span></div>
                    <div class="form-group"><label>Profile Picture</label><input type="file" name="profile_picture" accept="image/*"></div>
                    <span class="error"><?php echo esc($errors["picture"] ?? ""); ?></span>
                </div>
            </div>
            <button type="submit" name="update_profile" class="btn-primary">Save Profile</button>
        </form>
    </div>

    <div class="card">
        <h2 class="section-title">Change Password</h2>
        <form method="post" onsubmit="return validatePasswordForm()">
            <?php echo csrfField(); ?>
            <div class="form-group"><label>Current Password</label><input type="password" name="current_password" id="current_password" required></div>
            <span class="error"><?php echo esc($errors["current_password"] ?? ""); ?></span>
            <div class="form-group"><label>New Password</label><input type="password" name="new_password" id="new_password" required></div>
            <div class="form-group"><label>Confirm Password</label><input type="password" name="confirm_password" id="confirm_password" required></div>
            <span class="error"><?php echo esc($errors["new_password"] ?? $errors["confirm_password"] ?? ""); ?></span>
            <button type="submit" name="change_password" class="btn-primary">Change Password</button>
        </form>
    </div>
</div>
<script src="../js/task1_script.js"></script>
</body>
</html>
