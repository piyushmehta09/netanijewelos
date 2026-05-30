<?php
session_start();
require_once '../config/db.php';
$orderId  = intval($_GET['id'] ?? 0);
$total    = intval($_GET['total'] ?? 0);
$orderRef = htmlspecialchars($_GET['ref'] ?? '');
if(!$orderId) { header("Location: shop.php"); exit(); }

// Fetch order from DB to build WA message fresh (no URL encoding issues)
$ord = $pdo->prepare("SELECT o.*, u.full_name, u.email FROM orders o LEFT JOIN users u ON o.user_id=u.id WHERE o.id=?");
$ord->execute([$orderId]);
$order = $ord->fetch();

$items = $pdo->prepare("SELECT oi.*, p.product_name FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE oi.order_id=?");
$items->execute([$orderId]);
$orderItems = $items->fetchAll();

$phone = $order['shipping_phone'] ?? '';
$cleanPhone = preg_replace('/[^0-9]/', '', $phone);
$customerName = $order['shipping_name'] ?? ($order['full_name'] ?? 'Customer');
if(!$orderRef) $orderRef = '#NETANIS-' . str_pad($orderId, 5, '0', STR_PAD_LEFT);

$waLines  = "✨ *NETANIS JEWELOS* — Order Confirmed!\n\n";
$waLines .= "Hello *{$customerName}*! Your order is ready.\n\n";
$waLines .= "*Order ID:* {$orderRef}\n";
$waLines .= "*Items:*\n";
foreach($orderItems as $oi) {
    $waLines .= "• {$oi['product_name']} (Qty:{$oi['quantity']}) — ₹" . number_format($oi['price_at_purchase'] * $oi['quantity']) . "\n";
}
$waLines .= "\n*Total Payable:* ₹" . number_format($order['total_amount']) . " (Incl. 3% GST)\n";
$waLines .= "*Delivery to:* " . $order['shipping_address'] . ", " . $order['shipping_city'] . " - " . $order['shipping_pincode'] . "\n\n";
$waLines .= "Track: http://localhost/premium-jewellery-complete/client/dashboard.php\n";
$waLines .= "Questions? WhatsApp: 918147349242";

// wa.me is more reliable than api.whatsapp.com
$waUrl = "https://wa.me/{$cleanPhone}?text=" . rawurlencode($waLines);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Order Confirmed! | Netanis Jewelos</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
body{font-family:'Jost',sans-serif;background:#fdfaf7;color:#2d1a10;}
.serif{font-family:'Cormorant Garamond',serif;}
@keyframes pop{0%{opacity:0;transform:scale(0.4) rotate(-10deg)}60%{transform:scale(1.1)}100%{opacity:1;transform:scale(1)}}
@keyframes fadeUp{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:translateY(0)}}
@keyframes confetti{0%{opacity:1;transform:translateY(0) rotate(0)}100%{opacity:0;transform:translateY(-200px) rotate(720deg)}}
.check-pop{animation:pop 0.7s cubic-bezier(0.175,0.885,0.32,1.275) forwards;}
.fade-1{animation:fadeUp 0.6s ease 0.3s both;}
.fade-2{animation:fadeUp 0.6s ease 0.5s both;}
.fade-3{animation:fadeUp 0.6s ease 0.7s both;}
.fade-4{animation:fadeUp 0.6s ease 0.9s both;}
.wa-btn{background:#25d366;color:#fff;display:flex;align-items:center;justify-content:center;gap:12px;padding:16px 24px;border-radius:10px;text-decoration:none;font-weight:700;font-size:14px;transition:all 0.3s;box-shadow:0 8px 24px rgba(37,211,102,0.3);}
.wa-btn:hover{background:#1ebe5d;transform:translateY(-2px);box-shadow:0 12px 32px rgba(37,211,102,0.4);}
</style>
</head>
<body>
<?php include 'components/header.php'; ?>

<div style="min-height:80vh;display:flex;align-items:center;justify-content:center;padding:40px 20px;">
  <div style="max-width:480px;width:100%;text-align:center;">

    <!-- ANIMATED CHECK -->
    <div class="check-pop" style="width:100px;height:100px;background:linear-gradient(135deg,#22c55e,#16a34a);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 28px;box-shadow:0 12px 40px rgba(22,163,74,0.35);">
      <svg style="width:52px;height:52px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
    </div>

    <!-- TITLE -->
    <div class="fade-1">
      <h1 class="serif" style="font-size:40px;font-weight:600;color:#1a0f0a;margin-bottom:8px;">Masterpiece Secured! ✨</h1>
      <p style="font-size:14px;color:#8c7b6b;font-weight:300;">Your jewellery is being crafted with love. We'll reach you shortly.</p>
    </div>

    <!-- ORDER CARD -->
    <div class="fade-2" style="background:#fff;border:1px solid #ede8e0;border-radius:12px;padding:24px;margin:24px 0;text-align:left;box-shadow:0 4px 20px rgba(26,15,10,0.06);">
      <div style="display:flex;justify-content:space-between;align-items:start;border-bottom:1px solid #f5f0ea;padding-bottom:16px;margin-bottom:16px;">
        <div>
          <div style="font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:#9a8070;margin-bottom:4px;">Order Reference</div>
          <div style="font-family:'Cormorant Garamond',serif;font-size:20px;font-weight:700;color:#b8860b;"><?php echo $orderRef; ?></div>
        </div>
        <span style="background:#dcfce7;color:#166534;font-size:10px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;padding:6px 14px;border-radius:20px;">Confirmed ✓</span>
      </div>
      <!-- Items -->
      <?php foreach($orderItems as $oi): ?>
      <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:8px;">
        <span style="color:#3a2a1e;"><?php echo htmlspecialchars($oi['product_name']); ?> <span style="color:#9a8070;">×<?php echo $oi['quantity']; ?></span></span>
        <span style="font-weight:600;">₹<?php echo number_format($oi['price_at_purchase'] * $oi['quantity']); ?></span>
      </div>
      <?php endforeach; ?>
      <div style="border-top:1px solid #f5f0ea;padding-top:14px;margin-top:8px;display:flex;justify-content:space-between;align-items:baseline;">
        <div>
          <div style="font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:#9a8070;">Total Payable (COD)</div>
          <div class="serif" style="font-size:28px;font-weight:700;color:#1a0f0a;">₹<?php echo number_format($order['total_amount']); ?></div>
        </div>
        <div style="text-right;font-size:11px;color:#9a8070;text-align:right;line-height:1.8;">
          📧 Email sent<br>📦 5–7 working days<br>🆓 Free delivery
        </div>
      </div>
    </div>

    <!-- WHATSAPP BUTTON -->
    <div class="fade-3">
      <?php if($cleanPhone): ?>
      <a href="<?php echo htmlspecialchars($waUrl); ?>" target="_blank" class="wa-btn" style="margin-bottom:12px;">
        <svg style="width:22px;height:22px;" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        📲 Send Invoice on WhatsApp
      </a>
      <p style="font-size:11px;color:#9a8070;margin-bottom:16px;">Opens WhatsApp — sends your complete order receipt to your number</p>
      <?php else: ?>
      <div style="background:#fef3c7;border:1px solid #fde68a;border-radius:8px;padding:12px;font-size:12px;color:#92400e;margin-bottom:16px;">
        ⚠️ No WhatsApp number provided. Update in your profile for future orders.
      </div>
      <?php endif; ?>
    </div>

    <!-- ACTION BUTTONS -->
    <div class="fade-4" style="display:flex;gap:12px;">
      <a href="dashboard.php" style="flex:1;background:#1a0f0a;color:#fff;text-decoration:none;padding:14px;border-radius:8px;font-size:12px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;text-align:center;transition:background 0.2s;" onmouseover="this.style.background='#b8860b'" onmouseout="this.style.background='#1a0f0a'">📦 Track Order</a>
      <a href="shop.php" style="flex:1;background:#fff;border:1.5px solid #ede8e0;color:#3a2a1e;text-decoration:none;padding:14px;border-radius:8px;font-size:12px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;text-align:center;transition:all 0.2s;" onmouseover="this.style.borderColor='#b8860b';this.style.color='#b8860b'" onmouseout="this.style.borderColor='#ede8e0';this.style.color='#3a2a1e'">Continue Shopping</a>
    </div>

  </div>
</div>

<?php include 'components/footer.php'; ?>
</body>
</html>
