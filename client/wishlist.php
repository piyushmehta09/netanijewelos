<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=Please login to view your wishlist.");
    exit();
}

$userId = $_SESSION['user_id'];

// Handle remove from wishlist
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $rmStmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
    $rmStmt->execute([$userId, $_GET['remove']]);
    header("Location: wishlist.php");
    exit();
}

$latestRate = $pdo->query("SELECT * FROM gold_rates ORDER BY id DESC LIMIT 1")->fetch();

$stmt = $pdo->prepare("SELECT p.*, w.id as wish_id FROM wishlist w JOIN products p ON w.product_id = p.id WHERE w.user_id = ? ORDER BY w.added_at DESC");
$stmt->execute([$userId]);
$wishItems = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist | Netanis Jewelos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } .font-serif { font-family: 'Playfair Display', serif; } </style>
</head>
<body class="bg-[#fcfbfa] text-stone-800 min-h-screen">
    <?php include 'components/header.php'; ?>

    <div class="max-w-6xl mx-auto px-6 py-12">
        <h1 class="text-2xl font-serif font-semibold text-stone-900 mb-2">My Wishlist</h1>
        <p class="text-stone-400 text-xs font-light mb-8 uppercase tracking-widest">Saved masterpieces for later</p>

        <?php if (!empty($wishItems)): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($wishItems as $product):
                $ratePerGram = 6500;
                if ($latestRate) {
                    switch ($product['purity']) {
                        case '24Kt': $ratePerGram = $latestRate['rate_24kt']; break;
                        case '22Kt': $ratePerGram = $latestRate['rate_22kt']; break;
                        case '18Kt': $ratePerGram = $latestRate['rate_18kt']; break;
                        case '14Kt': $ratePerGram = $latestRate['rate_14kt']; break;
                    }
                }
                $baseMetal = $product['weight_grams'] * $ratePerGram;
                $making = $product['weight_grams'] * $product['making_charges_per_gram'];
                $sub = $baseMetal + $making;
                $disc = ($sub * $product['discount_percent']) / 100;
                $final = round(($sub - $disc) * 1.03);
            ?>
            <div class="bg-white border border-stone-200/60 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all group flex flex-col">
                <div class="relative aspect-square bg-stone-50 overflow-hidden border-b border-stone-100">
                    <img src="<?php echo str_contains($product['primary_image'], 'http') ? $product['primary_image'] : 'assets/uploads/' . basename($product['primary_image']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <a href="wishlist.php?remove=<?php echo $product['id']; ?>" class="absolute top-2 right-2 bg-white/90 hover:bg-red-50 text-red-400 hover:text-red-500 rounded-full w-8 h-8 flex items-center justify-center shadow border border-stone-100 transition-all text-sm">✕</a>
                </div>
                <div class="p-4 flex-1 flex flex-col justify-between">
                    <div>
                        <span class="text-[9px] uppercase tracking-widest text-[#832729] font-mono block mb-1 font-semibold"><?php echo $product['purity']; ?> • <?php echo $product['metal_type']; ?></span>
                        <h3 class="font-serif font-semibold text-sm text-stone-900 mb-1"><a href="product-details.php?id=<?php echo $product['id']; ?>" class="hover:text-[#832729]"><?php echo $product['product_name']; ?></a></h3>
                        <div class="text-[#832729] font-mono font-bold text-sm mb-3">₹<?php echo number_format($final); ?></div>
                    </div>
                    <div class="space-y-2">
                        <a href="../api/add-to-cart.php?id=<?php echo $product['id']; ?>" class="block text-center bg-[#832729] text-white py-2 text-[10px] font-medium uppercase tracking-widest rounded hover:bg-[#6b2022] transition-colors">Add to Cart</a>
                        <a href="product-details.php?id=<?php echo $product['id']; ?>" class="block text-center border border-stone-200 text-stone-500 py-2 text-[10px] font-medium uppercase tracking-widest rounded hover:bg-stone-50 transition-colors">View Details</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="bg-white border border-stone-200/60 rounded-xl p-16 text-center shadow-sm">
            <div class="text-4xl mb-4">❤️</div>
            <p class="text-stone-400 italic font-light mb-6">Your wishlist is empty. Save your favourite pieces here!</p>
            <a href="shop.php" class="inline-block bg-[#832729] text-white text-xs font-semibold uppercase tracking-wider px-8 py-3 rounded-lg hover:bg-[#6b2022] transition-colors">Browse Catalogue</a>
        </div>
        <?php endif; ?>
    </div>

    <?php include 'components/footer.php'; ?>
</body>
</html>
