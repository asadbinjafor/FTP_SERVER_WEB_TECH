<?php
if(contentFileExists($item["file_path"] ?? "")){
?>
    <a class="btn-primary" href="../control/download_process.php?id=<?php echo (int)$item["id"]; ?>">Download</a>
<?php } else { ?>
    <span class="btn-secondary" style="opacity:0.7;cursor:not-allowed;" title="File missing — re-upload from Admin panel">Unavailable</span>
<?php } ?>
