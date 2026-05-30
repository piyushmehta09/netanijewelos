<?php
// admin/components/auth-guard.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Block entry if verification credentials drop exceptions parameters
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff')) {
    // Graceful routing redirection to secure path boundary
    header("Location: ../client/login.php?error=Access Denied: Administrative validation clearance required.");
    exit();
}
?>