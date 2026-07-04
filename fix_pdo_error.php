<?php
// fix_pdo_error.php
// This script will automatically scan all PHP files in the current directory
// and fix any accidental replacement of the $pdo variable with a database name string.

$directory = __DIR__;
$files = glob($directory . '/*.php');
$filesFixed = 0;

echo "<h2>Starting automatic fix...</h2>";

foreach ($files as $file) {
    // Skip this script itself
    if (basename($file) === 'fix_pdo_error.php') continue;

    $content = file_get_contents($file);
    $originalContent = $content;

    // We look for any instance where get_setting() was called with a hardcoded string 
    // (like 'vntauras_vnt_aura') instead of the $pdo variable.
    // Regex matches get_setting('ANY_STRING',
    $pattern = "/get_setting\s*\(\s*['\"][^'\"]+['\"]\s*,/i";
    
    // Replace with get_setting($pdo,
    $content = preg_replace($pattern, "get_setting(\$pdo,", $content);
    
    // Also check if they assigned $pdo = "vntauras_vnt_aura" manually in the file
    $pattern2 = "/\\\$pdo\s*=\s*['\"][^'\"]+['\"]\s*;/i";
    if (preg_match($pattern2, $content)) {
        echo "<span style='color: orange'>Warning: Found hardcoded \$pdo string assignment in " . basename($file) . " - removing it.</span><br>";
        $content = preg_replace($pattern2, "", $content);
    }

    if ($content !== $originalContent) {
        file_put_contents($file, $content);
        echo "<span style='color: green'>✓ Fixed error in: " . basename($file) . "</span><br>";
        $filesFixed++;
    }
}

if ($filesFixed > 0) {
    echo "<h3>Successfully fixed $filesFixed files! You can now delete this script (fix_pdo_error.php) and try loading your treatments.php page again.</h3>";
} else {
    echo "<h3>No errors found. If the site still crashes, please send me the exact contents of treatments.php around line 78!</h3>";
}
?>
