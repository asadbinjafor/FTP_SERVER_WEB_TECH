<?php include '../control/admin_content_edit_process.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Edit Content</title><link rel="stylesheet" href="../css/task1_style.css"></head>
<body>
<?php include 'nav_staff.php'; ?>
<div class="main-wrap">
    <h1 class="section-title">Edit Content</h1>
    <?php foreach($errors as $e){ if($e){ ?><div class="msg-error"><?php echo esc($e); ?></div><?php } } ?>
    <form method="post" enctype="multipart/form-data" class="card" onsubmit="return validateContentUpload()">
        <?php echo csrfField(); ?>
        <input type="hidden" name="content_id" value="<?php echo (int)$content['id']; ?>">
        <div class="form-group"><label>Title</label><input type="text" name="title" value="<?php echo esc($content['title']); ?>" required></div>
        <div class="form-group"><label>Description</label><textarea name="description" rows="4"><?php echo esc($content['description']); ?></textarea></div>
        <div class="form-group"><label>Category</label>
            <select name="category_id" required>
                <?php foreach($categoryList as $c){
                    $label = $c["parent_name"] ? $c["parent_name"]." → ".$c["name"] : $c["name"];
                ?><option value="<?php echo (int)$c['id']; ?>" <?php echo (int)$content['category_id']===(int)$c['id']?'selected':''; ?>><?php echo esc($label); ?></option><?php } ?>
            </select>
        </div>
        <p style="color:var(--muted);margin-bottom:12px;">Current file: <?php echo esc($content['file_path']); ?> (<?php echo esc($content['file_type']); ?>)</p>
        <div class="form-group"><label>Replace file (optional)</label><input type="file" name="content_file"></div>
        <button type="submit" name="save" class="btn-primary">Update</button>
        <a class="btn-secondary" href="admin_contents.php">Cancel</a>
    </form>
</div>
<script src="../js/task2_script.js"></script>
</body>
</html>
