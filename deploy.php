<?php
/**
 * Advanced PHP Git Deploy Script (cPanel compatible)
 * This script is triggered by sync.bat (via curl) to pull changes 
 * into the cPanel repository and sync them to public_html using pure PHP.
 */

if (!isset($_GET['manual']) && empty($_SERVER['HTTP_X_GITHUB_EVENT'])) {
    http_response_code(403);
    die("Forbidden");
}

$public_html_dir = __DIR__;
// Direct path resolution for cPanel (avoids symlink/realpath issues)
$repo_dir = dirname(__DIR__) . '/repositories/VNT-Aura-Skin-Wellness';

$log = date('Y-m-d H:i:s') . " - Deployment triggered.\n";

/**
 * Pure PHP recursive directory copy
 */
function copy_directory($src, $dst, $exclude = []) {
    global $log;
    
    if (!is_dir($src)) {
        $log .= "ERROR: Source directory {$src} does not exist.\n";
        return false;
    }
    
    $dir = opendir($src);
    @mkdir($dst, 0755, true);
    
    $success = true;
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..') && !in_array($file, $exclude)) {
            $srcPath = $src . '/' . $file;
            $dstPath = $dst . '/' . $file;
            
            if (is_dir($srcPath)) {
                if (!copy_directory($srcPath, $dstPath, $exclude)) {
                    $success = false;
                }
            } else {
                if (!@copy($srcPath, $dstPath)) {
                    $log .= "Failed to copy: {$srcPath} to {$dstPath}\n";
                    $success = false;
                }
            }
        }
    }
    closedir($dir);
    return $success;
}

if (is_dir($repo_dir)) {
    // 1. Try to pull latest changes (If exec is enabled)
    if (function_exists('exec')) {
        exec("cd {$repo_dir} && git pull origin main 2>&1", $output, $return_var);
        $log .= "GIT PULL:\n" . implode("\n", $output) . "\n";
    } else {
        $log .= "exec() is disabled. Assuming cPanel auto-pulled the repo.\n";
    }
    
    // 2. Sync files using Pure PHP (bypasses exec/rsync restrictions)
    $log .= "Starting Pure PHP Sync from {$repo_dir} to {$public_html_dir}...\n";
    
    if (copy_directory($repo_dir, $public_html_dir, ['.git', '.gitignore'])) {
        $log .= "PHP Sync Completed Successfully!\n";
        $status = 200;
        $msg = "Deployment and sync to public_html successful!";
    } else {
        $log .= "PHP Sync encountered errors.\n";
        $status = 500;
        $msg = "Sync failed partially. Check deploy.log.";
    }
} else {
    // Fallback if the repositories folder isn't found
    $log .= "ERROR: Repository folder not found at {$repo_dir}\n";
    if (function_exists('exec')) {
        exec("cd {$public_html_dir} && git pull origin main 2>&1", $output, $return_var);
        $log .= "DIRECT GIT PULL:\n" . implode("\n", $output) . "\n";
    }
    $status = 500;
    $msg = "Repository folder not found. Check deploy.log.";
}

$log .= "-------------------------\n";
file_put_contents(__DIR__ . '/deploy.log', $log, FILE_APPEND);

http_response_code($status);
echo $msg . "\n";
?>
