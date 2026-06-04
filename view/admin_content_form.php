<?php include '../control/admin_content_form_process.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Upload Content</title><link rel="stylesheet" href="../css/task1_style.css"></head>
<body>
<?php include 'nav_staff.php'; ?>
<div class="main-wrap">
    <h1 class="section-title">Upload Media Content</h1>
    <?php foreach($errors as $e){ if($e){ ?><div class="msg-error"><?php echo esc($e); ?></div><?php } } ?>
    <form method="post" enctype="multipart/form-data" class="card" onsubmit="return validateContentUpload()">
        <?php echo csrfField(); ?>
        <div class="form-group"><label>Title</label><input type="text" name="title" required></div>
        <div class="form-group"><label>Description</label><textarea name="description" rows="4"></textarea></div>
        <div class="form-group"><label>Category</label>
            <select name="category_id" required>
                <option value="">Select</option>
                <?php foreach($categoryList as $c){
                    $label = $c["parent_name"] ? $c["parent_name"]." → ".$c["name"] : $c["name"];
                ?><option value="<?php echo (int)$c['id']; ?>"><?php echo esc($label); ?></option><?php } ?>
            </select>
        </div>
        <div class="form-group"><label>File (mp4, pdf, zip, exe… max 50MB)</label><input type="file" name="content_file" required></div>
        <button type="submit" name="save" class="btn-primary">Upload</button>
    </form>
</div>
<script src="../js/task2_script.js"></script>
</body>
</html>
