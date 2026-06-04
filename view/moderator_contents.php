<?php include '../control/moderator_contents_process.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Moderator Contents</title><link rel="stylesheet" href="../css/task1_style.css"></head>
<body>
<?php $navActive = "contents"; include 'nav_staff.php'; ?>
<div class="main-wrap">
    <h1 class="section-title">All Uploaded Contents</h1>
    <?php if(isset($_GET["uploaded"])){ ?><div class="msg-success">Content uploaded.</div><?php } ?>
    <p style="margin-bottom:16px;">
        <a class="btn-primary" href="moderator_content_form.php">Add Content</a>
        Filter:
        <a class="btn-secondary" href="moderator_contents.php">All</a>
        <?php foreach($catList as $cat){ ?>
            <a class="btn-secondary" href="moderator_contents.php?category=<?php echo (int)$cat['id']; ?>"><?php echo esc($cat['name']); ?></a>
        <?php } ?>
    </p>
    <input type="hidden" id="csrfDel" value="<?php echo esc(csrfToken()); ?>">
    <table class="data-table">
        <thead><tr><th>Title</th><th>Category</th><th>Downloads</th><th>Uploader</th><th>Action</th></tr></thead>
        <tbody>
        <?php while($c = $contents->fetch_assoc()){ ?>
            <tr>
                <td><?php echo esc($c["title"]); ?></td>
                <td><?php echo esc($c["category_name"]); ?></td>
                <td><?php echo (int)$c["download_count"]; ?></td>
                <td><?php echo esc($c["uploader_name"]); ?></td>
                <td><button class="btn-danger" onclick="deleteContentAjax(<?php echo (int)$c['id']; ?>, document.getElementById('csrfDel').value)">Delete</button></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<script src="../js/task2_script.js"></script>
</body>
</html>
