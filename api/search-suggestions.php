<?php
require_once '../config/db.php';
header('Content-Type: application/json');

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
if (strlen($query) < 2) { echo json_encode([]); exit(); }

try {
    $stmt = $pdo->prepare("SELECT id, product_name, category, purity, primary_image FROM products WHERE product_name LIKE ? OR category LIKE ? OR collection_tag LIKE ? LIMIT 5");
    $searchTerm = "%{$query}%";
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    $results = $stmt->fetchAll();

    $suggestions = [];
    foreach ($results as $row) {
        $suggestions[] = [
            'id' => $row['id'],
            'name' => $row['product_name'],
            'category' => $row['category'],
            'purity' => $row['purity'],
            'image' => str_contains($row['primary_image'], 'http') ? $row['primary_image'] : 'assets/uploads/' . basename($row['primary_image'])
        ];
    }
    echo json_encode($suggestions);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>