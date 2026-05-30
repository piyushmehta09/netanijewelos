<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'login_required']);
    exit();
}

$userId = $_SESSION['user_id'];
$productId = intval($_GET['id'] ?? 0);

if (!$productId) {
    echo json_encode(['status' => 'error']);
    exit();
}

// Check if already in wishlist
$check = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
$check->execute([$userId, $productId]);

if ($check->fetch()) {
    $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?")->execute([$userId, $productId]);
    echo json_encode(['status' => 'removed']);
} else {
    $pdo->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)")->execute([$userId, $productId]);
    echo json_encode(['status' => 'added']);
}
