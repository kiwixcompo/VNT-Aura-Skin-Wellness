<?php
session_start();

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
    if (isset($_SESSION['needs_password_change']) && $_SESSION['needs_password_change']) {
        if (basename($_SERVER['PHP_SELF']) !== 'change_password.php') {
            header('Location: change_password.php');
            exit;
        }
    }
}
?>
