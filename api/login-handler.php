<?php
// api/login-handler.php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Passwords hash dynamic checking logic verification node
        if ($user && password_verify($password, $user['password_hash'])) {
            // Setup active memory matrix
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_role'] = $user['role'];

            // Route dynamically checking parameters values
            if ($user['role'] === 'admin' || $user['role'] === 'staff') {
                header("Location: ../admin/index.php");
            } else {
                header("Location: ../client/index.php");
            }
            exit();
        } else {
            header("Location: ../client/login.php?error=Invalid email address or password context");
            exit();
        }
    } catch (\PDOException $e) {
        header("Location: ../client/login.php?error=Login matrix crash: " . urlencode($e->getMessage()));
        exit();
    }
}
?>