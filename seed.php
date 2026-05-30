<?php
require_once 'config/db.php';

try {
    // Purana kachra saaf karne ke liye truncate pipelines
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0; TRUNCATE TABLE gold_rates; TRUNCATE TABLE products; SET FOREIGN_KEY_CHECKS = 1;");

    // 1. Live Gold Rates Setup
    $stmt = $pdo->prepare("INSERT INTO gold_rates (rate_24kt, rate_22kt, rate_18kt, rate_14kt, silver_rate) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([7400, 6785, 5550, 4315, 92]);
    echo "💎 [MySQL Atlas Local]: Live Gold Rates synchronized!<br>";

    // 2. Dummy Product Injection
    $stmtProduct = $pdo->prepare("INSERT INTO products (product_name, sku, short_description, full_description, category, subcategory, collection_tag, metal_type, purity, weight_grams, making_charges_per_gram, discount_percent, primary_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmtProduct->execute([
        "Bloom Bud Gold Ring",
        "T-RNG-2026-001",
        "Beautiful floral design ring in 22Kt yellow gold.",
        "Crafted to perfection, this floral ring from the Summer Stack collection features absolute brilliance of yellow gold with hand-carved leaf matrices.",
        "Rings",
        "Dailywear Rings",
        "Elevated Summer Stack",
        "Gold",
        "22Kt",
        5.25,
        499.00,
        10,
        "https://images.unsplash.com/photo-1605100804763-247f67b3557e"
    ]);
    echo "💍 [MySQL Atlas Local]: Premium Test Product registered into cluster!";

} catch (\PDOException $e) {
    echo "🚨 Seeding Failed: " . $e->getMessage();
}
?>