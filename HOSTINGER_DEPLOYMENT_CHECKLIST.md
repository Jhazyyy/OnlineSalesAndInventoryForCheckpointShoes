# Hostinger Deployment Checklist

## 📦 Pre-Deployment (On Your Local Machine)

### 1. Prepare Your Files
- [ ] Run `composer install --optimize-autoloader --no-dev`
- [ ] Run `npm run build` (compile assets)
- [ ] Update `.env.example` with production-ready values
- [ ] Test everything works locally
- [ ] Create database backup

### 2. Configuration Files
- [ ] Update `config/app.php` - Set proper timezone
- [ ] Check `config/database.php` - MySQL settings
- [ ] Review `config/filesystems.php` - Ensure 'public' disk is configured
- [ ] Verify `.htaccess` exists in public folder

### 3. Security Check
- [ ] Remove any debug/test files
- [ ] Check `.gitignore` includes `.env`
- [ ] Review sensitive data in code
- [ ] Ensure no hardcoded credentials

---

## 🚀 Hostinger Deployment Steps

### Step 1: Upload Files to Hostinger

#### Option A: Via FTP/SFTP (Recommended for large projects)
1. Connect to Hostinger via FileZilla/Cyberduck
2. Upload Laravel files to `/home/username/` (NOT in public_html yet!)
3. Move ONLY the `public` folder contents to `/home/username/public_html/`

#### Option B: Via File Manager
1. Login to Hostinger Control Panel
2. Use File Manager to upload a ZIP file
3. Extract in `/home/username/`
4. Move `public` folder contents to `public_html`

**Directory Structure:**
```
/home/username/
├── public_html/          ← Contents of Laravel's 'public' folder
│   ├── index.php
│   ├── .htaccess
│   ├── favicon.ico
│   └── ...
├── app/
├── bootstrap/
├── config/
├── database/
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env                  ← Create this file!
└── artisan
```

### Step 2: Update index.php

Edit `/home/username/public_html/index.php`:

**Find:**
```php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
```

**Change to:**
```php
require __DIR__.'/../../vendor/autoload.php';
$app = require_once __DIR__.'/../../bootstrap/app.php';
```

### Step 3: Create .env File

Create `/home/username/.env` with:

```env
APP_NAME="Your App Name"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_TIMEZONE=UTC
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=public
QUEUE_CONNECTION=database
SESSION_DRIVER=database
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

ASSET_URL=https://yourdomain.com
```

### Step 4: Generate Application Key

Via SSH:
```bash
cd /home/username
php artisan key:generate
```

OR create `generate_key.php` in public_html:
```php
<?php
chdir(__DIR__ . '/../');
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$status = $kernel->call('key:generate');
echo $status === 0 ? "✅ Key generated!" : "❌ Failed!";
```
Visit it once, then DELETE it!

### Step 5: Setup Database

1. **Create Database in Hostinger:**
   - Go to Hostinger Panel → MySQL Databases
   - Create new database
   - Create database user
   - Grant all privileges

2. **Import Your Database:**
   - Via phpMyAdmin (in Hostinger Panel)
   - Upload your SQL file
   - Or run migrations via SSH: `php artisan migrate --force`

### Step 6: Fix Storage & Permissions

**Upload `create_symlink.php` to public_html:**
1. Visit: `https://yourdomain.com/create_symlink.php`
2. Verify success message
3. **DELETE the file immediately!**

**Via SSH (preferred):**
```bash
cd /home/username
php artisan storage:link
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Step 7: Clear & Cache

```bash
cd /home/username
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Then optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 8: Configure .htaccess

Ensure `/home/username/public_html/.htaccess` contains:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

## ✅ Post-Deployment Verification

### Test Checklist
- [ ] Website loads: `https://yourdomain.com`
- [ ] Login works
- [ ] Upload an image (test product/profile)
- [ ] Image displays correctly
- [ ] Check all navigation links
- [ ] Test forms and submissions
- [ ] Verify database connections
- [ ] Test file downloads
- [ ] Check console for JS errors
- [ ] Test on mobile device

### Image Testing
1. **Upload test_storage.php to public_html**
2. Visit: `https://yourdomain.com/test_storage.php`
3. Verify all checks pass
4. **DELETE the file!**

### Common URLs to Test
- [ ] Homepage: `/`
- [ ] Login: `/login`
- [ ] Dashboard: `/dashboard`
- [ ] Products: `/master-data/products`
- [ ] Sales: `/sales/orders`
- [ ] Purchases: `/purchases/purchase-orders`
- [ ] Reports: `/reports`
- [ ] Storage: `/storage/test.jpg` (if you have test image)

---

## 🐛 Troubleshooting

### Issue: "500 Internal Server Error"
**Solutions:**
```bash
# Check permissions
chmod -R 755 /home/username
chmod -R 775 /home/username/storage
chmod -R 775 /home/username/bootstrap/cache

# Check error logs
tail -f /home/username/logs/laravel.log
```

### Issue: Images Not Displaying
**Solutions:**
1. Run `create_symlink.php` OR `php artisan storage:link`
2. Verify APP_URL in `.env`
3. Check .htaccess rules
4. Clear browser cache
5. See `HOSTINGER_IMAGE_FIX_GUIDE.md`

### Issue: "No application encryption key"
**Solutions:**
```bash
php artisan key:generate
# OR use generate_key.php helper
```

### Issue: CSS/JS Not Loading
**Solutions:**
1. Run `npm run build` locally before upload
2. Check ASSET_URL in `.env`
3. Clear Laravel caches
4. Check browser console for 404s

### Issue: Database Connection Failed
**Solutions:**
1. Verify DB credentials in `.env`
2. Ensure database exists in Hostinger
3. Check DB_HOST (usually `localhost`)
4. Test connection via phpMyAdmin

### Issue: "Symlink creation failed"
**Solutions:**
1. Your hosting plan may not support symlinks
2. Use route-based storage (see guide)
3. Contact Hostinger support
4. Or manually copy files to public/storage

---

## 🔐 Security Hardening

### After Deployment
- [ ] Set `APP_DEBUG=false`
- [ ] Delete test/debug files (`test_storage.php`, `create_symlink.php`, etc.)
- [ ] Review file permissions (nothing should be 777)
- [ ] Enable HTTPS/SSL certificate
- [ ] Configure proper CORS if needed
- [ ] Set up regular database backups
- [ ] Monitor error logs regularly

### Files to DELETE from Production
- [ ] `create_symlink.php`
- [ ] `test_storage.php`
- [ ] `generate_key.php`
- [ ] `phpinfo.php` (if created)
- [ ] Any `check_*.php` files
- [ ] `README.md` (optional)
- [ ] `.git` folder (if uploaded)

---

## 📞 Getting Help

### Hostinger Support
- Email: support@hostinger.com
- Live Chat: Available 24/7 in control panel

### Laravel Documentation
- https://laravel.com/docs/11.x/deployment

### Common Log Locations
- Laravel: `/home/username/storage/logs/laravel.log`
- PHP: `/home/username/logs/error_log`
- Web server: Check Hostinger control panel

---

## 🎯 Quick Command Reference

```bash
# Navigate to project
cd /home/username

# Clear everything
php artisan optimize:clear

# Cache everything
php artisan optimize

# Storage link
php artisan storage:link

# Database
php artisan migrate --force
php artisan db:seed --force

# Permissions
chmod -R 775 storage bootstrap/cache
chown -R $USER:$USER storage bootstrap/cache
```

---

**🎉 Congratulations! Your Laravel app should now be live on Hostinger!**

If you encounter any issues, refer to:
- `HOSTINGER_IMAGE_FIX_GUIDE.md` for image problems
- Hostinger's knowledge base
- Laravel's deployment documentation
