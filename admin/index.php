<?php
require_once '../config/db.php';
require_once 'components/auth-guard.php';
require_once 'components/sidebar.php';

$totalProducts = $pdo->query("SELECT COUNT(*) FROM products WHERE is_active=1")->fetchColumn();
$latestRate = $pdo->query("SELECT * FROM gold_rates ORDER BY id DESC LIMIT 1")->fetch();
$totalRevenue = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE order_status != 'Cancelled'")->fetchColumn() ?? 0;
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$newOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE owner_notified=0 OR order_status='Pending'")->fetchColumn();
$recentOrders = $pdo->query("SELECT o.*, u.full_name FROM orders o JOIN users u ON o.user_id=u.id ORDER BY o.id DESC LIMIT 8")->fetchAll();
$todayOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at)=CURDATE()")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard | Netanis Jewelos</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
body { font-family: 'DM Sans', sans-serif; background: #faf9f7; color: #2d2926; }
.font-display { font-family: 'Cormorant Garamond', serif; }
:root { --gold: #b8860b; --border: #e8e0d5; }
.stat-card { background: white; border: 1px solid var(--border); border-radius: 16px; padding: 24px; }
.status-badge { font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; padding: 3px 10px; border-radius: 999px; }
.status-Pending { background: #fef3c7; color: #92400e; }
.status-Confirmed { background: #dbeafe; color: #1e40af; }
.status-Packed { background: #ede9fe; color: #5b21b6; }
.status-Shipped { background: #dcfce7; color: #166534; }
.status-Delivered { background: #d1fae5; color: #065f46; }
.status-Cancelled { background: #fee2e2; color: #991b1b; }
@keyframes pulse-ring { 0% { box-shadow: 0 0 0 0 rgba(184,134,11,0.4); } 70% { box-shadow: 0 0 0 10px rgba(184,134,11,0); } 100% { box-shadow: 0 0 0 0 rgba(184,134,11,0); } }
.pulse-gold { animation: pulse-ring 2s infinite; }
</style>
</head>
<body class="flex min-h-screen">
<?php renderSidebar('dashboard'); ?>

<div class="flex-1 overflow-y-auto">
  <!-- TOP BAR -->
  <div class="bg-white border-b border-[#e8e0d5] px-10 py-5 flex justify-between items-center sticky top-0 z-20">
    <div>
      <h1 class="font-display text-2xl font-semibold text-[#2d2926]">Dashboard Overview</h1>
      <p class="text-[#8c7b6b] text-xs mt-0.5"><?php echo date('l, d F Y'); ?></p>
    </div>
    <div class="flex items-center space-x-3">
      <?php if($newOrders > 0): ?>
      <a href="orders.php" class="pulse-gold flex items-center space-x-2 bg-[#b8860b] text-white px-4 py-2.5 rounded-xl text-xs font-semibold">
        <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
        <span><?php echo $newOrders; ?> New Order<?php echo $newOrders>1?'s':''; ?>!</span>
      </a>
      <?php endif; ?>
      <a href="../client/index.php" target="_blank" class="text-xs border border-[#e8e0d5] px-4 py-2.5 rounded-xl hover:bg-[#f5efe7] transition-colors text-[#5c5047]">🌐 View Store</a>
    </div>
  </div>

  <div class="p-10">
    <!-- STATS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
      <div class="stat-card">
        <div class="text-[10px] uppercase tracking-widest text-[#8c7b6b] mb-2">Total Revenue</div>
        <div class="font-display text-3xl font-bold text-emerald-600">₹<?php echo number_format($totalRevenue); ?></div>
        <div class="text-[10px] text-[#8c7b6b] mt-1">All confirmed orders</div>
      </div>
      <div class="stat-card">
        <div class="text-[10px] uppercase tracking-widest text-[#8c7b6b] mb-2">Total Orders</div>
        <div class="font-display text-3xl font-bold text-[#2d2926]"><?php echo $totalOrders; ?></div>
        <div class="text-[10px] text-emerald-600 mt-1">Today: +<?php echo $todayOrders; ?></div>
      </div>
      <div class="stat-card">
        <div class="text-[10px] uppercase tracking-widest text-[#8c7b6b] mb-2">Active Products</div>
        <div class="font-display text-3xl font-bold text-[#2d2926]"><?php echo $totalProducts; ?></div>
        <div class="text-[10px] text-[#8c7b6b] mt-1">In catalogue</div>
      </div>
      <div class="stat-card" style="border-color:#b8860b;background:linear-gradient(135deg,#fef9ee,#fdf3d0);">
        <div class="text-[10px] uppercase tracking-widest text-[#8c7b6b] mb-2">Live Gold 22Kt</div>
        <div class="font-display text-3xl font-bold text-[#b8860b]">₹<?php echo number_format($latestRate['rate_22kt'] ?? 0); ?></div>
        <div class="text-[10px] text-[#8c7b6b] mt-1">per gram · <a href="update-gold-rate.php" class="underline hover:text-[#b8860b]">Update</a></div>
      </div>
    </div>

    <!-- NEW ORDER ALERT -->
    <?php if($newOrders > 0): ?>
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 mb-8 flex items-center justify-between">
      <div class="flex items-center space-x-3">
        <div class="w-10 h-10 bg-[#b8860b] text-white rounded-full flex items-center justify-center font-bold text-sm animate-pulse"><?php echo $newOrders; ?></div>
        <div>
          <div class="font-semibold text-amber-900 text-sm">New Orders Require Attention!</div>
          <div class="text-amber-700 text-xs mt-0.5">Please review and confirm these orders to process delivery.</div>
        </div>
      </div>
      <a href="orders.php" class="bg-[#b8860b] text-white px-5 py-2.5 rounded-xl text-xs font-semibold uppercase tracking-wider hover:bg-[#9a7009] transition-colors">Manage Orders →</a>
    </div>
    <?php endif; ?>

    <!-- RECENT ORDERS -->
    <div class="bg-white rounded-2xl border border-[#e8e0d5] shadow-sm overflow-hidden">
      <div class="px-6 py-5 flex items-center justify-between border-b border-[#f0ebe4]">
        <h2 class="font-display text-lg font-semibold">Recent Orders</h2>
        <a href="orders.php" class="text-xs text-[#b8860b] font-semibold hover:underline">View All →</a>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-xs">
          <thead>
            <tr class="bg-[#faf9f7] border-b border-[#e8e0d5]">
              <th class="text-left px-6 py-3 text-[10px] uppercase tracking-widest text-[#8c7b6b] font-semibold">Order ID</th>
              <th class="text-left px-6 py-3 text-[10px] uppercase tracking-widest text-[#8c7b6b] font-semibold">Customer</th>
              <th class="text-left px-6 py-3 text-[10px] uppercase tracking-widest text-[#8c7b6b] font-semibold">Date</th>
              <th class="text-right px-6 py-3 text-[10px] uppercase tracking-widest text-[#8c7b6b] font-semibold">Amount</th>
              <th class="text-center px-6 py-3 text-[10px] uppercase tracking-widest text-[#8c7b6b] font-semibold">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#f5f0ea]">
            <?php foreach($recentOrders as $o): ?>
            <tr class="hover:bg-[#faf9f7] transition-colors">
              <td class="px-6 py-4 font-mono font-bold text-[#b8860b]">#NETANIS-<?php echo str_pad($o['id'],5,'0',STR_PAD_LEFT); ?></td>
              <td class="px-6 py-4">
                <div class="font-medium text-[#2d2926]"><?php echo htmlspecialchars($o['shipping_name'] ?: $o['full_name']); ?></div>
                <div class="text-[#8c7b6b] text-[10px] mt-0.5"><?php echo htmlspecialchars($o['shipping_phone'] ?? ''); ?></div>
              </td>
              <td class="px-6 py-4 text-[#8c7b6b]"><?php echo date('d M Y', strtotime($o['created_at'])); ?></td>
              <td class="px-6 py-4 text-right font-semibold text-[#2d2926]">₹<?php echo number_format($o['total_amount']); ?></td>
              <td class="px-6 py-4 text-center">
                <span class="status-badge status-<?php echo $o['order_status']; ?>"><?php echo $o['order_status']; ?></span>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($recentOrders)): ?>
            <tr><td colspan="5" class="px-6 py-12 text-center text-[#8c7b6b] italic">No orders yet.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- AUTO REFRESH for new order notifications -->
<script>
setTimeout(() => { location.reload(); }, 60000); // Refresh every 60s
</script>
</body>
</html>
