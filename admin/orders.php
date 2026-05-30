<?php require_once 'components/auth-guard.php'; ?>
<?php
require_once '../config/db.php';
require_once 'components/sidebar.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    try {
        $stmt = $pdo->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
        $stmt->execute([$_POST['order_status'], $_POST['order_id']]);
        $message = "<div class='bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg mb-6 text-xs shadow-sm font-sans'>✨ Order status updated successfully.</div>";
    } catch (\PDOException $e) {
        $message = "<div class='bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg mb-6 text-xs shadow-sm font-sans'>🚨 Error: " . $e->getMessage() . "</div>";
    }
}

$ordersList = $pdo->query("SELECT o.*, u.full_name, u.email FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders | Netanis Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } .font-serif { font-family: 'Playfair Display', serif; } </style>
</head>
<body class="bg-[#fcfbfa] text-stone-800 flex min-h-screen">

    <?php renderSidebar('orders_dashboard_token'); ?>

    <div class="flex-1 p-10 overflow-y-auto">
        <div class="max-w-6xl mx-auto">
            <h1 class="text-3xl font-serif tracking-wide text-[#832729] font-semibold mb-1">Orders Management</h1>
            <p class="text-stone-500 text-xs font-light mb-8 uppercase tracking-widest font-sans">Track and update customer orders</p>

            <?php echo $message; ?>

            <div class="bg-white border border-stone-200/60 rounded-xl p-6 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs font-light">
                        <thead class="bg-stone-50 border-b border-stone-200 text-stone-500 uppercase tracking-widest text-[10px] font-medium">
                            <tr>
                                <th class="p-4">Order ID</th>
                                <th class="p-4">Customer</th>
                                <th class="p-4">Delivery Address</th>
                                <th class="p-4 text-right">Amount</th>
                                <th class="p-4 text-center">Status</th>
                                <th class="p-4 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            <?php if (count($ordersList) > 0): ?>
                                <?php foreach ($ordersList as $order):
                                    $statusColors = [
                                        'Pending' => 'text-amber-600',
                                        'Confirmed' => 'text-blue-600',
                                        'Packed' => 'text-purple-600',
                                        'Shipped' => 'text-indigo-600',
                                        'Delivered' => 'text-emerald-600',
                                        'Cancelled' => 'text-red-600',
                                    ];
                                    $sc = $statusColors[$order['order_status']] ?? 'text-stone-600';
                                ?>
                                <tr class="hover:bg-stone-50/40 transition-colors">
                                    <td class="p-4 font-mono font-bold text-stone-900">#NETANIS-00<?php echo $order['id']; ?><br><span class="text-[9px] text-stone-400 font-light"><?php echo date('d M Y', strtotime($order['created_at'])); ?></span></td>
                                    <td class="p-4">
                                        <div class="font-medium text-stone-800"><?php echo $order['shipping_name'] ?? htmlspecialchars($order['full_name']); ?></div>
                                        <div class="text-[10px] text-stone-400 mt-0.5"><?php echo $order['shipping_phone'] ?? ''; ?></div>
                                    </td>
                                    <td class="p-4 text-[10px] text-stone-500 font-light max-w-[160px]">
                                        <?php if ($order['shipping_address']): ?>
                                        <?php echo htmlspecialchars($order['shipping_address']); ?>, <?php echo htmlspecialchars($order['shipping_city']); ?> - <?php echo htmlspecialchars($order['shipping_pincode']); ?>
                                        <?php else: ?>
                                        <span class="italic">Not provided</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-4 text-right font-mono text-stone-700 font-medium">₹<?php echo number_format($order['total_amount']); ?></td>
                                    <td class="p-4 text-center">
                                        <form action="orders.php" method="POST" class="flex items-center justify-center space-x-2">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <select name="order_status" class="bg-stone-50 border border-stone-200 rounded px-2 py-1 text-xs outline-none focus:border-[#832729] <?php echo $sc; ?> font-medium">
                                                <?php foreach (['Pending', 'Confirmed', 'Packed', 'Shipped', 'Delivered', 'Cancelled'] as $s): ?>
                                                <option value="<?php echo $s; ?>" <?php echo $order['order_status'] === $s ? 'selected' : ''; ?>><?php echo $s; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                    </td>
                                    <td class="p-4 text-center">
                                            <button type="submit" name="update_status" class="bg-[#832729] text-white text-[10px] font-semibold tracking-wider uppercase px-3 py-1.5 rounded hover:bg-[#6b2022] transition-colors">Update</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="p-10 text-center italic text-stone-400">No orders yet.</td>
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
