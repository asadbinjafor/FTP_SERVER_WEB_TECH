<?php
include_once "database.php";

class MyDB{
    function createConn(){
        $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if($conn->connect_error){
            return false;
        }
        $conn->set_charset("utf8mb4");
        return $conn;
    }

    function closeConn($conn){
        if($conn instanceof mysqli){
            $tid = $conn->thread_id;
            if($tid !== null && $tid !== 0){
                $conn->close();
            }
        }
    }

    function getUserByEmail($email, $conn){
        $sql  = "SELECT * FROM users WHERE email = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result();
    }

    function getUserById($id, $conn){
        $sql  = "SELECT * FROM users WHERE id = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result();
    }

    function emailExists($email, $conn, $excludeId = 0){
        $sql  = "SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $email, $excludeId);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    function registerUser($name, $email, $hash, $role, $conn){
        $sql  = "INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $email, $hash, $role);
        return $stmt->execute();
    }

    function updateProfile($id, $name, $email, $picture, $conn){
        $sql  = "UPDATE users SET name = ?, email = ?, profile_picture = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $email, $picture, $id);
        return $stmt->execute();
    }

    function updatePassword($id, $hash, $conn){
        $sql  = "UPDATE users SET password_hash = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $hash, $id);
        return $stmt->execute();
    }

    function getAllModerators($conn){
        $sql = "SELECT id, name, email, role, created_at FROM users WHERE role = 'moderator' ORDER BY created_at DESC";
        return $conn->query($sql);
    }

    function getAllStaff($conn){
        $sql = "SELECT id, name, email, role, created_at FROM users WHERE role IN ('admin','moderator') ORDER BY role, created_at DESC";
        return $conn->query($sql);
    }

    function deleteModerator($id, $conn){
        $sql  = "DELETE FROM users WHERE id = ? AND role = 'moderator'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    function deleteStaffUser($id, $role, $conn){
        $sql  = "DELETE FROM users WHERE id = ? AND role = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $id, $role);
        return $stmt->execute();
    }

    function deleteModeratorCascade($moderatorId, $conn){
        $sql  = "SELECT id, file_path FROM contents WHERE uploader_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $moderatorId);
        $stmt->execute();
        $res = $stmt->get_result();
        while($row = $res->fetch_assoc()){
            $this->deleteContentFile($row["file_path"]);
            $this->deleteContent($row["id"], $conn);
        }
        return $this->deleteModerator($moderatorId, $conn);
    }

    function deleteContentFile($fileName){
        if($fileName === ""){
            return;
        }
        include_once dirname(__DIR__) . "/control/app.php";
        $path = CONTENT_UPLOAD_DIR . basename($fileName);
        if(is_file($path)){
            unlink($path);
        }
    }

    function deleteContentWithFile($id, $conn){
        $res = $this->getContentById($id, $conn);
        if($res->num_rows > 0){
            $row = $res->fetch_assoc();
            $this->deleteContentFile($row["file_path"]);
        }
        return $this->deleteContent($id, $conn);
    }

    function getContentsFiltered($categoryId, $conn){
        if($categoryId <= 0){
            return $this->getAllContents($conn);
        }
        $sql = "SELECT contents.*, categories.name AS category_name, users.name AS uploader_name
                FROM contents
                LEFT JOIN categories ON contents.category_id = categories.id
                LEFT JOIN users ON contents.uploader_id = users.id
                WHERE contents.category_id = ? OR contents.category_id IN (SELECT id FROM categories WHERE parent_id = ?)
                ORDER BY contents.uploaded_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $categoryId, $categoryId);
        $stmt->execute();
        return $stmt->get_result();
    }

    function verifyCurrentPassword($userId, $password, $conn){
        $res = $this->getUserById($userId, $conn);
        if($res->num_rows == 0){
            return false;
        }
        $user = $res->fetch_assoc();
        return password_verify($password, $user["password_hash"]);
    }

    function getTopCategories($conn){
        $sql = "SELECT id, name FROM categories WHERE parent_id IS NULL ORDER BY name";
        return $conn->query($sql);
    }

    function getSubcategories($parentId, $conn){
        $sql  = "SELECT id, name FROM categories WHERE parent_id = ? ORDER BY name";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $parentId);
        $stmt->execute();
        return $stmt->get_result();
    }

    function getCategoryById($id, $conn){
        $sql  = "SELECT * FROM categories WHERE id = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result();
    }

    function getAllCategoriesFlat($conn){
        $sql = "SELECT c.id, c.name, c.parent_id, p.name AS parent_name
                FROM categories c
                LEFT JOIN categories p ON c.parent_id = p.id
                ORDER BY COALESCE(p.name, c.name), c.name";
        return $conn->query($sql);
    }

    function getHighlightedContents($limit, $conn){
        $sql  = "SELECT contents.*, categories.name AS category_name, users.name AS uploader_name
                 FROM contents
                 LEFT JOIN categories ON contents.category_id = categories.id
                 LEFT JOIN users ON contents.uploader_id = users.id
                 ORDER BY contents.download_count DESC, contents.uploaded_at DESC
                 LIMIT ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result();
    }

    function getContentsByCategory($categoryId, $subId, $fileType, $conn){
        $sql = "SELECT contents.*, categories.name AS category_name, users.name AS uploader_name
                FROM contents
                LEFT JOIN categories ON contents.category_id = categories.id
                LEFT JOIN users ON contents.uploader_id = users.id
                WHERE 1=1";
        $types = "";
        $params = array();

        if($categoryId > 0){
            if($subId > 0){
                $sql .= " AND contents.category_id = ?";
                $types .= "i";
                $params[] = $subId;
            } else {
                $sql .= " AND (contents.category_id = ? OR contents.category_id IN (SELECT id FROM categories WHERE parent_id = ?))";
                $types .= "ii";
                $params[] = $categoryId;
                $params[] = $categoryId;
            }
        }
        if($fileType !== ""){
            $sql .= " AND contents.file_type = ?";
            $types .= "s";
            $params[] = $fileType;
        }
        $sql .= " ORDER BY contents.uploaded_at DESC";

        $stmt = $conn->prepare($sql);
        if($types !== ""){
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result();
    }

    function searchContents($q, $categoryId, $subId, $fileType, $conn){
        $search = "%" . $q . "%";
        $sql = "SELECT contents.*, categories.name AS category_name, users.name AS uploader_name
                FROM contents
                LEFT JOIN categories ON contents.category_id = categories.id
                LEFT JOIN users ON contents.uploader_id = users.id
                WHERE (contents.title LIKE ? OR contents.description LIKE ?)";
        $types = "ss";
        $params = array($search, $search);

        if($categoryId > 0){
            if($subId > 0){
                $sql .= " AND contents.category_id = ?";
                $types .= "i";
                $params[] = $subId;
            } else {
                $sql .= " AND (contents.category_id = ? OR contents.category_id IN (SELECT id FROM categories WHERE parent_id = ?))";
                $types .= "ii";
                $params[] = $categoryId;
                $params[] = $categoryId;
            }
        }
        if($fileType !== ""){
            $sql .= " AND contents.file_type = ?";
            $types .= "s";
            $params[] = $fileType;
        }
        $sql .= " ORDER BY contents.download_count DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result();
    }

    function getContentById($id, $conn){
        $sql  = "SELECT contents.*, categories.name AS category_name, users.name AS uploader_name
                 FROM contents
                 LEFT JOIN categories ON contents.category_id = categories.id
                 LEFT JOIN users ON contents.uploader_id = users.id
                 WHERE contents.id = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result();
    }

    function getAllContents($conn){
        $sql = "SELECT contents.*, categories.name AS category_name, users.name AS uploader_name
                FROM contents
                LEFT JOIN categories ON contents.category_id = categories.id
                LEFT JOIN users ON contents.uploader_id = users.id
                ORDER BY contents.uploaded_at DESC";
        return $conn->query($sql);
    }

    function insertContent($title, $desc, $path, $ftype, $catId, $uploaderId, $conn){
        $sql  = "INSERT INTO contents (title, description, file_path, file_type, category_id, uploader_id)
                 VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssii", $title, $desc, $path, $ftype, $catId, $uploaderId);
        if($stmt->execute()){
            return $conn->insert_id;
        }
        return false;
    }

    function updateContent($id, $title, $desc, $path, $ftype, $catId, $conn){
        if($path !== ""){
            $sql  = "UPDATE contents SET title=?, description=?, file_path=?, file_type=?, category_id=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssii", $title, $desc, $path, $ftype, $catId, $id);
        } else {
            $sql  = "UPDATE contents SET title=?, description=?, file_type=?, category_id=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssii", $title, $desc, $ftype, $catId, $id);
        }
        return $stmt->execute();
    }

    function deleteContent($id, $conn){
        $sql  = "DELETE FROM contents WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    function incrementDownload($id, $conn){
        $sql  = "UPDATE contents SET download_count = download_count + 1 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    function addContentRequest($title, $category, $message, $userId, $ip, $sid, $conn){
        $sql  = "INSERT INTO content_requests (user_id, content_title, category_requested, message, requester_ip, session_id, status)
                 VALUES (?, ?, ?, ?, ?, ?, 'pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssss", $userId, $title, $category, $message, $ip, $sid);
        return $stmt->execute();
    }

    function getClientRequests($userId, $conn){
        $sql  = "SELECT * FROM content_requests WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result();
    }

    function getAllRequests($conn){
        $sql = "SELECT cr.*, u.name AS client_name, u.email AS client_email
                FROM content_requests cr
                LEFT JOIN users u ON u.id = cr.user_id
                ORDER BY cr.created_at DESC";
        return $conn->query($sql);
    }

    function getPendingRequestCount($conn){
        $row = $conn->query("SELECT COUNT(*) AS cnt FROM content_requests WHERE status='pending'")->fetch_assoc();
        return (int)$row["cnt"];
    }

    function updateRequestStatus($id, $status, $conn){
        $sql  = "UPDATE content_requests SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }

    function getAdminDashboardCounts($conn){
        $c = array();
        $c["contents"]   = (int)$conn->query("SELECT COUNT(*) AS n FROM contents")->fetch_assoc()["n"];
        $c["categories"] = (int)$conn->query("SELECT COUNT(*) AS n FROM categories")->fetch_assoc()["n"];
        $c["moderators"] = (int)$conn->query("SELECT COUNT(*) AS n FROM users WHERE role='moderator'")->fetch_assoc()["n"];
        $c["requests"]   = $this->getPendingRequestCount($conn);
        return $c;
    }
}
?>
