-- Run once if database already exists (add client role + request user link)
USE isp_media;

ALTER TABLE users MODIFY role ENUM('admin','moderator','client') NOT NULL;

ALTER TABLE content_requests
ADD COLUMN user_id INT NULL AFTER id;

ALTER TABLE content_requests
ADD CONSTRAINT fk_cr_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL;
