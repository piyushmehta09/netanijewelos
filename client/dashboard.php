<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=Please login to view your dashboard.");
    exit();
}

$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? 'Guest';

$stmt = $pdo->prepare("SELECT o.*, GROUP_CONCAT(p.product_name SEPARATOR ', ') as items FROM orders o LEFT JOIN order_items oi ON o.id = oi.order_id LEFT JOIN products p ON oi.product_id = p.id WHERE o.user_id = ? GROUP BY o.id ORDER BY o.id DESC");
$stmt->execute([$userId]);
$userOrders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard | Netanis Jewelos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } .font-serif { font-family: 'Playfair Display', serif; } </style>
</head>
<body class="bg-[#fcfbfa] text-stone-800 min-h-screen">

    <?php include 'components/header.php'; ?>

    <div class="max-w-5xl mx-auto px-6 py-12 grid grid-cols-1 md:grid-cols-4 gap-8">
        
        <div class="md:col-span-1">
            <div class="bg-white border border-stone-200/60 rounded-xl p-5 shadow-sm text-center">
                <div class="w-14 h-14 bg-[#832729]/10 text-[#832729] rounded-full flex items-center justify-center font-serif text-xl font-bold mx-auto mb-3 uppercase">
                    <?php echo substr($userName, 0, 1); ?>
                </div>
                <h3 class="font-serif font-bold text-sm text-stone-900"><?php echo htmlspecialchars($userName); ?></h3>
                <p class="text-[9px] text-stone-400 font-mono uppercase tracking-wider mt-0.5">Verified Member</p>
                <div class="mt-5 pt-4 border-t border-stone-100 text-left text-[11px] space-y-2.5">
                    <a href="dashboard.php" class="block font-semibold text-[#832729]">📦 My Orders</a>
                    <a href="wishlist.php" class="block text-stone-500 hover:text-stone-900">❤️ Wishlist</a>
                    <a href="shop.php" class="block text-stone-500 hover:text-stone-900">💍 Continue Shopping</a>
                    <a href="../api/logout.php" class="block text-red-400 hover:text-red-600">🚪 Logout</a>
                </div>
            </div>
        </div>

        <div class="md:col-span-3 space-y-6">
            <div>
                <h2 class="text-xl font-serif font-semibold text-stone-900 mb-1">My Orders</h2>
                <p class="text-stone-400 text-xs font-light mb-6 uppercase tracking-widest">Track your order progress</p>
            </div>

            <?php if(count($userOrders) > 0): ?>
                <?php foreach($userOrders as $order): 
                    $statuses = ['Pending', 'Confirmed', 'Packed', 'Shipped', 'Delivered'];
                    $currentIdx = array_search($order['order_status'], $statuses);
                    if($currentIdx === false) $currentIdx = 0;
                    
                    $statusColors = [
                        'Pending' => 'text-amber-600 bg-amber-50 border-amber-200',
                        'Confirmed' => 'text-blue-600 bg-blue-50 border-blue-200',
                        'Packed' => 'text-purple-600 bg-purple-50 border-purple-200',
                        'Shipped' => 'text-indigo-600 bg-indigo-50 border-indigo-200',
                        'Delivered' => 'text-emerald-600 bg-emerald-50 border-emerald-200',
                        'Cancelled' => 'text-red-600 bg-red-50 border-red-200',
                    ];
                    $statusClass = $statusColors[$order['order_status']] ?? 'text-stone-600 bg-stone-50 border-stone-200';
                ?>
                <div class="bg-white border border-stone-200/60 rounded-xl p-6 shadow-sm">
                    <div class="flex justify-between items-start border-b border-stone-100 pb-4 mb-5">
                        <div>
                            <div class="font-mono font-bold text-stone-900 text-sm">#NETANIS-00<?php echo $order['id']; ?></div>
                            <div class="text-[10px] text-stone-400 mt-0.5"><?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></div>
                            <?php if ($order['items']): ?>
                            <div class="text-[11px] text-stone-500 mt-1 font-light"><?php echo htmlspecialchars($order['items']); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="text-right">
                            <div class="font-mono font-bold text-[#832729]">₹<?php echo number_format($order['total_amount']); ?></div>
                            <span class="text-[9px] font-semibold uppercase tracking-wider px-2.5 py-0.5 rounded-full border <?php echo $statusClass; ?> block mt-1.5"><?php echo $order['order_status']; ?></span>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <?php if ($order['order_status'] !== 'Cancelled'): ?>
                    <div class="relative flex items-center justify-between w-full px-2 mb-2">
                        <div class="absolute left-0 right-0 top-1/2 -translate-y-1/2 h-[2px] bg-stone-100 z-0"></div>
                        <div class="absolute left-0 top-1/2 -translate-y-1/2 h-[2px] bg-[#832729] z-0 transition-all duration-300" style="width: <?php echo ($currentIdx / (count($statuses) - 1)) * 100; ?>%;"></div>
                        <?php foreach($statuses as $index => $stepName): 
                            $isPassed = $index <= $currentIdx;
                        ?>
                        <div class="relative z-10 flex flex-col items-center">
                            <div class="w-5 h-5 rounded-full flex items-center justify-center text-[8px] font-mono shadow-sm transition-all <?php echo $isPassed ? 'bg-[#832729] text-white' : 'bg-white border-2 border-stone-200 text-stone-400'; ?>">
                                <?php echo $isPassed ? '✓' : ''; ?>
                            </div>
                            <span class="text-[8px] uppercase tracking-wider mt-2 font-sans font-semibold <?php echo $isPassed ? 'text-stone-800' : 'text-stone-400'; ?>"><?php echo $stepName; ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($order['shipping_address']): ?>
                    <div class="mt-4 pt-4 border-t border-stone-50 text-[10px] text-stone-400 font-light">
                        📦 Delivering to: <?php echo htmlspecialchars($order['shipping_address'] . ', ' . $order['shipping_city'] . ' - ' . $order['shipping_pincode']); ?>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="p-12 text-center bg-white border border-stone-200/60 rounded-xl">
                    <div class="text-3xl mb-3">📦</div>
                    <p class="text-stone-400 text-xs font-light italic mb-4">No orders placed yet.</p>
                    <a href="shop.php" class="inline-block bg-[#832729] text-white text-xs font-semibold uppercase tracking-wider px-6 py-3 rounded-lg hover:bg-[#6b2022] transition-colors">Browse Jewellery</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'components/footer.php'; ?>
</body>
</html>
