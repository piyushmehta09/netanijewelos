<?php
session_start();
if(!isset($_SESSION['jewellery_cart'])) $_SESSION['jewellery_cart'] = [];

if(isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    if(isset($_SESSION['jewellery_cart'][$product_id])) {
        $_SESSION['jewellery_cart'][$product_id]++;
    } else {
        $_SESSION['jewellery_cart'][$product_id] = 1;
    }
    // If AJAX request, return JSON
    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        $total = array_sum($_SESSION['jewellery_cart']);
        header('Content-Type: application/json');
        echo json_encode(['status'=>'added','cart_count'=>$total]);
        exit();
    }
    // Check referrer - go back to where user was (not cart)
    $ref = $_SERVER['HTTP_REFERER'] ?? '../client/shop.php';
    header("Location: $ref");
    exit();
}
header("Location: ../client/shop.php");
exit();
