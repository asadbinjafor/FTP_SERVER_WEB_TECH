# A$AD FTP

## Project Scenario Summary

**A$AD FTP** is a web-based media library and download platform. It lets visitors browse categorized digital content (software, videos, documents, archives, and more), search and filter listings, and download files. Registered **clients** can submit requests when content is missing; **moderators** and **admins** manage uploads and review those requests.

The system supports four access levels:

| Role | Description |
|------|-------------|
| **Admin** | Full control: dashboard stats, create/delete admin and moderator accounts, upload/edit/delete all content, review client requests. |
| **Moderator** | Manages own uploads: add/edit/delete content, review and update status of client requests. |
| **Client** | Self-registration: profile and password, browse and download, submit content requests (AJAX), track request history. |
| **Guest** | Public home page: browse categories, search/filter, download available files; encouraged to register to submit requests. |

**Typical workflow:** Admin or moderator uploads a file with category and metadata → content appears on the public browse page → users download (download count increments) → clients can request new items → admin/moderator reviews requests from their dashboards.

This project was built as **Web Technologies — WTProject_04**, using PHP with a layered structure (`control/`, `model/`, `view/`), MySQL, client-side validation, AJAX APIs, and security practices required by the assignment.

---

## Technologies & Topics Used

The project applies front-end, back-end, database, and security topics from web technologies courses.

### Front-End

| Topic | How it is used in this project |
|-------|----------------------------------|
| **HTML5** | Semantic layout, forms, tables, navigation, content cards, admin/moderator panels, auth pages |
| **CSS3** | Custom properties (`:root`), Flexbox/Grid layouts, responsive design, cards, badges, alerts, data tables (`task1_style.css`, `task4_style.css`) |
| **JavaScript** | Form validation (profile, password, registration, uploads), `XMLHttpRequest` / fetch-style AJAX for search, subcategories, content requests, staff/content delete |

### Back-End

| Topic | How it is used in this project |
|-------|----------------------------------|
| **PHP** | Server-side logic, sessions, routing via `*_process.php` handlers, role gates |
| **Layered architecture** | `control/` (logic & APIs), `model/` (MyDB + queries), `view/` (templates) |
| **MySQLi** | Database connection with **prepared statements** (SQL injection prevention) |
| **Sessions & cookies** | Login state, roles, “Remember Me” (HMAC-signed cookie), CSRF tokens |
| **File upload** | Profile pictures and content files with extension whitelist and size limits |
| **Password security** | `password_hash()` on register; `password_verify()` on login |

### Database

| Topic | How it is used in this project |
|-------|----------------------------------|
| **MySQL** | Database name: `isp_media` (see `model/database.php`) |
| **Tables** | `users`, `contents`, `categories`, `content_requests` |
| **Keys & integrity** | Foreign keys (e.g. content → category, uploader; requests → user after upgrade) |

### Other Web Topics

| Topic | How it is used in this project |
|-------|----------------------------------|
| **AJAX / JSON** | `content_search_api.php`, `subcategories_api.php`, `request_add_api.php`, `request_status_api.php`, delete APIs |
| **XSS prevention** | `esc()` / `htmlspecialchars()` when outputting user and DB data |
| **CSRF protection** | Hidden token on forms; `requireCsrfPost()` on POST handlers |
| **Security headers** | `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy` |
| **Responsive UI** | Viewport meta, flexible grids and navigation |
| **Apache (XAMPP)** | Local hosting; `index.php` redirects to `view/Home.php` |

---

## Default User Credentials

After importing **`database.sql`** in phpMyAdmin (creates database `isp_media` and seed data), use the default admin account documented in the project:

| Role | Email | Password |
|------|-------|----------|
| **Admin** | `admin@media.local` | `Admin@1234` |

**Notes:**

- Additional **moderator** accounts are created by admin under **Admin → Staff**.
- **Client** accounts are created via public **Register** (`registration.php`); role is set to `client` automatically.
- If you already had an older database, run **`database_upgrade_client.sql`** once (or open `control/db_upgrade_client.php`) to enable the client role and link requests to users.

---

## How to Run the Project

1. Install **XAMPP** and start **Apache** and **MySQL**.
2. Copy the project folder to `htdocs` (e.g. `C:\xampp\htdocs\WTProject_04`).
3. Import **`database.sql`** in phpMyAdmin (creates `isp_media` and seed admin).
4. If upgrading an existing DB, also run **`database_upgrade_client.sql`**.
5. Open: **http://localhost/WTProject_04/view/Home.php**  
   (or **http://localhost/WTProject_04/** — redirects via `index.php`).
6. Log in with the admin email and password above.

If MySQL credentials differ from XAMPP defaults, edit **`model/database.php`**.

Setup help page: **`view/db_setup_help.php`**.

---

## Main Modules (Assignment Tasks)

| Task | Module | Main features |
|------|--------|----------------|
| **Task 1** | Auth & profile | Register (client), login, remember me, profile edit, password change, CSRF on forms |
| **Task 2** | Moderator | Dashboard, content upload/edit/delete (AJAX delete), client request review |
| **Task 3** | Admin | Dashboard, staff management (admin/moderator), all content CRUD, request moderation |
| **Task 4** | Public browse | Category tabs, live search/filter (AJAX), subcategory chips, downloads, client request box |

---

## Project Folder Overview

```
WTProject_04/
├── control/          → Process scripts, gates, JSON APIs, security helpers
├── model/            → database.php, mydb.php (MySQLi queries)
├── view/             → PHP/HTML pages and navigation partials
├── css/              → task1_style.css, task4_style.css
├── js/               → task1_script.js, task2_script.js, task4_script.js
├── uploads/
│   ├── profile/      → Profile pictures
│   └── contents/     → Uploaded media files
├── database_upgrade_client.sql
├── index.php         → Redirects to view/Home.php
└── README.md         → This file
```

---

## Security Features (Summary)

- Prepared statements for parameterized queries  
- Hashed passwords (never stored as plain text)  
- CSRF tokens on form submissions and verified on POST  
- Escaped output to reduce XSS risk  
- Role-based access (`admin_gate`, `moderator_gate`, `client_gate`)  
- Validated file uploads (allowed extensions and max size)  
- Signed “Remember Me” cookie with `httponly` and `SameSite`  

---

This README describes the project scenario, technologies used, and default login details for reviewers, instructors, and repository visitors.
