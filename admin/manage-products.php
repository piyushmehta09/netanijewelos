<?php require_once 'components/auth-guard.php'; ?>
<?php
require_once '../config/db.php';
require_once 'components/sidebar.php';

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$_GET['delete']]);
    header("Location: manage-products.php?deleted=1");
    exit();
}

$latestRate = $pdo->query("SELECT * FROM gold_rates ORDER BY id DESC LIMIT 1")->fetch();
$productsList = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Vault | Netanis Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } .font-serif { font-family: 'Playfair Display', serif; } </style>
</head>
<body class="bg-[#fcfbfa] text-stone-800 flex min-h-screen">

    <?php renderSidebar('products'); ?>

    <div class="flex-1 p-10 overflow-y-auto">
        <div class="max-w-6xl mx-auto">
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b border-stone-200 pb-6 mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-serif tracking-wide text-[#832729] font-semibold mb-1">Product Vault</h1>
                    <p class="text-stone-500 text-xs font-light"><?php echo count($productsList); ?> products in catalogue</p>
                </div>
                <a href="add-product.php" class="bg-[#832729] hover:bg-[#6b2022] text-white text-xs font-medium uppercase tracking-widest px-5 py-3 rounded-lg shadow-sm transition-all flex items-center space-x-2">
                    <span>+</span><span>Add New Product</span>
                </a>
            </div>

            <?php if (isset($_GET['deleted'])): ?>
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg mb-6 text-xs">✓ Product deleted successfully.</div>
            <?php endif; ?>

            <div class="bg-white border border-stone-200/60 rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs font-light">
                        <thead class="bg-stone-50 border-b border-stone-200/80 text-stone-500 uppercase tracking-widest text-[10px] font-medium">
                            <tr>
                                <th class="p-4">Product</th>
                                <th class="p-4">SKU / Specs</th>
                                <th class="p-4 text-center">Category</th>
                                <th class="p-4 text-right">Weight</th>
                                <th class="p-4 text-right text-[#832729]">Live Price</th>
                                <th class="p-4 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            <?php if(count($productsList) > 0): ?>
                                <?php foreach($productsList as $product): 
                                    $ratePerGram = 0;
                                    if ($latestRate) {
                                        switch($product['purity']) {
                                            case '24Kt': $ratePerGram = $latestRate['rate_24kt']; break;
                                            case '22Kt': $ratePerGram = $latestRate['rate_22kt']; break;
                                            case '18Kt': $ratePerGram = $latestRate['rate_18kt']; break;
                                            case '14Kt': $ratePerGram = $latestRate['rate_14kt']; break;
                                        }
                                    }
                                    $baseMetalPrice = $product['weight_grams'] * $ratePerGram;
                                    $totalMakingCharges = $product['weight_grams'] * $product['making_charges_per_gram'];
                                    $subtotal = $baseMetalPrice + $totalMakingCharges;
                                    $discount = ($subtotal * $product['discount_percent']) / 100;
                                    $finalCalculatedPrice = round(($subtotal - $discount) * 1.03);
                                ?>
                                <tr class="hover:bg-stone-50/40 transition-colors">
                                    <td class="p-4">
                                        <div class="flex items-center space-x-3">
                                            <img src="<?php echo str_contains($product['primary_image'], 'http') ? $product['primary_image'] : '../client/assets/uploads/' . basename($product['primary_image']); ?>" class="w-12 h-12 object-cover rounded-lg border border-stone-200 bg-stone-50 shadow-sm">
                                            <div>
                                                <div class="font-medium text-stone-900 font-serif"><?php echo htmlspecialchars($product['product_name']); ?></div>
                                                <?php if ($product['discount_percent'] > 0): ?>
                                                <span class="text-[9px] bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded font-bold"><?php echo $product['discount_percent']; ?>% OFF</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 font-mono">
                                        <div class="text-stone-700 text-xs font-semibold"><?php echo htmlspecialchars($product['sku']); ?></div>
                                        <div class="text-[10px] text-stone-400 mt-0.5"><?php echo $product['metal_type']; ?> | <?php echo $product['purity']; ?></div>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="bg-stone-100 text-stone-600 px-2.5 py-1 rounded text-[10px] uppercase font-medium tracking-wider"><?php echo htmlspecialchars($product['category']); ?></span>
                                    </td>
                                    <td class="p-4 text-right font-mono text-stone-700 font-medium"><?php echo number_format($product['weight_grams'], 3); ?> g</td>
                                    <td class="p-4 text-right font-mono text-[#832729] font-bold">₹<?php echo number_format($finalCalculatedPrice); ?></td>
                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <a href="../client/product-details.php?id=<?php echo $product['id']; ?>" target="_blank" class="text-[10px] bg-stone-100 text-stone-600 px-2.5 py-1.5 rounded hover:bg-stone-200 transition-colors font-medium">View</a>
                                            <a href="manage-products.php?delete=<?php echo $product['id']; ?>" onclick="return confirm('Delete this product?')" class="text-[10px] bg-red-50 text-red-500 border border-red-100 px-2.5 py-1.5 rounded hover:bg-red-100 transition-colors font-medium">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="p-12 text-center italic text-stone-400">No products yet. <a href="add-product.php" class="text-[#832729] hover:underline">Add one now</a></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
