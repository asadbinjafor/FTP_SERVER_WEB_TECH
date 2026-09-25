<?php
function storageConfigured() {
    return getenv('SUPABASE_URL') && getenv('SUPABASE_SERVICE_ROLE_KEY') && getenv('SUPABASE_STORAGE_BUCKET');
}

function storageRequest($method, $folder, $name, $body = null, $contentType = null) {
    $bucket = rawurlencode(getenv('SUPABASE_STORAGE_BUCKET'));
    $object = rawurlencode($folder) . '/' . rawurlencode(basename($name));
    $prefix = $method === 'GET' ? '/storage/v1/object/authenticated/' : '/storage/v1/object/';
    $url = rtrim(getenv('SUPABASE_URL'), '/') . $prefix . $bucket . '/' . $object;
    $key = getenv('SUPABASE_SERVICE_ROLE_KEY');
    $headers = array('apikey: ' . $key, 'Authorization: Bearer ' . $key);
    if ($contentType) { $headers[] = 'Content-Type: ' . $contentType; }
    $curl = curl_init($url);
    curl_setopt_array($curl, array(
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 90
    ));
    if ($body !== null) { curl_setopt($curl, CURLOPT_POSTFIELDS, $body); }
    $response = curl_exec($curl);
    $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    if ($response === false || $code < 200 || $code >= 300) {
        error_log('Supabase Storage request failed: HTTP ' . $code . ' (' . $folder . ')');
        return false;
    }
    return $response;
}

function saveUploadedFile($file, $folder, $name) {
    if (getenv('RENDER') && !storageConfigured()) {
        error_log('Supabase Storage is not configured');
        return false;
    }
    if (storageConfigured()) {
        $body = file_get_contents($file['tmp_name']);
        if ($body === false) { return false; }
        $type = mime_content_type($file['tmp_name']) ?: 'application/octet-stream';
        return storageRequest('POST', $folder, $name, $body, $type) !== false;
    }
    $dir = $folder === 'profile' ? PROFILE_UPLOAD_DIR : CONTENT_UPLOAD_DIR;
    ensureUploadDir($dir);
    return move_uploaded_file($file['tmp_name'], $dir . basename($name));
}

function deleteStoredFile($folder, $name) {
    if (!$name) { return; }
    $dir = $folder === 'profile' ? PROFILE_UPLOAD_DIR : CONTENT_UPLOAD_DIR;
    $path = $dir . basename($name);
    if (storageConfigured()) {
        storageRequest('DELETE', $folder, $name);
    }
    if (is_file($path)) { unlink($path); }
}

function storedFilePath($folder, $name) {
    $local = ($folder === 'profile' ? PROFILE_UPLOAD_DIR : CONTENT_UPLOAD_DIR) . basename($name);
    if (is_file($local)) { return $local; }
    if (!storageConfigured()) { return false; }
    $body = storageRequest('GET', $folder, $name);
    if ($body === false) { return false; }
    $path = tempnam(sys_get_temp_dir(), 'ftp_');
    if ($path === false || file_put_contents($path, $body) === false) { return false; }
    return $path;
}
