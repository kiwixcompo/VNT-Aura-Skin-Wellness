<?php
/**
 * GitHub Auto-Deploy Script for VNT Aura Skin & Wellness
 * 
 * This script automatically downloads the latest changes from GitHub via API
 * when triggered by sync.bat (manual execution) or a GitHub webhook.
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration
$config = [
    'repo_url' => 'https://github.com/kiwixcompo/VNT-Aura-Skin-Wellness.git',
    'branch' => 'main',
    'deploy_path' => __DIR__, // Current directory where this script is located
    'secret_key' => 'VNTAura2026SecureKey!@#$%', // Secure webhook secret
    'log_file' => __DIR__ . '/deploy.log',
    'backup_dir' => __DIR__ . '/backups',
    'allowed_ips' => [
        '140.82.112.0/20',    // GitHub webhook IPs
        '185.199.108.0/22',   // GitHub webhook IPs
        '192.30.252.0/22',    // GitHub webhook IPs
        '127.0.0.1',          // Localhost for manual testing
    ]
];

// Security check for webhook requests
function isValidRequest($config) {
    // For manual requests (from sync.bat), allow them
    if (isset($_GET['manual']) && $_GET['manual'] === 'true') {
        return true;
    }
    
    // For webhook requests, verify signature
    if (isset($_SERVER['HTTP_X_HUB_SIGNATURE_256'])) {
        $payload = file_get_contents('php://input');
        $signature = hash_hmac('sha256', $payload, $config['secret_key']);
        $expected_signature = 'sha256=' . $signature;
        
        if (!hash_equals($expected_signature, $_SERVER['HTTP_X_HUB_SIGNATURE_256'])) {
            return false;
        }
    }
    
    return true;
}

// Logging function
function logMessage($message, $config) {
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] $message" . PHP_EOL;
    @file_put_contents($config['log_file'], $log_entry, FILE_APPEND | LOCK_EX);
}

// Create backup before deployment
function createBackup($config) {
    try {
        if (!is_dir($config['backup_dir'])) {
            if (!mkdir($config['backup_dir'], 0755, true)) {
                logMessage("Failed to create backup directory", $config);
                return false;
            }
        }
        
        $backup_name = 'backup_' . date('Y-m-d_H-i-s') . '.txt';
        $backup_path = $config['backup_dir'] . '/' . $backup_name;
        
        $backup_info = [
            'timestamp' => date('Y-m-d H:i:s'),
            'files_count' => count(glob('*')),
            'directory' => getcwd()
        ];
        
        file_put_contents($backup_path, json_encode($backup_info, JSON_PRETTY_PRINT));
        logMessage("Backup created: $backup_name", $config);
        
        // Keep only last 10 backups
        $backups = glob($config['backup_dir'] . '/backup_*.txt');
        if (count($backups) > 10) {
            usort($backups, function($a, $b) {
                return filemtime($b) - filemtime($a);
            });
            for ($i = 10; $i < count($backups); $i++) {
                @unlink($backups[$i]);
            }
        }
        
        return true;
    } catch (Exception $e) {
        logMessage("Backup failed: " . $e->getMessage(), $config);
        return false;
    }
}

// Deployment using GitHub API (for shared hosting without Git)
function deployWithoutGit($config) {
    logMessage("=== DEPLOYMENT STARTED (GitHub API Method) ===", $config);
    
    createBackup($config);
    
    // GitHub API URL to get repository contents
    $api_url = "https://api.github.com/repos/kiwixcompo/VNT-Aura-Skin-Wellness/zipball/{$config['branch']}";
    
    logMessage("Downloading repository from GitHub API: {$api_url}", $config);
    
    // Download the repository as a ZIP file
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => [
                'User-Agent: VNTAura-Deploy/1.0'
            ],
            'timeout' => 60
        ]
    ]);
    
    $zip_content = @file_get_contents($api_url, false, $context);
    
    if ($zip_content === false) {
        logMessage("ERROR: Failed to download repository from GitHub API", $config);
        return false;
    }
    
    // Save ZIP file temporarily
    $temp_zip = $config['deploy_path'] . '/temp_deploy.zip';
    if (file_put_contents($temp_zip, $zip_content) === false) {
        logMessage("ERROR: Failed to save temporary ZIP file", $config);
        return false;
    }
    
    logMessage("Repository downloaded successfully, extracting files", $config);
    
    // Extract ZIP file
    if (class_exists('ZipArchive')) {
        $zip = new ZipArchive;
        if ($zip->open($temp_zip) === TRUE) {
            $temp_dir = $config['deploy_path'] . '/temp_extract';
            if (!is_dir($temp_dir)) {
                mkdir($temp_dir, 0755, true);
            }
            
            $zip->extractTo($temp_dir);
            $zip->close();
            
            $extracted_folders = glob($temp_dir . '/*', GLOB_ONLYDIR);
            if (empty($extracted_folders)) {
                logMessage("ERROR: No extracted folder found", $config);
                @unlink($temp_zip);
                return false;
            }
            
            $source_dir = $extracted_folders[0];
            
            // Copy files from extracted folder to deployment directory
            if (copyDirectory($source_dir, $config['deploy_path'])) {
                logMessage("Files copied successfully", $config);
                
                // Clean up temporary files
                @unlink($temp_zip);
                removeDirectory($temp_dir);
                
                logMessage("=== DEPLOYMENT COMPLETED SUCCESSFULLY ===", $config);
                return true;
            } else {
                logMessage("ERROR: Failed to copy files", $config);
                @unlink($temp_zip);
                removeDirectory($temp_dir);
                return false;
            }
        } else {
            logMessage("ERROR: Failed to open ZIP file", $config);
            @unlink($temp_zip);
            return false;
        }
    } else {
        logMessage("ERROR: ZipArchive class not available", $config);
        @unlink($temp_zip);
        return false;
    }
}

// Copy directory recursively
function copyDirectory($source, $destination) {
    if (!is_dir($source)) {
        return false;
    }
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($iterator as $item) {
        $target = $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
        
        if ($item->isDir()) {
            if (!is_dir($target)) {
                @mkdir($target, 0755, true);
            }
        } else {
            // Skip files that must never be overwritten on the server
            $subPath = $iterator->getSubPathName();
            $skipPaths = [
                'deploy.php', 
                'deploy.log',
                'includes/database.sqlite', // VERY IMPORTANT: Prevent overwriting live bookings with local DB!
                'public/verification_codes.txt',
                'sync.bat'
            ];
            
            // Normalise path separators for comparison
            $normPath = str_replace('\\', '/', $subPath);
            
            if (in_array(basename($target), ['deploy.php', 'deploy.log'])) continue;
            if (in_array($normPath, $skipPaths)) continue;
            if (strpos($normPath, 'storage/emails/') === 0) continue; // Skip local dev emails
            if (strpos($normPath, 'uploads/') === 0) continue; // Skip overwriting live uploads
            if (strpos($normPath, '.git/') === 0) continue;
            
            @copy($item, $target);
        }
    }
    
    return true;
}

// Remove directory recursively
function removeDirectory($dir) {
    if (!is_dir($dir)) {
        return false;
    }
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    
    foreach ($iterator as $item) {
        if ($item->isDir()) {
            @rmdir($item);
        } else {
            @unlink($item);
        }
    }
    
    @rmdir($dir);
    return true;
}

// Handle the request
try {
    if (!isValidRequest($config)) {
        http_response_code(403);
        logMessage("ERROR: Unauthorized deployment attempt", $config);
        die('Unauthorized');
    }
    
    $is_manual = isset($_GET['manual']) && $_GET['manual'] === 'true';
    $is_webhook = isset($_SERVER['HTTP_X_GITHUB_EVENT']);
    
    if ($is_manual) {
        logMessage("Manual deployment triggered", $config);
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>VNT Aura - Deployment</title>
            <style>
                body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
                .header { background: #455A44; color: white; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
                .status { padding: 15px; border-radius: 5px; margin: 10px 0; }
                .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
                .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
                .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
                .log { background: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; border-radius: 5px; font-family: monospace; white-space: pre-wrap; }
                .links { margin-top: 20px; }
                .links a { display: inline-block; margin-right: 15px; padding: 10px 15px; background: #B4975A; color: white; text-decoration: none; border-radius: 3px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>🚀 VNT Aura - Auto Deployment</h1>
                <p>Repository: https://github.com/kiwixcompo/VNT-Aura-Skin-Wellness</p>
            </div>
            
            <div class="status info">
                <strong>📋 Deployment Status:</strong> Starting manual deployment...
            </div>
        <?php
        flush();
        
        echo '<div class="status info">📦 Using GitHub API deployment method...</div>';
        flush();
        $success = deployWithoutGit($config);
        
        if ($success) {
            echo '<div class="status success"><strong>✅ Success!</strong> Deployment completed successfully!</div>';
        } else {
            echo '<div class="status error"><strong>❌ Failed!</strong> Deployment encountered errors.</div>';
        }
        
        if (file_exists($config['log_file'])) {
            $log_content = file_get_contents($config['log_file']);
            $recent_logs = implode("\n", array_slice(explode("\n", $log_content), -20));
            echo '<h3>📋 Recent Deployment Log:</h3>';
            echo '<div class="log">' . htmlspecialchars($recent_logs) . '</div>';
        }
        ?>
            <div class="links">
                <a href="/">🌐 View Website</a>
                <a href="?manual=true">🔄 Deploy Again</a>
                <a href="https://github.com/kiwixcompo/VNT-Aura-Skin-Wellness">📊 View Repository</a>
            </div>
        </body>
        </html>
        <?php
        
    } elseif ($is_webhook) {
        $event = $_SERVER['HTTP_X_GITHUB_EVENT'] ?? 'unknown';
        logMessage("Webhook deployment triggered: $event", $config);
        
        if ($event !== 'push') {
            echo "OK - Event ignored";
            exit;
        }
        
        $payload = json_decode(file_get_contents('php://input'), true);
        if (isset($payload['ref']) && $payload['ref'] !== 'refs/heads/' . $config['branch']) {
            echo "OK - Branch ignored";
            exit;
        }
        
        $success = deployWithoutGit($config);
        
        if ($success) {
            echo "OK - Deployment successful";
        } else {
            http_response_code(500);
            echo "ERROR - Deployment failed";
        }
    } else {
        http_response_code(400);
        die('Invalid request');
    }
    
} catch (Exception $e) {
    logMessage("EXCEPTION: " . $e->getMessage(), $config);
    http_response_code(500);
    echo "ERROR - Exception occurred";
}
?>
