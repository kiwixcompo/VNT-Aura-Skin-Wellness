<?php
/**
 * Advanced PHP Git Deploy Script (cPanel compatible)
 * This script is triggered by sync.bat (via curl) to pull changes 
 * into the cPanel repository and sync them to public_html.
 */

if (!isset($_GET['manual']) && empty($_SERVER['HTTP_X_GITHUB_EVENT'])) {
    http_response_code(403);
    die("Forbidden");
}

$public_html_dir = __DIR__;
// Assuming standard cPanel structure: public_html and repositories are side-by-side in /home/user/
$repo_dir = realpath(__DIR__ . '/../repositories/VNT-Aura-Skin-Wellness');

$log = date('Y-m-d H:i:s') . " - Deployment triggered.\n";
$output = [];
$return_var = 0;

if ($repo_dir && is_dir($repo_dir)) {
    // 1. Pull latest changes into the cPanel repository
    exec("cd {$repo_dir} && git pull origin main 2>&1", $output, $return_var);
    $log .= "GIT PULL:\n" . implode("\n", $output) . "\n";
    
    // 2. Sync the updated files from the repository to public_html (excluding .git)
    if ($return_var === 0) {
        $sync_output = [];
        $sync_return = 0;
        // Using rsync to mirror files securely
        exec("rsync -av --exclude='.git' {$repo_dir}/ {$public_html_dir}/ 2>&1", $sync_output, $sync_return);
        $log .= "\nRSYNC SYNC:\n" . implode("\n", $sync_output) . "\n";
        
        if ($sync_return !== 0) {
            // Fallback to cp if rsync is not available
            exec("cp -a {$repo_dir}/. {$public_html_dir}/ 2>&1", $cp_output, $cp_return);
            $log .= "\nCP FALLBACK SYNC:\n" . implode("\n", $cp_output) . "\n";
        }
    }
} else {
    // Fallback if the repositories folder isn't structured like cPanel yet (e.g., direct public_html git repo)
    exec("cd {$public_html_dir} && git pull origin main 2>&1", $output, $return_var);
    $log .= "DIRECT GIT PULL (No repositories folder found):\n" . implode("\n", $output) . "\n";
}

$log .= "-------------------------\n";
file_put_contents('deploy.log', $log, FILE_APPEND);

if ($return_var === 0) {
    echo "Deployment and sync to public_html successful!\n";
} else {
    http_response_code(500);
    echo "Deployment failed. Check deploy.log for details.\n";
}
?>
