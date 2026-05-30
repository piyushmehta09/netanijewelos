<?php
session_start();
require_once '../config/db.php';
require_once '../config/mail-config.php';
require_once '../api/send-notifications.php';

if(empty($_SESSION['jewellery_cart'])) { header("Location: shop.php"); exit(); }

$latestRate = $pdo->query("SELECT * FROM gold_rates ORDER BY id DESC LIMIT 1")->fetch();
$totalGold=0; $totalMaking=0; $totalSavings=0; $totalDiamond=0; $checkoutProducts=[];

$ids = implode(',', array_map('intval', array_keys($_SESSION['jewellery_cart'])));
$products = $pdo->query("SELECT * FROM products WHERE id IN ($ids)")->fetchAll();

foreach($products as $p) {
    $qty = $_SESSION['jewellery_cart'][$p['id']];
    $rate = $latestRate['rate_22kt'] ?? 6820;
    switch($p['purity']) {
        case '24Kt': $rate = $latestRate['rate_24kt']; break;
        case '18Kt': $rate = $latestRate['rate_18kt']; break;
        case '14Kt': $rate = $latestRate['rate_14kt']; break;
    }
    $goldC = $p['weight_grams'] * $rate;
    $makingF = $p['weight_grams'] * $p['making_charges_per_gram'];
    $makingD = ($makingF * ($p['making_discount_percent']??0)) / 100;
    $makingFinal = $makingF - $makingD;
    $diamC = 0;
    if($p['has_diamond'] && $p['diamond_carat']>0) {
        $df = $p['diamond_carat'] * ($latestRate['diamond_rate_per_ct']??25000);
        $dd = ($df * ($p['diamond_discount_percent']??0))/100;
        $diamC = $df - $dd;
    }
    $itemPrice = round(($goldC + $makingFinal + $diamC) * 1.03);
    $totalGold += $goldC * $qty;
    $totalMaking += $makingFinal * $qty;
    $totalDiamond += $diamC * $qty;
    $savings = ($makingD + (($p['diamond_carat']??0)*($latestRate['diamond_rate_per_ct']??25000)*($p['diamond_discount_percent']??0)/100)) * $qty;
    $totalSavings += $savings;
    $checkoutProducts[] = ['id'=>$p['id'],'name'=>$p['product_name'],'sku'=>$p['sku'],'quantity'=>$qty,'price'=>$itemPrice,'purity'=>$p['purity']];
}

$grandSub = $totalGold + $totalMaking + $totalDiamond;
$grandGst = $grandSub * 0.03;
$grandTotal = round($grandSub + $grandGst);

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();
        $phone = preg_replace('/[^0-9+]/', '', $_POST['customer_phone']);
        $firstName = trim($_POST['first_name']);
        $lastName = trim($_POST['last_name']);
        $fullName = trim($firstName . ' ' . $lastName);

        $userId = $_SESSION['user_id'] ?? null;
        if(!$userId) {
            $chk = $pdo->query("SELECT id FROM users WHERE id=1")->fetch();
            if($chk) { $userId=1; }
            else {
                $dp = password_hash('NetanisGuest2026', PASSWORD_BCRYPT);
                $pdo->prepare("INSERT INTO users (id,full_name,email,password_hash,phone,role) VALUES (1,'Guest User','guest@netanis.com',?,?,'customer')")->execute([$dp,$phone]);
                $userId=1;
            }
        }

        $os = $pdo->prepare("INSERT INTO orders (user_id,total_amount,gst_amount,order_status,payment_status,shipping_name,shipping_phone,shipping_address,shipping_city,shipping_state,shipping_pincode) VALUES (?,?,?,'Pending','Unpaid',?,?,?,?,?,?)");
        $os->execute([$userId,$grandTotal,$grandGst,$fullName,$phone,trim($_POST['street_address']),trim($_POST['city']),trim($_POST['state']),trim($_POST['pincode'])]);
        $orderId = $pdo->lastInsertId();

        $is = $pdo->prepare("INSERT INTO order_items (order_id,product_id,quantity,price_at_purchase) VALUES (?,?,?,?)");
        foreach($checkoutProducts as $cp) { $is->execute([$orderId,$cp['id'],$cp['quantity'],$cp['price']]); }

        $pdo->commit();
        $_SESSION['jewellery_cart'] = [];

        // Send emails
        sendOrderNotifications($orderId, $pdo);

        // Build WhatsApp message
        $waItems = '';
        foreach($checkoutProducts as $cp) { $waItems .= "• {$cp['name']} (Qty:{$cp['quantity']}) — ₹" . number_format($cp['price']*$cp['quantity']) . "\n"; }
        $orderRef = '#NETANIS-' . str_pad($orderId, 5, '0', STR_PAD_LEFT);
        $waText  = "✨ *" . STORE_NAME . "* — Order Confirmed!\n\n";
        $waText .= "Hello *{$fullName}*! Your order is confirmed.\n\n";
        $waText .= "*Order ID:* {$orderRef}\n";
        $waText .= "*Items:*\n{$waItems}";
        $waText .= "\n*Total Payable:* ₹" . number_format($grandTotal) . " (Incl. 3% GST)\n";
        $waText .= "*Delivery to:* " . trim($_POST['street_address']) . ", " . trim($_POST['city']) . " - " . trim($_POST['pincode']) . "\n\n";
        $waText .= "Track: http://localhost/premium-jewellery/client/dashboard.php\n";
        $waText .= "Questions? WhatsApp us: " . STORE_WHATSAPP;

        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        $waUrl = "https://wa.me/" . $cleanPhone . "?text=" . rawurlencode($waText);

        header("Location: order-success.php?id={$orderId}&total={$grandTotal}&phone=" . urlencode($phone) . "&wa=" . urlencode($waUrl) . "&ref=" . urlencode($orderRef));
        exit();
    } catch(Exception $e) {
        $pdo->rollBack();
        $formError = "Order failed. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout | Netanis Jewelos</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
body { font-family: 'DM Sans', sans-serif; background: #faf9f7; color: #2d2926; }
.font-display { font-family: 'Cormorant Garamond', serif; }
:root { --gold: #b8860b; --border: #e8e0d5; --warm: #f5efe7; }
.input-field { width: 100%; background: white; border: 1.5px solid var(--border); border-radius: 10px; padding: 12px 16px; font-size: 13px; color: #2d2926; outline: none; transition: border-color 0.2s; font-family: 'DM Sans', sans-serif; }
.input-field:focus { border-color: var(--gold); }
.label { display: block; font-size: 10px; font-weight: 600; letter-spacing: 1.5px; text-transform: uppercase; color: #8c7b6b; margin-bottom: 6px; }
.btn-gold { background: #b8860b; color: white; border-radius: 10px; transition: all 0.3s; border: none; cursor: pointer; }
.btn-gold:hover { background: #9a7009; transform: translateY(-1px); }
.step-badge { width: 28px; height: 28px; background: #b8860b; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; flex-shrink: 0; }
</style>
</head>
<body>
<?php include 'components/header.php'; ?>

<div class="max-w-6xl mx-auto px-6 py-12 grid grid-cols-1 lg:grid-cols-5 gap-10">
  
  <!-- FORM: 3 cols -->
  <div class="lg:col-span-3">
    <h1 class="font-display text-3xl font-semibold text-[#2d2926] mb-1">Secure Checkout</h1>
    <p class="text-[#8c7b6b] text-xs mb-8 uppercase tracking-widest">Complete your order</p>

    <?php if(isset($formError)): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl mb-6 text-sm"><?php echo $formError; ?></div>
    <?php endif; ?>

    <form method="POST" action="checkout.php" class="space-y-8">

      <!-- SECTION 1: Contact -->
      <div class="bg-white rounded-2xl border border-[#e8e0d5] p-6 shadow-sm">
        <div class="flex items-center space-x-3 mb-5">
          <div class="step-badge">1</div>
          <h2 class="font-display text-lg font-semibold">Contact Details</h2>
        </div>
        <div class="grid grid-cols-2 gap-4 mb-4">
          <div>
            <label class="label">First Name *</label>
            <input type="text" name="first_name" required value="<?php echo isset($_SESSION['user_name']) ? explode(' ',$_SESSION['user_name'])[0] : ''; ?>" placeholder="Piyush" class="input-field">
          </div>
          <div>
            <label class="label">Last Name *</label>
            <input type="text" name="last_name" required placeholder="Mehta" class="input-field">
          </div>
        </div>
        <div>
          <label class="label">WhatsApp Number (with country code) *</label>
          <input type="tel" name="customer_phone" required placeholder="919876543210" class="input-field font-mono">
          <p class="text-[10px] text-[#8c7b6b] mt-1.5">📲 Your order confirmation will be sent to this WhatsApp number</p>
        </div>
      </div>

      <!-- SECTION 2: Address -->
      <div class="bg-white rounded-2xl border border-[#e8e0d5] p-6 shadow-sm">
        <div class="flex items-center space-x-3 mb-5">
          <div class="step-badge">2</div>
          <h2 class="font-display text-lg font-semibold">Delivery Address</h2>
        </div>
        <div class="space-y-4">
          <div>
            <label class="label">Street Address *</label>
            <input type="text" name="street_address" required placeholder="Flat, House no., Apartment, Locality" class="input-field">
          </div>
          <div class="grid grid-cols-3 gap-4">
            <div>
              <label class="label">City *</label>
              <input type="text" name="city" required placeholder="Jodhpur" class="input-field">
            </div>
            <div>
              <label class="label">State *</label>
              <input type="text" name="state" required placeholder="Rajasthan" class="input-field">
            </div>
            <div>
              <label class="label">Pincode *</label>
              <input type="text" name="pincode" required placeholder="342001" maxlength="6" class="input-field font-mono">
            </div>
          </div>
        </div>
      </div>

      <!-- SECTION 3: Payment -->
      <div class="bg-white rounded-2xl border border-[#e8e0d5] p-6 shadow-sm">
        <div class="flex items-center space-x-3 mb-5">
          <div class="step-badge">3</div>
          <h2 class="font-display text-lg font-semibold">Payment Method</h2>
        </div>
        <div class="flex items-center space-x-3 p-4 bg-[#f5efe7] border border-[#e8e0d5] rounded-xl">
          <div class="w-4 h-4 rounded-full border-2 border-[#b8860b] flex items-center justify-center">
            <div class="w-2 h-2 bg-[#b8860b] rounded-full"></div>
          </div>
          <div>
            <div class="text-sm font-semibold">Cash on Delivery (COD)</div>
            <div class="text-[11px] text-[#8c7b6b]">Pay when your jewellery arrives at your doorstep</div>
          </div>
          <div class="ml-auto text-xl">💵</div>
        </div>
        <div class="mt-3 bg-amber-50 border border-amber-200 rounded-lg p-3 text-[11px] text-amber-800">
          📦 Free delivery on all orders · 5-7 working days · You'll receive a WhatsApp confirmation with tracking details
        </div>
      </div>

      <button type="submit" class="btn-gold w-full py-4 text-sm font-semibold tracking-wider uppercase">
        ✓ Confirm Order — ₹<?php echo number_format($grandTotal); ?>
      </button>
      <p class="text-center text-[10px] text-[#8c7b6b] uppercase tracking-widest">🔒 Secure & Encrypted · BIS Hallmark Certified</p>
    </form>
  </div>

  <!-- ORDER SUMMARY: 2 cols -->
  <div class="lg:col-span-2">
    <div class="bg-white rounded-2xl border border-[#e8e0d5] shadow-sm sticky top-28 overflow-hidden">
      <div class="bg-[#2d2926] px-6 py-4">
        <h2 class="font-display text-lg text-white font-semibold">Order Summary</h2>
        <p class="text-[#8c7b6b] text-[10px] uppercase tracking-widest mt-0.5"><?php echo count($checkoutProducts); ?> item(s)</p>
      </div>

      <!-- Items -->
      <div class="p-5 border-b border-[#e8e0d5] space-y-4 max-h-60 overflow-y-auto">
        <?php foreach($checkoutProducts as $cp):
          $pRow = null; foreach($products as $pr) { if($pr['id']==$cp['id']) { $pRow=$pr; break; } }
          $imgSrc = $pRow ? (str_contains($pRow['primary_image'],'http') ? $pRow['primary_image'] : 'assets/uploads/'.basename($pRow['primary_image'])) : '';
        ?>
        <div class="flex items-center space-x-3">
          <div class="w-14 h-14 bg-[#f5efe7] rounded-xl overflow-hidden border border-[#e8e0d5] shrink-0">
            <img src="<?php echo $imgSrc; ?>" class="w-full h-full object-cover" alt="<?php echo htmlspecialchars($cp['name']); ?>">
          </div>
          <div class="flex-1 min-w-0">
            <div class="text-xs font-semibold text-[#2d2926] truncate"><?php echo htmlspecialchars($cp['name']); ?></div>
            <div class="text-[10px] text-[#8c7b6b] font-mono"><?php echo $cp['purity']; ?> · Qty: <?php echo $cp['quantity']; ?></div>
          </div>
          <div class="text-sm font-semibold text-[#2d2926] shrink-0">₹<?php echo number_format($cp['price'] * $cp['quantity']); ?></div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Bill -->
      <div class="p-5 space-y-3 text-xs">
        <div class="flex justify-between text-[#5c5047]">
          <span>Gold Cost</span><span class="font-medium">₹<?php echo number_format($totalGold); ?></span>
        </div>
        <div class="flex justify-between text-[#5c5047]">
          <span>Making Charges</span><span class="font-medium">₹<?php echo number_format($totalMaking); ?></span>
        </div>
        <?php if($totalDiamond > 0): ?>
        <div class="flex justify-between text-[#5c5047]">
          <span>Diamond Cost</span><span class="font-medium">₹<?php echo number_format($totalDiamond); ?></span>
        </div>
        <?php endif; ?>
        <?php if($totalSavings > 0): ?>
        <div class="flex justify-between text-emerald-600">
          <span>💚 You Save</span><span class="font-semibold">-₹<?php echo number_format($totalSavings); ?></span>
        </div>
        <?php endif; ?>
        <div class="flex justify-between text-[#5c5047]">
          <span>GST (3%)</span><span class="font-medium">₹<?php echo number_format($grandGst); ?></span>
        </div>
        <div class="flex justify-between text-[#5c5047]">
          <span>Delivery</span><span class="font-medium text-emerald-600">FREE</span>
        </div>
        <div class="border-t border-[#e8e0d5] pt-3 flex justify-between items-baseline">
          <span class="font-semibold text-sm text-[#2d2926]">Total Payable</span>
          <span class="font-display text-2xl font-bold text-[#b8860b]">₹<?php echo number_format($grandTotal); ?></span>
        </div>
      </div>
    </div>
  </div>

</div>
<?php include 'components/footer.php'; ?>
</body>
</html>
