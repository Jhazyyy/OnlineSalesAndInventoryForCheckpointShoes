<?php
/**
 * Generate Laravel Application Key
 * 
 * Upload this to public_html if you don't have SSH access.
 * Visit once to generate key, then DELETE immediately!
 */

// Change to Laravel root directory
chdir(__DIR__ . '/..');

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Generate Application Key</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: green; background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { color: red; background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .warning { color: orange; background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0; }
        code { background: #f4f4f4; padding: 2px 5px; border-radius: 3px; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔑 Generate Laravel Application Key</h1>
";

try {
    // Check if .env file exists
    if (!file_exists('.env')) {
        echo "<div class='error'>";
        echo "<strong>❌ Error: .env file not found!</strong><br>";
        echo "Please create your .env file first by copying .env.example";
        echo "</div>";
    } else {
        // Load .env file
        $envContent = file_get_contents('.env');
        
        // Check if APP_KEY already exists and is set
        if (preg_match('/^APP_KEY=(.+)$/m', $envContent, $matches)) {
            $currentKey = trim($matches[1]);
            if (!empty($currentKey) && $currentKey !== 'base64:') {
                echo "<div class='warning'>";
                echo "<strong>⚠️ Warning: APP_KEY already exists!</strong><br>";
                echo "Current key: <code>" . substr($currentKey, 0, 20) . "...</code><br><br>";
                echo "If you generate a new key, all encrypted data will become unreadable!<br>";
                echo "Only proceed if this is a fresh installation or you know what you're doing.";
                echo "</div>";
            }
        }
        
        // Generate new key
        require 'vendor/autoload.php';
        $app = require_once 'bootstrap/app.php';
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        
        // Capture output
        ob_start();
        $status = $kernel->call('key:generate', ['--show' => true]);
        $output = ob_get_clean();
        
        if ($status === 0) {
            // Extract the key from output
            if (preg_match('/base64:[A-Za-z0-9+\/=]+/', $output, $matches)) {
                $newKey = $matches[0];
                
                // Update .env file
                if (preg_match('/^APP_KEY=(.*)$/m', $envContent)) {
                    $envContent = preg_replace('/^APP_KEY=(.*)$/m', 'APP_KEY=' . $newKey, $envContent);
                } else {
                    $envContent .= "\nAPP_KEY=" . $newKey . "\n";
                }
                
                file_put_contents('.env', $envContent);
                
                echo "<div class='success'>";
                echo "<strong>✅ Application key generated successfully!</strong><br><br>";
                echo "New key: <code>" . htmlspecialchars($newKey) . "</code><br><br>";
                echo "The key has been saved to your .env file.";
                echo "</div>";
                
                echo "<div class='warning'>";
                echo "<strong>🔐 CRITICAL: Security Step</strong><br>";
                echo "DELETE this file (<code>generate_key.php</code>) IMMEDIATELY!<br>";
                echo "Leaving it accessible is a serious security risk!";
                echo "</div>";
                
                echo "<div class='success'>";
                echo "<strong>✅ Next Steps:</strong><br>";
                echo "1. Delete this file<br>";
                echo "2. Clear configuration cache: <code>php artisan config:clear</code><br>";
                echo "3. Test your application login";
                echo "</div>";
            } else {
                echo "<div class='error'>";
                echo "<strong>❌ Could not extract key from output</strong><br>";
                echo "Output: <pre>" . htmlspecialchars($output) . "</pre>";
                echo "</div>";
            }
        } else {
            echo "<div class='error'>";
            echo "<strong>❌ Failed to generate key!</strong><br>";
            echo "Status code: $status<br>";
            echo "Output: <pre>" . htmlspecialchars($output) . "</pre>";
            echo "</div>";
        }
    }
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<strong>❌ Error occurred:</strong><br>";
    echo htmlspecialchars($e->getMessage());
    echo "</div>";
    
    echo "<div class='warning'>";
    echo "<strong>Alternative Method:</strong><br>";
    echo "If you have SSH access, run:<br>";
    echo "<pre>cd /home/username\nphp artisan key:generate</pre>";
    echo "</div>";
}

echo "
    <h2>📝 Manual Method (if automatic fails)</h2>
    <p>1. Run this command on your local machine:</p>
    <pre>php artisan key:generate --show</pre>
    <p>2. Copy the generated key (starts with 'base64:')</p>
    <p>3. Add it to your .env file on Hostinger:</p>
    <pre>APP_KEY=base64:your_key_here</pre>
    <p>4. Clear cache: <code>php artisan config:clear</code></p>
</body>
</html>";
?>
