# Deploying to Railway

This guide shows how to deploy this Laravel 12 app to Railway using the included Dockerfile.

## Prerequisites
- A Railway account
- The Railway CLI installed locally (Windows PowerShell):

```powershell
winget install Railway.Railway
```

- A GitHub repo with this code (recommended workflow), or deploy from local using the CLI.

## Why Dockerfile
This app needs PHP 8.2 plus extensions used by PDF/Excel exports (zip, gd) and a Node build for Vite. The provided `Dockerfile` builds everything deterministically and serves via Apache from `public/`.

## 1) Connect the repo to Railway
- Push your changes (including `Dockerfile`) to GitHub on the `for_production` branch.
- In Railway Dashboard:
  - New Project > Deploy from GitHub
  - Select your repository and branch
  - Railway will detect the `Dockerfile` and start building

Alternatively, from your local machine:

```powershell
railway login
railway init
railway up
```

When asked, choose to deploy using the Dockerfile.

## 2) Configure environment variables
Set the following variables in Railway (Settings > Variables) for the Web service:

- APP_ENV=production
- APP_DEBUG=false
- APP_NAME=Checkpoint Shoes
- APP_URL=https://<your-domain>
- APP_KEY=base64:...
- SESSION_DRIVER=cookie
- QUEUE_CONNECTION=database
- LOG_CHANNEL=stack

To generate APP_KEY quickly:
- Locally: `php artisan key:generate --show` and copy the value
- Or in Railway: open the service Shell and run the same command, then paste into Variables

## 3) Database
Create a managed database in Railway:
- Add New > Database > (MySQL or PostgreSQL)
- Railway will provide env vars; map them to Laravel's expected keys on the Web service:

For MySQL:
- DB_CONNECTION=mysql
- DB_HOST=<Railway MySQL host>
- DB_PORT=3306
- DB_DATABASE=<database>
- DB_USERNAME=<username>
- DB_PASSWORD=<password>

For PostgreSQL:
- DB_CONNECTION=pgsql
- DB_HOST=<host>
- DB_PORT=5432
- DB_DATABASE=<database>
- DB_USERNAME=<username>
- DB_PASSWORD=<password>

Tip: If Railway provides a single `DATABASE_URL`, you can either parse it or add the discrete values above.

## 4) First deploy and migrations
- Trigger a deploy (push to GitHub or click Deploy) and wait for build to complete
- After deploy, open the project Shell (Web service) and run:

```bash
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
```

## 5) Assets and public path
The Dockerfile builds Vite assets and places them in `public/build`. Apache is configured to serve `public/` as the document root with proper rewrite rules for Laravel.

## 6) File uploads and persistence
By default, container files do not persist across deploys. If your app stores user uploads, attach a Railway Volume to the Web service and mount it at `/var/www/html/storage/app/public`. The existing `public/storage` symlink will continue to work.

## 7) Queues (optional but recommended)
This app defaults to the `database` queue driver. You have two options:

- Separate worker service: Duplicate the Web service in Railway, rename to "Worker", and set the Start Command to:
  `php artisan queue:work --sleep=3 --tries=3 --queue=default`

- Same container using a Procfile/supervisor is possible, but separate service is simpler in Railway.

Ensure the Worker shares the same environment variables as Web and points to the same database.

## 8) Domains and HTTPS
- Add a custom domain in Railway (Settings > Domains) and point DNS as instructed
- APP_URL should match your HTTPS domain

## Troubleshooting
- 500 errors immediately after deploy: check APP_KEY, APP_URL, and caches (`php artisan config:clear`)
- Missing ZipArchive: our Dockerfile installs `zip`; if you used Nixpacks without Dockerfile, switch to this Docker-based deploy
- PDF/image issues: ensure `gd` is installed (Dockerfile includes it)
- Vite 404s: confirm `public/build` exists; redeploy to rebuild assets

## Notes
- PHP version: 8.2 (matches composer.json)
- Laravel: 12.x
- Livewire 3, Spatie Permissions/Activitylog, Maatwebsite/Excel, DOMPDF supported via installed extensions.
