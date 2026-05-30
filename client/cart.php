<?php
session_start();
require_once '../config/db.php';

// Handle remove
if(isset($_GET['action']) && $_GET['action'] === 'remove' && isset($_GET['id'])) {
    unset($_SESSION['jewellery_cart'][intval($_GET['id'])]);
    header("Location: cart.php"); exit();
}

// Handle qty update
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_qty'])) {
    $pid = intval($_POST['product_id']);
    $qty = max(1, intval($_POST['qty']));
    if(isset($_SESSION['jewellery_cart'][$pid])) $_SESSION['jewellery_cart'][$pid] = $qty;
    header("Location: cart.php"); exit();
}

$latestRate = $pdo->query("SELECT * FROM gold_rates ORDER BY id DESC LIMIT 1")->fetch();
$cartItems = [];
$totalGold = $totalMaking = $totalDiamond = $totalSavings = 0;

if(!empty($_SESSION['jewellery_cart'])) {
    $ids = implode(',', array_map('intval', array_keys($_SESSION['jewellery_cart'])));
    $prods = $pdo->query("SELECT * FROM products WHERE id IN ($ids) AND is_active=1")->fetchAll();
    foreach($prods as $p) {
        $qty = $_SESSION['jewellery_cart'][$p['id']];
        $rate = match($p['purity']) {
            '24Kt' => $latestRate['rate_24kt'], '18Kt' => $latestRate['rate_18kt'],
            '14Kt' => $latestRate['rate_14kt'], default => $latestRate['rate_22kt']
        };
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
        $sub = $goldC + $makingFinal + $diamC;
        $unit = round($sub * 1.03);
        $totalGold += $goldC * $qty;
        $totalMaking += $makingFinal * $qty;
        $totalDiamond += $diamC * $qty;
        $totalSavings += ($makingD + ($p['has_diamond']&&$p['diamond_carat']>0?(($p['diamond_carat']*($latestRate['diamond_rate_per_ct']??25000)) * ($p['diamond_discount_percent']??0)/100):0)) * $qty;
        $cartItems[] = ['p'=>$p, 'qty'=>$qty, 'unit'=>$unit, 'imgSrc'=>str_contains($p['primary_image'],'http')?$p['primary_image']:'assets/uploads/'.basename($p['primary_image'])];
    }
}
$grandSub = $totalGold + $totalMaking + $totalDiamond;
$grandGst = $grandSub * 0.03;
$grandTotal = round($grandSub + $grandGst);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Shopping Bag | Netanis Jewelos</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
body { font-family: 'DM Sans', sans-serif; background: #faf9f7; color: #2d2926; }
.font-display { font-family: 'Cormorant Garamond', serif; }
:root { --gold: #b8860b; --border: #e8e0d5; }
.btn-gold { background: #b8860b; color: white; transition: all 0.3s; }
.btn-gold:hover { background: #9a7009; transform: translateY(-1px); }
.qty-btn { width: 28px; height: 28px; border: 1.5px solid #e8e0d5; border-radius: 6px; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:14px; transition: all 0.2s; }
.qty-btn:hover { border-color: #b8860b; color: #b8860b; }
</style>
</head>
<body>
<?php include 'components/header.php'; ?>

<div class="max-w-7xl mx-auto px-6 py-12">
  <!-- Breadcrumb -->
  <div class="text-xs text-[#8c7b6b] flex items-center space-x-2 mb-8">
    <a href="index.php" class="hover:text-[#b8860b]">Home</a><span>/</span>
    <span class="text-[#2d2926]">Shopping Bag</span>
  </div>

  <h1 class="font-display text-4xl font-semibold text-[#2d2926] mb-8">Shopping Bag <span class="text-xl text-[#8c7b6b] font-light">(<?php echo count($cartItems); ?> item<?php echo count($cartItems)!=1?'s':''; ?>)</span></h1>

  <?php if(!empty($cartItems)): ?>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- ITEMS -->
    <div class="lg:col-span-2 space-y-4">
      <?php foreach($cartItems as $ci): ?>
      <div class="bg-white rounded-2xl border border-[#e8e0d5] p-5 shadow-sm flex items-start space-x-4">
        <a href="product-details.php?id=<?php echo $ci['p']['id']; ?>" class="shrink-0">
          <img src="<?php echo $ci['imgSrc']; ?>" class="w-24 h-24 object-cover rounded-xl border border-[#e8e0d5] bg-[#f5efe7]">
        </a>
        <div class="flex-1 min-w-0">
          <div class="flex justify-between items-start">
            <div>
              <p class="text-[10px] uppercase tracking-widest text-[#b8860b] font-semibold mb-1"><?php echo $ci['p']['purity']; ?> <?php echo $ci['p']['metal_type']; ?><?php if($ci['p']['has_diamond']): ?> · Diamond<?php endif; ?></p>
              <h3 class="font-display text-base font-semibold text-[#2d2926] mb-1">
                <a href="product-details.php?id=<?php echo $ci['p']['id']; ?>" class="hover:text-[#b8860b]"><?php echo htmlspecialchars($ci['p']['product_name']); ?></a>
              </h3>
              <p class="text-[10px] text-[#8c7b6b] font-mono">SKU: <?php echo htmlspecialchars($ci['p']['sku']); ?></p>
              <?php if($ci['p']['making_discount_percent']>0||$ci['p']['diamond_discount_percent']>0): ?>
              <span class="inline-block mt-1 text-[9px] bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full font-semibold">Discount Applied</span>
              <?php endif; ?>
            </div>
            <div class="text-right">
              <div class="font-display text-xl font-bold text-[#2d2926]">₹<?php echo number_format($ci['unit'] * $ci['qty']); ?></div>
              <div class="text-[10px] text-[#8c7b6b]">₹<?php echo number_format($ci['unit']); ?>/pc · Incl. GST</div>
            </div>
          </div>
          <div class="flex items-center justify-between mt-4">
            <!-- Qty -->
            <form method="POST" class="flex items-center space-x-2">
              <input type="hidden" name="product_id" value="<?php echo $ci['p']['id']; ?>">
              <div class="qty-btn" onclick="adjustQty(this, -1)">−</div>
              <input type="number" name="qty" value="<?php echo $ci['qty']; ?>" min="1" max="10"
                class="w-12 text-center border border-[#e8e0d5] rounded-lg py-1 text-sm font-semibold focus:outline-none focus:border-[#b8860b] qty-input">
              <div class="qty-btn" onclick="adjustQty(this, 1)">+</div>
              <button type="submit" name="update_qty" class="text-[10px] text-[#b8860b] hover:underline font-semibold uppercase tracking-wider ml-1">Update</button>
            </form>
            <a href="cart.php?action=remove&id=<?php echo $ci['p']['id']; ?>" class="text-[10px] text-red-400 hover:text-red-600 font-medium uppercase tracking-wider flex items-center space-x-1">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
              <span>Remove</span>
            </a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>

      <div class="bg-[#fffbf0] border border-[#fde68a] rounded-2xl p-4 text-xs text-amber-800">
        ✨ <strong>BIS Hallmark Certified</strong> · 7-Day Easy Returns · Free Delivery Pan India · COD Available
      </div>
    </div>

    <!-- SUMMARY -->
    <div>
      <div class="bg-white rounded-2xl border border-[#e8e0d5] shadow-sm overflow-hidden sticky top-28">
        <div class="bg-[#2d2926] px-6 py-4">
          <h2 class="font-display text-lg text-white font-semibold">Price Summary</h2>
        </div>
        <div class="p-6 space-y-3 text-sm border-b border-[#e8e0d5]">
          <div class="flex justify-between text-[#5c5047]"><span>Gold Cost</span><span class="font-medium">₹<?php echo number_format($totalGold); ?></span></div>
          <div class="flex justify-between text-[#5c5047]"><span>Making Charges</span><span class="font-medium">₹<?php echo number_format($totalMaking); ?></span></div>
          <?php if($totalDiamond > 0): ?>
          <div class="flex justify-between text-[#5c5047]"><span>Diamond Cost</span><span class="font-medium">₹<?php echo number_format($totalDiamond); ?></span></div>
          <?php endif; ?>
          <?php if($totalSavings > 0): ?>
          <div class="flex justify-between text-emerald-600 font-semibold"><span>💚 You Save</span><span>-₹<?php echo number_format($totalSavings); ?></span></div>
          <?php endif; ?>
          <div class="flex justify-between text-[#5c5047]"><span>GST (3%)</span><span class="font-medium">₹<?php echo number_format($grandGst); ?></span></div>
          <div class="flex justify-between text-[#5c5047]"><span>Delivery</span><span class="font-medium text-emerald-600">FREE</span></div>
        </div>
        <div class="p-6">
          <div class="flex justify-between items-baseline mb-6">
            <span class="font-semibold text-[#2d2926]">Total Payable</span>
            <span class="font-display text-3xl font-bold text-[#b8860b]">₹<?php echo number_format($grandTotal); ?></span>
          </div>
          <a href="checkout.php" class="btn-gold w-full py-4 rounded-xl text-sm font-semibold tracking-wider uppercase text-center block">
            Proceed to Checkout →
          </a>
          <a href="shop.php" class="mt-3 block text-center text-xs text-[#8c7b6b] hover:text-[#b8860b] transition-colors">← Continue Shopping</a>
        </div>
      </div>
    </div>
  </div>

  <?php else: ?>
  <div class="bg-white rounded-2xl border border-[#e8e0d5] p-20 text-center shadow-sm">
    <div class="text-6xl mb-6">🛍️</div>
    <h2 class="font-display text-3xl font-semibold text-[#2d2926] mb-3">Your bag is empty</h2>
    <p class="text-[#8c7b6b] font-light mb-8">Discover our exquisite collection of handcrafted jewellery</p>
    <a href="shop.php" class="btn-gold px-10 py-4 rounded-xl text-sm font-semibold tracking-wider uppercase inline-block">Browse Collection</a>
  </div>
  <?php endif; ?>
</div>

<?php include 'components/footer.php'; ?>

<script>
function adjustQty(btn, delta) {
  const form = btn.closest('form');
  const input = form.querySelector('.qty-input');
  const newVal = Math.max(1, Math.min(10, parseInt(input.value||1) + delta));
  input.value = newVal;
}
</script>
</body>
</html>
