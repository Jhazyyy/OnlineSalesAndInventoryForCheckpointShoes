<?php
/**
 * Test Storage Configuration for Hostinger
 * 
 * Upload this to public_html and visit to verify your storage setup.
 * DELETE after testing!
 */

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Storage Configuration Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; }
        .success { color: green; background: #d4edda; padding: 10px; border-left: 4px solid green; margin: 10px 0; }
        .error { color: red; background: #f8d7da; padding: 10px; border-left: 4px solid red; margin: 10px 0; }
        .warning { color: orange; background: #fff3cd; padding: 10px; border-left: 4px solid orange; margin: 10px 0; }
        .info { color: blue; background: #d1ecf1; padding: 10px; border-left: 4px solid blue; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
        th { background: #f4f4f4; }
        code { background: #f4f4f4; padding: 2px 5px; border-radius: 3px; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .test-section { margin: 30px 0; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>🔍 Storage Configuration Test</h1>
";

// Test 1: Check Symlink
echo "<div class='test-section'>";
echo "<h2>1️⃣ Symlink Test</h2>";
$link = __DIR__ . '/storage';
$target = __DIR__ . '/../storage/app/public';

if (file_exists($link)) {
    if (is_link($link)) {
        $currentTarget = readlink($link);
        echo "<div class='success'>✅ Symlink exists at: <code>$link</code></div>";
        echo "<div class='info'>Points to: <code>$currentTarget</code></div>";
        
        if (is_dir($currentTarget)) {
            echo "<div class='success'>✅ Target directory is accessible</div>";
        } else {
            echo "<div class='error'>❌ Target directory is NOT accessible</div>";
        }
    } else {
        echo "<div class='error'>❌ 'storage' exists but is NOT a symlink</div>";
    }
} else {
    echo "<div class='error'>❌ Symlink does NOT exist</div>";
    echo "<div class='warning'>Run <code>php artisan storage:link</code> or use <code>create_symlink.php</code></div>";
}
echo "</div>";

// Test 2: Directory Permissions
echo "<div class='test-section'>";
echo "<h2>2️⃣ Directory Permissions Test</h2>";
$directories = [
    'Public Storage Link' => $link,
    'Storage App Public' => $target,
    'Storage Root' => __DIR__ . '/../storage',
    'Bootstrap Cache' => __DIR__ . '/../bootstrap/cache',
];

echo "<table>";
echo "<tr><th>Directory</th><th>Path</th><th>Exists</th><th>Writable</th><th>Readable</th></tr>";
foreach ($directories as $name => $path) {
    $exists = file_exists($path);
    $writable = is_writable($path);
    $readable = is_readable($path);
    
    echo "<tr>";
    echo "<td><strong>$name</strong></td>";
    echo "<td><code>" . htmlspecialchars($path) . "</code></td>";
    echo "<td>" . ($exists ? "✅ Yes" : "❌ No") . "</td>";
    echo "<td>" . ($writable ? "✅ Yes" : "❌ No") . "</td>";
    echo "<td>" . ($readable ? "✅ Yes" : "❌ No") . "</td>";
    echo "</tr>";
}
echo "</table>";

if (file_exists($target) && !is_writable($target)) {
    echo "<div class='warning'>";
    echo "⚠️ Storage directory is not writable! Run:<br>";
    echo "<code>chmod -R 775 storage</code>";
    echo "</div>";
}
echo "</div>";

// Test 3: Storage Subdirectories
echo "<div class='test-section'>";
echo "<h2>3️⃣ Storage Subdirectories</h2>";
if (is_dir($target)) {
    $subdirs = ['products', 'profile_photos', 'customers/avatars', 'packages', 'payment_proofs', 'logos'];
    
    echo "<table>";
    echo "<tr><th>Subdirectory</th><th>Full Path</th><th>Exists</th><th>Writable</th></tr>";
    foreach ($subdirs as $subdir) {
        $fullPath = $target . '/' . $subdir;
        $exists = file_exists($fullPath);
        $writable = is_writable($fullPath);
        
        echo "<tr>";
        echo "<td><strong>$subdir</strong></td>";
        echo "<td><code>" . htmlspecialchars($fullPath) . "</code></td>";
        echo "<td>" . ($exists ? "✅ Yes" : "⚠️ Will be created on upload") . "</td>";
        echo "<td>" . ($writable ? "✅ Yes" : ($exists ? "❌ No" : "-")) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<div class='error'>❌ Cannot scan storage directory</div>";
}
echo "</div>";

// Test 4: Sample Files
echo "<div class='test-section'>";
echo "<h2>4️⃣ Sample Files in Storage</h2>";
if (is_dir($target)) {
    $allFiles = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($target, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile() && $iterator->getDepth() <= 2) {
            $relativePath = str_replace($target . '/', '', $file->getPathname());
            $allFiles[] = [
                'path' => $relativePath,
                'size' => $file->getSize(),
                'modified' => date('Y-m-d H:i:s', $file->getMTime())
            ];
        }
    }
    
    if (count($allFiles) > 0) {
        echo "<div class='success'>✅ Found " . count($allFiles) . " file(s) in storage</div>";
        echo "<table>";
        echo "<tr><th>File Path</th><th>Size</th><th>Last Modified</th></tr>";
        foreach (array_slice($allFiles, 0, 10) as $file) {
            echo "<tr>";
            echo "<td><code>" . htmlspecialchars($file['path']) . "</code></td>";
            echo "<td>" . number_format($file['size'] / 1024, 2) . " KB</td>";
            echo "<td>" . $file['modified'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        if (count($allFiles) > 10) {
            echo "<p><em>Showing first 10 of " . count($allFiles) . " files</em></p>";
        }
    } else {
        echo "<div class='info'>ℹ️ No files found in storage (this is normal for new installations)</div>";
    }
} else {
    echo "<div class='error'>❌ Cannot read storage directory</div>";
}
echo "</div>";

// Test 5: URL Configuration
echo "<div class='test-section'>";
echo "<h2>5️⃣ URL Configuration</h2>";
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$baseUrl = $protocol . '://' . $host;

echo "<table>";
echo "<tr><th>Configuration</th><th>Value</th></tr>";
echo "<tr><td><strong>Current URL</strong></td><td><code>$baseUrl</code></td></tr>";
echo "<tr><td><strong>Storage URL</strong></td><td><code>$baseUrl/storage</code></td></tr>";
echo "<tr><td><strong>Expected in .env</strong></td><td><code>APP_URL=$baseUrl</code></td></tr>";
echo "</table>";

echo "<div class='info'>";
echo "ℹ️ Make sure your <code>.env</code> file has:<br>";
echo "<pre>APP_URL=$baseUrl\nASSET_URL=$baseUrl\nFILESYSTEM_DISK=public</pre>";
echo "</div>";
echo "</div>";

// Test 6: Image Access Test
echo "<div class='test-section'>";
echo "<h2>6️⃣ Image Access Test</h2>";
if (is_dir($target)) {
    $testImage = null;
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($target, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $ext = strtolower($file->getExtension());
            if (in_array($ext, $imageExtensions)) {
                $testImage = str_replace($target . '/', '', $file->getPathname());
                break;
            }
        }
    }
    
    if ($testImage) {
        $imageUrl = $baseUrl . '/storage/' . $testImage;
        echo "<div class='success'>✅ Found test image: <code>$testImage</code></div>";
        echo "<p>Try accessing: <a href='$imageUrl' target='_blank'>$imageUrl</a></p>";
        echo "<p>If you see a 404 error, check your .htaccess configuration.</p>";
    } else {
        echo "<div class='info'>ℹ️ No images found to test. Upload an image first.</div>";
    }
} else {
    echo "<div class='error'>❌ Cannot access storage directory</div>";
}
echo "</div>";

// Summary
echo "<div class='test-section'>";
echo "<h2>📋 Summary & Recommendations</h2>";

$issues = [];
if (!file_exists($link) || !is_link($link)) {
    $issues[] = "Create storage symlink";
}
if (!is_writable($target)) {
    $issues[] = "Fix storage directory permissions (chmod -R 775 storage)";
}

if (count($issues) > 0) {
    echo "<div class='error'>";
    echo "<strong>❌ Issues Found:</strong><br>";
    foreach ($issues as $issue) {
        echo "• $issue<br>";
    }
    echo "</div>";
} else {
    echo "<div class='success'>";
    echo "✅ <strong>All storage checks passed!</strong><br>";
    echo "Your storage configuration appears to be correct.";
    echo "</div>";
}

echo "<div class='warning'>";
echo "<strong>🔐 SECURITY REMINDER:</strong><br>";
echo "DELETE this file (<code>test_storage.php</code>) after testing!";
echo "</div>";
echo "</div>";

echo "
    <h2>🔧 Quick Fixes</h2>
    <pre>
# Create symlink
php artisan storage:link

# Fix permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
    </pre>
</body>
</html>";
?>
