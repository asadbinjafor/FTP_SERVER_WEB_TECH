<?php
include_once dirname(__DIR__) . "/control/app.php";
include_once dirname(__DIR__) . "/control/security.php";
$staffRole  = $_SESSION["role"] ?? "";
$navActive  = $navActive ?? "";
$pendingNav = 0;

if(in_array($staffRole, array("admin", "moderator"))){
    include_once dirname(__DIR__) . "/model/mydb.php";
    $mydbTmp = new MyDB();
    $connTmp = $mydbTmp->createConn();
    if($connTmp){
        $pendingNav = $mydbTmp->getPendingRequestCount($connTmp);
        $mydbTmp->closeConn($connTmp);
    }
}
?>
<nav class="site-nav">
    <div class="nav-inner">
        <a class="nav-brand" href="Home.php"><?php echo esc(SITE_NAME); ?></a>
        <div class="nav-menu">
            <a class="nav-item" href="Home.php">Public Browse</a>
            <?php if($staffRole === "admin"){ ?>
                <a class="nav-item <?php echo $navActive === 'dashboard' ? 'nav-active' : ''; ?>" href="admin_dashboard.php">Dashboard</a>
                <a class="nav-item <?php echo $navActive === 'staff' ? 'nav-active' : ''; ?>" href="admin_staff.php">Staff</a>
                <a class="nav-item <?php echo $navActive === 'contents' ? 'nav-active' : ''; ?>" href="admin_contents.php">Contents</a>
                <a class="nav-item <?php echo $navActive === 'requests' ? 'nav-active' : ''; ?>" href="admin_requests.php">
                    Requests<?php if($pendingNav > 0){ ?> <span class="nav-badge"><?php echo (int)$pendingNav; ?></span><?php } ?>
                </a>
            <?php } else { ?>
                <a class="nav-item <?php echo $navActive === 'dashboard' ? 'nav-active' : ''; ?>" href="moderator_dashboard.php">Dashboard</a>
                <a class="nav-item <?php echo $navActive === 'contents' ? 'nav-active' : ''; ?>" href="moderator_contents.php">Contents</a>
                <a class="nav-item <?php echo $navActive === 'requests' ? 'nav-active' : ''; ?>" href="moderator_requests.php">
                    Requests<?php if($pendingNav > 0){ ?> <span class="nav-badge"><?php echo (int)$pendingNav; ?></span><?php } ?>
                </a>
            <?php } ?>
            <a class="nav-item" href="editprofile.php">Profile</a>
            <form class="nav-logout-form" action="../control/logout_process.php" method="get">
                <button type="submit" class="nav-item nav-cta nav-btn">Logout</button>
            </form>
        </div>
    </div>
</nav>
