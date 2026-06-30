<?php
// Secret token for triggering the deployment securely
// Make sure this matches the token in your sync_and_deploy.bat file!
$secret_token = 'vnt_deploy_token_2026';

// Check if the correct token was provided in the URL
if (!isset($_GET['token']) || $_GET['token'] !== $secret_token) {
    header('HTTP/1.0 403 Forbidden');
    die('403 Forbidden - Invalid Token');
}

// Ensure this script is running from the cPanel repository directory
// Note: This script assumes you have a Git repository setup on cPanel at /home/vntauras/repositories/vnt-aura
$repo_dir = '/home/vntauras/repositories/vnt-aura';

// Execute Git Pull to fetch latest changes from GitHub
// Because we have .cpanel.yml in the repo, cPanel's Version Control will automatically execute the deployment tasks after pulling!
$output = [];
$exit_code = 0;

exec("cd {$repo_dir} && git pull origin main 2>&1", $output, $exit_code);

if ($exit_code === 0) {
    echo "Deployment Triggered Successfully!\n";
    echo implode("\n", $output);
} else {
    header('HTTP/1.0 500 Internal Server Error');
    echo "Deployment Failed!\n";
    echo implode("\n", $output);
}
?>
