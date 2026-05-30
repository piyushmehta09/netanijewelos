<?php
// api/fix-guest-user.php
require_once '../config/db.php';

try {
    // Check if user with ID 1 already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = 1");
    $stmt->execute();
    
    if (!$stmt->fetch()) {
        // Safe injection of dummy system user with ID 1 to satisfy constraints
        $dummyPassword = password_hash('GuestSimulation2026', PASSWORD_BCRYPT);
        $insert = $pdo->prepare("INSERT INTO users (id, full_name, email, password_hash, phone, role) VALUES (1, 'Guest Customer', 'guest@netanisjewelos.com', ?, '919876543210', 'customer')");
        $insert->execute([$dummyPassword]);
        echo "<div style='font-family:sans-serif; padding:20px; color:#1e4620; bg:#edf7ed;'>✨ Database Core Restored! Guest anchor row registered at ID 1. Now retry your checkout flow.</div>";
    } else {
        echo "<div style='font-family:sans-serif; padding:20px; color:#1c3d5a; bg:#eff8ff;'>ℹ️ ID 1 already exists in your database user matrix. The constraint error might be from another mismatch.</div>";
    }
} catch (Exception $e) {
    die("🚨 Fix Script Failure: " . $e->getMessage());
}
?>