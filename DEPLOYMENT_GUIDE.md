# Deployment guide

Architecture: GitHub `main` → Render (PHP/Apache) → Supabase (PostgreSQL + private Storage); Vercel serves a static startup page at `/` and proxies every other route to Render.

## Order

1. **Supabase:** Create a project. In SQL Editor, run [`supabase_schema.sql`](supabase_schema.sql). It creates `users`, `categories`, `contents`, and `content_requests` plus starter categories. No default admin password is shipped.
2. **Admin:** Generate a password hash locally with `php -r "echo password_hash(trim(fgets(STDIN)), PASSWORD_DEFAULT), PHP_EOL;"` (type your chosen password on stdin). In Supabase SQL Editor, run:
   ```sql
   INSERT INTO public.users (name, email, password_hash, role)
   VALUES ('Admin', 'YOUR_EMAIL', 'PASTE_GENERATED_HASH', 'admin');
   ```
   Existing MySQL data is not in this repository; migrate it separately if needed. Keep real credentials out of Git.
3. **Storage:** Create a **private** Supabase Storage bucket named `ftp-uploads` with a file size limit of at least 50 MB. The app stores new content files in `contents/` and profile photos in `profile/`. Obtain the project URL and service role key from Supabase. The service role key belongs only in Render environment variables, never in Vercel or client code.
4. **Render:** Create a **Web Service** from this GitHub repository and production branch `main`; choose Docker runtime (the root [`Dockerfile`](Dockerfile) is detected). Set health check path `/health.php`. Set `DATABASE_URL` to the Supabase PostgreSQL connection string with `sslmode=require`; URL-encode special characters in its password. If using the pooler, use its exact host, port, username, and database. Alternatively set `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `DB_SSLMODE=require`. Set `SUPABASE_URL`, `SUPABASE_SERVICE_ROLE_KEY`, `SUPABASE_STORAGE_BUCKET=ftp-uploads`, and a long random `REMEMBER_SECRET`. The optional [`render.yaml`](render.yaml) has the same service settings. Deploy and verify `https://YOUR_RENDER_HOST.onrender.com/health.php` returns HTTP 200 JSON and `/view/Home.php` loads.
5. **Vercel:** Replace `REPLACE_WITH_RENDER_HOST` in [`vercel-proxy/vercel.json`](vercel-proxy/vercel.json) with the Render hostname only (no `https://` or trailing slash), then commit/push that one change. Import the same GitHub repository in Vercel with **Root Directory `vercel-proxy`**, Framework Preset **Other**, and automatic deployments enabled. Deploy. `/` serves [`vercel-proxy/index.html`](vercel-proxy/index.html), which checks `/health.php` and `/view/Home.php` twice consecutively for HTTP 200 before redirecting. Failed or 502 checks retry every 5 seconds; the Retry button triggers another check. All other paths proxy to Render.

Both Render and Vercel should deploy automatically from later pushes to `main`. Render must be healthy before the Vercel startup page can redirect.

## Local run

With Docker available, copy [`.env.example`](.env.example) to `.env`, fill real values, and run `docker build -t ftp-web .` followed by `docker run --rm --env-file .env -p 10000:10000 ftp-web`. Open `http://localhost:10000/` (redirects to `/view/Home.php`). The PHP entry point is [`index.php`](index.php); login is `/view/login.php`, client registration is `/view/registration.php`, admin is `/view/admin_dashboard.php`, and moderator is `/view/moderator_dashboard.php`. The old MySQL upgrade endpoint is disabled. The `.env` file is ignored by Git.

## Troubleshooting

| Symptom | Check |
| --- | --- |
| Vercel 502 or startup loop | Open Render `/health.php` and `/view/Home.php` directly. Render Free can sleep; the startup page retries while it wakes. Check that `vercel.json` has the actual Render hostname. |
| Database unavailable / 503 | Verify the Supabase schema and `DATABASE_URL`, or all `DB_*` variables. Use Supabase's exact pooler or direct connection port. Check Render logs without printing credentials. |
| Render port failure | Keep the Docker `start-app` command: it configures Apache to listen on Render's dynamic `$PORT`. |
| SSL connection failure | Set `sslmode=require` in `DATABASE_URL` or `DB_SSLMODE=require`; use the connection string supplied by Supabase. |
| Login/session resets | Use the same Vercel hostname throughout a session. Secure cookies use the forwarded HTTPS protocol; Render instance restarts clear file-backed PHP sessions, so sign in again. Keep `REMEMBER_SECRET` stable. |
| Missing CSS/image/file | Check exact Linux filename case (`/view/Home.php`, `/css/task1_style.css`). Verify the Storage bucket and server-side key. Existing repository sample files are local; newly uploaded files are stored in Supabase. |

Render Free sleeps when idle. The startup page handles wake-up 502 responses; true always-on service requires a paid Render plan.
