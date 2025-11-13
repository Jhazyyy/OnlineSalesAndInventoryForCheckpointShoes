# Hostinger Deployment - Image Fix Guide

## The Problem
When deploying Laravel to Hostinger, uploaded images show as broken because:
1. ❌ The storage symlink is missing
2. ❌ File permissions are incorrect
3. ❌ The `.htaccess` might not be configured properly
4. ❌ The `APP_URL` might be wrong in production

## ✅ Complete Fix for Hostinger

### Step 1: Update Your .env File on Hostinger

```env
# Update these values for your Hostinger domain
APP_URL=https://yourdomain.com
ASSET_URL=https://yourdomain.com

# Make sure this is set to 'public'
FILESYSTEM_DISK=public
```

### Step 2: Create the Storage Symlink

**Option A: Via SSH (Recommended)**
```bash
cd /home/your_username/public_html
php artisan storage:link
```

**Option B: Via File Manager (If no SSH access)**
Create a file called `create_symlink.php` in your `public_html` root:

```php
<?php
// create_symlink.php
$target = __DIR__ . '/../storage/app/public';
$link = __DIR__ . '/storage';

if (file_exists($link)) {
    echo "Symlink already exists at: $link\n";
} else {
    if (symlink($target, $link)) {
        echo "✓ Symlink created successfully!\n";
        echo "Target: $target\n";
        echo "Link: $link\n";
    } else {
        echo "✗ Failed to create symlink. Try manual creation.\n";
    }
}
?>
```

Then visit: `https://yourdomain.com/create_symlink.php`

After successful creation, **DELETE this file for security!**

### Step 3: Fix File Permissions

Via SSH or File Manager, set these permissions:

```bash
# Storage directories
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Public storage directory
chmod -R 775 public/storage
```

### Step 4: Update Your .htaccess File

Make sure your `public/.htaccess` has this:

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

    # Allow access to storage symlink
    RewriteCond %{REQUEST_URI} ^/storage/
    RewriteRule ^(.*)$ $1 [L]
</IfModule>
```

### Step 5: Alternative - Direct Storage Access (If Symlink Fails)

If symlink doesn't work on your Hostinger plan, update your code:

**Change this:**
```blade
<img src="{{ asset('storage/' . $image) }}">
```

**To this:**
```blade
<img src="{{ url('storage/' . $image) }}">
```

### Step 6: Create a Storage Route (Fallback Option)

Add to `routes/web.php`:

```php
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

// Fallback route if symlink doesn't work
Route::get('/storage/{path}', function ($path) {
    $file = Storage::disk('public')->get($path);
    $mimeType = Storage::disk('public')->mimeType($path);
    
    return Response::make($file, 200, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*')->name('storage.file');
```

## 🔧 Testing Your Fix

### 1. Test Symlink Creation
Create `test_storage.php` in your public_html:

```php
<?php
echo "<h2>Storage Link Test</h2>";

$link = __DIR__ . '/storage';
$target = __DIR__ . '/../storage/app/public';

echo "Link path: $link<br>";
echo "Target path: $target<br><br>";

if (is_link($link)) {
    echo "✓ Symlink exists!<br>";
    echo "Points to: " . readlink($link) . "<br>";
    
    if (is_dir(readlink($link))) {
        echo "✓ Target directory exists and is accessible!<br>";
        
        // List files in storage/app/public
        $files = scandir($target);
        echo "<br>Files in storage:<br>";
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                echo "- $file<br>";
            }
        }
    } else {
        echo "✗ Target directory not accessible!<br>";
    }
} else {
    echo "✗ Symlink does not exist!<br>";
    echo "Run: php artisan storage:link<br>";
}
?>
```

Visit: `https://yourdomain.com/test_storage.php`

### 2. Test Image Upload
1. Upload an image through your system
2. Check if it appears in File Manager at: `/storage/app/public/`
3. Try accessing it directly: `https://yourdomain.com/storage/filename.jpg`

## 📁 Hostinger Directory Structure

Your files should be organized like this:

```
/home/your_username/
├── public_html/              ← Laravel's public folder content goes here
│   ├── index.php
│   ├── .htaccess
│   ├── storage/              ← SYMLINK to ../storage/app/public
│   └── ...
├── storage/                  ← Laravel's storage folder
│   ├── app/
│   │   └── public/          ← Uploaded files go here
│   │       ├── products/
│   │       ├── profile_photos/
│   │       ├── customers/
│   │       └── ...
│   ├── framework/
│   └── logs/
├── app/
├── bootstrap/
├── config/
└── ...
```

## 🚨 Common Hostinger Issues & Solutions

### Issue 1: "Symbolic links are disabled"
**Solution:** Use the route-based storage access (Step 6 above)

### Issue 2: Images work locally but not in production
**Solution:** 
- Clear cache: `php artisan cache:clear`
- Clear config: `php artisan config:clear`
- Regenerate config: `php artisan config:cache`

### Issue 3: 404 errors on storage files
**Solution:** Check your `.htaccess` file includes the storage rewrite rule

### Issue 4: Permission denied errors
**Solution:**
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R your_username:your_username storage
```

## 🔐 Security Checklist

After deployment:
- ✅ Delete `create_symlink.php`
- ✅ Delete `test_storage.php`
- ✅ Make sure `.env` is not publicly accessible
- ✅ Verify `storage/` folder is outside `public_html/`

## 📝 Quick Deployment Commands

Run these in order via SSH:

```bash
# Navigate to your app directory
cd /home/your_username/public_html

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Create storage link
php artisan storage:link

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Fix permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

## 🎯 Final Verification Checklist

- [ ] Symlink created: `public/storage` → `storage/app/public`
- [ ] `.env` has correct `APP_URL`
- [ ] `.htaccess` is properly configured
- [ ] Permissions are set (775 for storage)
- [ ] Test image upload works
- [ ] Test image display in browser
- [ ] Clear all Laravel caches
- [ ] Remove test/debug files

---

## Need Help?

If images still don't work after following all steps:

1. Check Hostinger's error logs: `/home/your_username/logs/`
2. Enable Laravel debug: `APP_DEBUG=true` (temporarily)
3. Check browser console for 404 errors
4. Verify file actually exists in `/storage/app/public/`

Contact me with the specific error message for more help!
