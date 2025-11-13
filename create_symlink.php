<?php
/**
 * Create Storage Symlink for Hostinger
 * 
 * Upload this file to your public_html folder and visit it once.
 * After successful creation, DELETE this file!
 */

$target = __DIR__ . '/../storage/app/public';
$link = __DIR__ . '/storage';

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Storage Symlink Creator</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: green; background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { color: red; background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { color: blue; background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 10px 0; }
        code { background: #f4f4f4; padding: 2px 5px; border-radius: 3px; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔗 Storage Symlink Creator for Hostinger</h1>
";

// Check if symlink already exists
if (file_exists($link)) {
    if (is_link($link)) {
        $currentTarget = readlink($link);
        echo "<div class='info'>";
        echo "<strong>ℹ️ Symlink already exists!</strong><br>";
        echo "Location: <code>$link</code><br>";
        echo "Points to: <code>$currentTarget</code><br><br>";
        
        if ($currentTarget === $target) {
            echo "✅ Symlink is correctly configured!";
        } else {
            echo "⚠️ Symlink points to wrong location!<br>";
            echo "Expected: <code>$target</code><br>";
            echo "You may need to delete the existing symlink and recreate it.";
        }
        echo "</div>";
    } else {
        echo "<div class='error'>";
        echo "<strong>❌ Error: A file or folder named 'storage' already exists!</strong><br>";
        echo "Please rename or remove: <code>$link</code><br>";
        echo "Then refresh this page.";
        echo "</div>";
    }
} else {
    // Check if target directory exists
    if (!is_dir($target)) {
        echo "<div class='error'>";
        echo "<strong>❌ Error: Target directory doesn't exist!</strong><br>";
        echo "Expected at: <code>$target</code><br>";
        echo "Please ensure Laravel is properly uploaded to Hostinger.";
        echo "</div>";
    } else {
        // Try to create the symlink
        if (symlink($target, $link)) {
            echo "<div class='success'>";
            echo "<strong>✅ Symlink created successfully!</strong><br>";
            echo "Source: <code>$link</code><br>";
            echo "Target: <code>$target</code><br><br>";
            echo "Your uploaded images should now display correctly!";
            echo "</div>";
            
            echo "<div class='info'>";
            echo "<strong>🔐 IMPORTANT: Security Step</strong><br>";
            echo "Please <strong>DELETE</strong> this file (<code>create_symlink.php</code>) immediately for security reasons!";
            echo "</div>";
        } else {
            echo "<div class='error'>";
            echo "<strong>❌ Failed to create symlink!</strong><br><br>";
            echo "<strong>This usually means:</strong><br>";
            echo "1. PHP doesn't have permission to create symlinks<br>";
            echo "2. Your hosting plan doesn't support symlinks<br><br>";
            echo "<strong>Alternative Solution:</strong><br>";
            echo "Contact Hostinger support or use the route-based storage access method described in HOSTINGER_IMAGE_FIX_GUIDE.md";
            echo "</div>";
            
            echo "<div class='info'>";
            echo "<strong>📝 Manual Command (if you have SSH access):</strong><br>";
            echo "<pre>cd /home/your_username/public_html\nphp artisan storage:link</pre>";
            echo "</div>";
        }
    }
}

// Test symlink
if (is_link($link) && is_dir(readlink($link))) {
    $targetPath = readlink($link);
    $files = @scandir($targetPath);
    
    if ($files !== false) {
        echo "<div class='success'>";
        echo "<strong>✅ Symlink is functional!</strong><br><br>";
        echo "<strong>Directories in storage:</strong><br>";
        foreach ($files as $file) {
            if ($file != '.' && $file != '..' && is_dir($targetPath . '/' . $file)) {
                echo "📁 $file<br>";
            }
        }
        echo "</div>";
    }
}

echo "
    <h2>📋 Next Steps</h2>
    <ol>
        <li><strong>Delete this file</strong> (<code>create_symlink.php</code>)</li>
        <li>Clear Laravel caches:
            <pre>php artisan cache:clear\nphp artisan config:clear\nphp artisan view:clear</pre>
        </li>
        <li>Test uploading an image in your system</li>
        <li>Verify the image displays correctly</li>
    </ol>
    
    <h2>🔧 Troubleshooting</h2>
    <p>If images still don't display:</p>
    <ul>
        <li>Check file permissions: <code>chmod -R 775 storage</code></li>
        <li>Verify APP_URL in .env matches your domain</li>
        <li>Check browser console for 404 errors</li>
        <li>Refer to <strong>HOSTINGER_IMAGE_FIX_GUIDE.md</strong> for more solutions</li>
    </ul>
</body>
</html>";
?>
