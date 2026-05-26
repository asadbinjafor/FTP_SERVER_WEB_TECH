<?php include '../control/admin_contents_process.php'; $navActive = "contents"; ?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>All Contents</title><link rel="stylesheet" href="../css/task1_style.css"></head>
<body>
<?php include 'nav_staff.php'; ?>
<div class="main-wrap">
    <h1 class="section-title">All Contents</h1>
    <?php if(isset($_GET["uploaded"])){ ?><div class="msg-success">Content uploaded.</div><?php } ?>
    <?php if(isset($_GET["updated"])){ ?><div class="msg-success">Content updated.</div><?php } ?>
    <p style="margin-bottom:16px;"><a class="btn-primary" href="admin_content_form.php">Upload New</a></p>
    <input type="hidden" id="csrfDel" value="<?php echo esc(csrfToken()); ?>">
    <table class="data-table">
        <thead><tr><th>Title</th><th>Category</th><th>Type</th><th>Downloads</th><th>Uploader</th><th>Actions</th></tr></thead>
        <tbody>
        <?php while($c = $contents->fetch_assoc()){ ?>
            <tr>
                <td><?php echo esc($c["title"]); ?></td>
                <td><?php echo esc($c["category_name"]); ?></td>
                <td><?php echo esc($c["file_type"]); ?></td>
                <td><?php echo (int)$c["download_count"]; ?></td>
                <td><?php echo esc($c["uploader_name"]); ?></td>
                <td>
                    <a class="btn-secondary" href="admin_content_edit.php?id=<?php echo (int)$c['id']; ?>">Edit</a>
                    <button class="btn-danger" onclick="deleteContentAjax(<?php echo (int)$c['id']; ?>, document.getElementById('csrfDel').value)">Delete</button>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<script src="../js/task2_script.js"></script>
</body>
</html>
