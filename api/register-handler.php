<?php
// api/register-handler.php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $phone = trim($_POST['phone']);

    if (empty($fullName) || empty($email) || empty($password)) {
        header("Location: ../client/signup.php?error=Missing required parameters");
        exit();
    }

    try {
        // Check if user account email sequence already registered
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->execute([$email]);
        if ($checkStmt->fetch()) {
            header("Location: ../client/signup.php?error=Email already registered in system");
            exit();
        }

        // Standard Hashing Core Engine Implementation
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        // Insert fresh metadata records
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash, phone, role) VALUES (?, ?, ?, ?, 'customer')");
        $stmt->execute([$fullName, $email, $passwordHash, $phone]);

        header("Location: ../client/login.php?success=Account created successfully. Please login.");
        exit();

    } catch (\PDOException $e) {
        header("Location: ../client/signup.php?error=System process failure: " . urlencode($e->getMessage()));
        exit();
    }
}
?>