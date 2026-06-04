<?php
include '../control/home_process.php';
include_once '../control/paths.php';
$viewBase = viewBase();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc(SITE_NAME); ?> — Browse & Download</title>
    <base href="<?php echo esc($viewBase); ?>">
    <link rel="stylesheet" href="../css/task1_style.css?v=6">
    <link rel="stylesheet" href="../css/task4_style.css?v=6">
</head>
<body>
<?php include 'nav_guest.php'; ?>

<div class="main-wrap">
    <?php if($role !== "guest"){ ?>
        <div class="welcome-bar">
            <span>Welcome, <strong><?php echo esc($name); ?></strong></span>
            <?php if($role === "client"){ ?>
                <a class="btn-secondary" href="editprofile.php">My Profile</a>
                <a class="btn-secondary" href="Home.php#requestSection">Request Content</a>
                <a class="btn-secondary" href="client_requests.php">My Requests</a>
            <?php } ?>
        </div>
    <?php } ?>

    <div class="category-tabs">
        <a class="cat-tab <?php echo $activeCategory === 0 ? 'active' : ''; ?>" href="Home.php">All</a>
        <?php foreach($topCategories as $cat){ ?>
            <a class="cat-tab <?php echo $activeCategory === (int)$cat['id'] ? 'active' : ''; ?>"
               href="Home.php?category=<?php echo (int)$cat['id']; ?>"><?php echo esc($cat['name']); ?></a>
        <?php } ?>
    </div>

    <?php if($activeCategory === 0 && $searchQ === ""){ ?>
    <section class="highlight-section">
        <h2 class="section-title">Popular Downloads</h2>
        <div class="highlight-grid content-grid">
            <?php
            $hasHighlight = false;
            if($highlighted){
                while($h = $highlighted->fetch_assoc()){
                    $hasHighlight = true;
            ?>
                <article class="content-card">
                    <h3><?php echo esc($h["title"]); ?></h3>
                    <p class="meta"><?php echo esc($h["category_name"] ?? "—"); ?> · <?php echo esc($h["uploader_name"] ?? ""); ?></p>
                    <p class="desc"><?php echo esc($h["description"] ?? ""); ?></p>
                    <div class="card-foot">
                        <span class="file-type-tag"><?php echo esc($h["file_type"] ?? "file"); ?></span>
                        <span class="download-count"><?php echo (int)$h["download_count"]; ?> downloads</span>
                        <?php $item = $h; include 'part_download_btn.php'; ?>
                    </div>
                </article>
            <?php }
            }
            if(!$hasHighlight){ ?>
                <div class="empty-state">No content yet. Admin/Moderator can upload from dashboard after login.</div>
            <?php } ?>
        </div>
    </section>
    <?php } ?>

    <?php if($activeCategory > 0 && count($subcategories) > 0){ ?>
    <div class="filter-chips">
        <span style="color:var(--muted);font-size:0.85rem;margin-right:8px;">Subcategory:</span>
        <a class="filter-chip <?php echo $activeSub === 0 ? 'active' : ''; ?>" href="Home.php?category=<?php echo $activeCategory; ?>">All</a>
        <?php foreach($subcategories as $sub){ ?>
            <a class="filter-chip <?php echo $activeSub === (int)$sub['id'] ? 'active' : ''; ?>"
               href="Home.php?category=<?php echo $activeCategory; ?>&sub=<?php echo (int)$sub['id']; ?>"><?php echo esc($sub['name']); ?></a>
        <?php } ?>
    </div>
    <?php } ?>

    <div class="search-panel">
        <h2 class="section-title">Search & Filter</h2>
        <div id="searchMsg"></div>
        <div class="search-row">
            <div class="form-group">
                <label for="searchQ">Search</label>
                <input type="text" id="searchQ" placeholder="Title or description..." value="<?php echo esc($searchQ); ?>">
            </div>
            <div class="form-group">
                <label for="filterCategory">Category</label>
                <select id="filterCategory">
                    <option value="0">All categories</option>
                    <?php foreach($topCategories as $cat){ ?>
                        <option value="<?php echo (int)$cat['id']; ?>" <?php echo $activeCategory === (int)$cat['id'] ? 'selected' : ''; ?>><?php echo esc($cat['name']); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="filterSub">Subcategory</label>
                <select id="filterSub">
                    <option value="0">All subcategories</option>
                    <?php foreach($subcategories as $sub){ ?>
                        <option value="<?php echo (int)$sub['id']; ?>" <?php echo $activeSub === (int)$sub['id'] ? 'selected' : ''; ?>><?php echo esc($sub['name']); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="filterFileType">File type</label>
                <select id="filterFileType">
                    <option value="">All types</option>
                    <?php foreach(array("mp4","mkv","pdf","zip","exe","mp3") as $ft){ ?>
                        <option value="<?php echo $ft; ?>" <?php echo $fileTypeFilter === $ft ? 'selected' : ''; ?>><?php echo strtoupper($ft); ?></option>
                    <?php } ?>
                </select>
            </div>
            <button type="button" class="btn-primary" id="btnSearch">Search</button>
        </div>
    </div>

    <div id="contentGrid" class="content-grid">
        <?php
        if($contents && $contents->num_rows > 0){
            while($c = $contents->fetch_assoc()){
        ?>
            <article class="content-card">
                <h3><?php echo esc($c["title"]); ?></h3>
                <p class="meta"><?php echo esc($c["category_name"]); ?> · <?php echo esc($c["uploader_name"]); ?></p>
                <p class="desc"><?php echo esc($c["description"]); ?></p>
                <div class="card-foot">
                    <span class="file-type-tag"><?php echo esc($c["file_type"]); ?></span>
                    <span class="download-count"><?php echo (int)$c["download_count"]; ?> downloads</span>
                    <?php $item = $c; include 'part_download_btn.php'; ?>
                </div>
            </article>
        <?php }
        } elseif($activeCategory > 0 || $searchQ !== "") { ?>
            <div class="empty-state">No content in this category yet.</div>
        <?php } ?>
    </div>

    <?php if($role === "client"){ ?>
    <section class="request-box" id="requestSection">
        <h2>Request New Content</h2>
        <p class="sub">Can't find what you need? Submit a request — Admin &amp; Moderator will review it.</p>
        <div id="requestMsg"></div>
        <form id="requestForm">
            <input type="hidden" id="reqCsrf" value="<?php echo esc(csrfToken()); ?>">
            <div class="request-grid">
                <div class="form-group">
                    <label for="reqTitle">Content Title *</label>
                    <input type="text" id="reqTitle" maxlength="200" required placeholder="e.g. Adobe Photoshop 2024">
                </div>
                <div class="form-group">
                    <label for="reqCategory">Category *</label>
                    <select id="reqCategory" required>
                        <option value="">Select category</option>
                        <?php foreach($categoryOptions as $ac){
                            $label = $ac["parent_name"] ? $ac["parent_name"] . " → " . $ac["name"] : $ac["name"];
                        ?>
                            <option value="<?php echo esc($label); ?>"><?php echo esc($label); ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="reqMessage">Additional message</label>
                <textarea id="reqMessage" rows="3" placeholder="Version, language, or other details..."></textarea>
            </div>
            <button type="submit" class="btn-primary">Submit Request</button>
        </form>
    </section>
    <?php } elseif($role === "guest"){ ?>
    <div class="card guest-cta-card" style="margin-top:24px;text-align:center;">
        <p style="color:var(--muted);margin-bottom:12px;">Want to request new content?</p>
        <form action="registration.php" method="get" style="display:inline;"><button type="submit" class="btn-primary">Register as Client</button></form>
        <form action="login.php" method="get" style="display:inline;"><button type="submit" class="btn-secondary">Client Login</button></form>
    </div>
    <?php } ?>

</div>

<script src="../js/task4_script.js?v=4"></script>
</body>
</html>
