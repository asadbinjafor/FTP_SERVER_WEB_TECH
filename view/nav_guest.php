<?php
include_once dirname(__DIR__) . "/control/auth.php";
include_once dirname(__DIR__) . "/control/app.php";
if(!function_exists("esc")){
    include_once dirname(__DIR__) . "/control/security.php";
}
$navRole = isset($role) ? $role : getSessionRole();
if(isset($_SESSION["user_id"]) && $navRole === "guest"){
    $navRole = "client";
}
?>
<nav class="site-nav" id="siteNav">
    <div class="nav-inner">
        <a class="nav-brand" href="Home.php"><?php echo esc(SITE_NAME); ?></a>
        <div class="nav-actions" role="navigation" aria-label="Main navigation">
            <a class="nav-item" href="Home.php">Browse</a>
            <?php if($navRole === "admin"){ ?>
                <a class="nav-item" href="admin_dashboard.php">Admin Panel</a>
            <?php } elseif($navRole === "moderator"){ ?>
                <a class="nav-item" href="moderator_dashboard.php">Moderator Panel</a>
            <?php } elseif($navRole === "client"){ ?>
                <a class="nav-item" href="Home.php#requestSection">Request Content</a>
                <a class="nav-item" href="client_requests.php">My Requests</a>
                <a class="nav-item" href="editprofile.php">My Profile</a>
            <?php } ?>

            <?php if(in_array($navRole, array("admin", "moderator", "client"), true)){ ?>
                <?php if($navRole !== "client"){ ?>
                    <a class="nav-item" href="editprofile.php">Profile</a>
                <?php } ?>
                <span class="nav-user"><?php echo esc($_SESSION["name"] ?? ""); ?></span>
                <form class="nav-logout-form" action="../control/logout_process.php" method="get">
                    <button type="submit" class="nav-item nav-cta nav-btn">Logout</button>
                </form>
            <?php } else { ?>
                <form class="nav-inline-form" action="registration.php" method="get">
                    <button type="submit" class="nav-item nav-outline nav-btn">Client Register</button>
                </form>
                <form class="nav-inline-form" action="login.php" method="get">
                    <button type="submit" class="nav-item nav-cta nav-btn">Login</button>
                </form>
            <?php } ?>
        </div>
    </div>
</nav>
