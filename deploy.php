<?php
/**
 * Simple PHP Git Deploy Script
 * This script is triggered by sync.bat (via curl) or a GitHub Webhook
 * to automatically pull the latest changes to the live server.
 */

// Basic security check (Optional: you can add a secret token check here if needed)
if (!isset($_GET['manual']) && empty($_SERVER['HTTP_X_GITHUB_EVENT'])) {
    http_response_code(403);
    die("Forbidden");
}

// Ensure the directory is correct (usually the root of your web server)
$dir = __DIR__;

// Execute git pull
// Note: The server running PHP must have git installed and configured
// to pull from the repository without prompting for a password (e.g. using SSH keys).
$output = [];
$return_var = 0;

exec("cd {$dir} && git pull origin main 2>&1", $output, $return_var);

// Log the deployment
$log = date('Y-m-d H:i:s') . " - Deployment triggered.\n";
$log .= implode("\n", $output) . "\n";
$log .= "Return Code: {$return_var}\n";
$log .= "-------------------------\n";

file_put_contents('deploy.log', $log, FILE_APPEND);

if ($return_var === 0) {
    echo "Deployment successful.\n";
} else {
    http_response_code(500);
    echo "Deployment failed. Check deploy.log for details.\n";
}
?>
