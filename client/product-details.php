<?php
session_start();
require_once '../config/db.php';
$id = intval($_GET['id'] ?? 0);
$product = $pdo->prepare("SELECT * FROM products WHERE id=? AND is_active=1");
$product->execute([$id]);
$item = $product->fetch();
if(!$item) { header("Location: shop.php"); exit(); }
$latestRate = $pdo->query("SELECT * FROM gold_rates ORDER BY id DESC LIMIT 1")->fetch();

// Price Calculation
$ratePerGram = $latestRate['rate_22kt'] ?? 6820;
switch($item['purity']) {
    case '24Kt': $ratePerGram = $latestRate['rate_24kt']; break;
    case '22Kt': $ratePerGram = $latestRate['rate_22kt']; break;
    case '18Kt': $ratePerGram = $latestRate['rate_18kt']; break;
    case '14Kt': $ratePerGram = $latestRate['rate_14kt']; break;
}
$goldCost = $item['weight_grams'] * $ratePerGram;
$makingFull = $item['weight_grams'] * $item['making_charges_per_gram'];
$makingDisc = ($makingFull * ($item['making_discount_percent'] ?? 0)) / 100;
$makingFinal = $makingFull - $makingDisc;
$diamondCostFull = 0; $diamondCostFinal = 0;
if($item['has_diamond'] && $item['diamond_carat'] > 0) {
    $diamondCostFull = $item['diamond_carat'] * ($latestRate['diamond_rate_per_ct'] ?? 25000);
    $dDisc = ($diamondCostFull * ($item['diamond_discount_percent'] ?? 0)) / 100;
    $diamondCostFinal = $diamondCostFull - $dDisc;
}
$subTotal = $goldCost + $makingFinal + $diamondCostFinal;
$gstAmount = $subTotal * 0.03;
$finalPrice = round($subTotal + $gstAmount);

// Wishlist check
$inWishlist = false;
if(isset($_SESSION['user_id'])) {
    $wc = $pdo->prepare("SELECT id FROM wishlist WHERE user_id=? AND product_id=?");
    $wc->execute([$_SESSION['user_id'], $item['id']]);
    $inWishlist = (bool)$wc->fetch();
}

// Related products
$relStmt = $pdo->prepare("SELECT * FROM products WHERE category=? AND id!=? AND is_active=1 LIMIT 4");
$relStmt->execute([$item['category'], $item['id']]);
$relatedProducts = $relStmt->fetchAll();

// Parse JSON fields
$prodDetails = $item['product_details_json'] ? json_decode($item['product_details_json'], true) : [];
$goldDetails = $item['gold_details_json'] ? json_decode($item['gold_details_json'], true) : [];
$diamondDetails = $item['diamond_details_json'] ? json_decode($item['diamond_details_json'], true) : [];

$imgSrc = str_contains($item['primary_image'], 'http') ? $item['primary_image'] : 'assets/uploads/' . basename($item['primary_image']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($item['product_name']); ?> | Netanis Jewelos</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
body { font-family: 'DM Sans', sans-serif; background: #faf9f7; color: #2d2926; }
.font-display { font-family: 'Cormorant Garamond', serif; }
:root { --gold: #b8860b; --cream: #faf9f7; --warm: #f5efe7; --dark: #2d2926; --muted: #8c7b6b; --border: #e8e0d5; }
.tab-btn { border-bottom: 2px solid transparent; transition: all 0.2s; color: #8c7b6b; font-size: 12px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; padding: 12px 0; cursor: pointer; }
.tab-btn.active { border-bottom-color: var(--gold); color: var(--dark); }
.tab-btn:hover:not(.active) { color: var(--dark); }
.tab-pane { display: none; }
.tab-pane.active { display: block; }
.price-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #f0ebe4; }
.price-row:last-child { border-bottom: none; }
.badge { background: #b8860b; color: white; font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; padding: 2px 8px; border-radius: 999px; }
.badge-disc { background: #10b981; }
.btn-gold { background: #b8860b; color: white; transition: all 0.3s; border: 1.5px solid #b8860b; }
.btn-gold:hover { background: #9a7009; }
.btn-outline-gold { border: 1.5px solid #b8860b; color: #b8860b; transition: all 0.3s; background: transparent; }
.btn-outline-gold:hover { background: #b8860b; color: white; }
.detail-table tr td:first-child { color: #8c7b6b; font-size: 12px; width: 45%; padding: 8px 0; }
.detail-table tr td:last-child { font-size: 12px; font-weight: 500; color: #2d2926; }
.img-thumb { border: 2px solid transparent; transition: all 0.2s; cursor: pointer; }
.img-thumb.active, .img-thumb:hover { border-color: #b8860b; }
.gold-line { background: linear-gradient(90deg, transparent, #b8860b, transparent); height: 1px; }
.card-hover { transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1); }
.card-hover:hover { transform: translateY(-4px); box-shadow: 0 16px 48px rgba(45,41,38,0.1); }
.img-zoom img { transition: transform 0.7s cubic-bezier(0.25, 0.8, 0.25, 1); }
.img-zoom:hover img { transform: scale(1.05); }
</style>
</head>
<body>
<?php include 'components/header.php'; ?>

<!-- BREADCRUMB -->
<div class="max-w-7xl mx-auto px-8 py-4">
  <div class="text-xs text-[#8c7b6b] flex items-center space-x-2">
    <a href="index.php" class="hover:text-[#b8860b]">Home</a>
    <span>/</span>
    <a href="shop.php" class="hover:text-[#b8860b]">Jewellery</a>
    <span>/</span>
    <a href="shop.php?cat=<?php echo urlencode($item['category']); ?>" class="hover:text-[#b8860b]"><?php echo htmlspecialchars($item['category']); ?></a>
    <span>/</span>
    <span class="text-[#2d2926]"><?php echo htmlspecialchars($item['product_name']); ?></span>
  </div>
</div>

<!-- MAIN PRODUCT AREA -->
<div class="max-w-7xl mx-auto px-8 pb-16 grid grid-cols-1 lg:grid-cols-2 gap-14">

  <!-- LEFT: IMAGE -->
  <div>
    <div class="bg-white rounded-2xl overflow-hidden border border-[#e8e0d5] shadow-sm mb-4" style="aspect-ratio:1;">
      <img id="mainProductImg" src="<?php echo $imgSrc; ?>" class="w-full h-full object-cover" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
    </div>
    <?php
    $extraImages = $item['extra_images'] ? json_decode($item['extra_images'], true) : [];
    $allImages = array_merge([$item['primary_image']], $extraImages);
    if(count($allImages) > 1): ?>
    <div class="flex space-x-3 mt-3">
      <?php foreach($allImages as $i => $img):
        $imgUrl = str_contains($img,'http') ? $img : 'assets/uploads/'.basename($img);
      ?>
      <div onclick="document.getElementById('mainProductImg').src='<?php echo $imgUrl; ?>'; document.querySelectorAll('.img-thumb').forEach(t=>t.classList.remove('active')); this.classList.add('active');"
           class="img-thumb w-16 h-16 rounded-xl overflow-hidden bg-[#f5efe7] border border-[#e8e0d5] <?php echo $i===0?'active':''; ?>">
        <img src="<?php echo $imgUrl; ?>" class="w-full h-full object-cover">
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

  <!-- RIGHT: DETAILS -->
  <div>
    <div class="mb-1 flex items-center space-x-2">
      <span class="badge"><?php echo $item['purity']; ?> <?php echo $item['metal_type']; ?></span>
      <?php if($item['has_diamond']): ?><span class="badge">Diamond</span><?php endif; ?>
      <?php if($item['collection_tag']): ?><span class="text-[10px] text-[#8c7b6b] uppercase tracking-widest"><?php echo htmlspecialchars($item['collection_tag']); ?></span><?php endif; ?>
    </div>
    <h1 class="font-display text-4xl font-semibold text-[#2d2926] leading-tight mb-2 mt-2"><?php echo htmlspecialchars($item['product_name']); ?></h1>
    <p class="text-xs text-[#8c7b6b] font-mono mb-4">SKU: <?php echo htmlspecialchars($item['sku']); ?></p>
    <p class="text-[#5c5047] text-sm font-light leading-relaxed mb-6"><?php echo htmlspecialchars($item['short_description']); ?></p>

    <!-- PRICE -->
    <div class="bg-white rounded-2xl border border-[#e8e0d5] p-5 mb-6 shadow-sm">
      <div class="flex items-baseline space-x-3 mb-2">
        <div class="font-display text-4xl font-semibold text-[#2d2926]">₹<?php echo number_format($finalPrice); ?></div>
        <div class="text-[10px] text-[#8c7b6b] font-light">Incl. 3% GST • Making Charges</div>
      </div>
      <?php if($item['making_discount_percent'] > 0 || $item['diamond_discount_percent'] > 0): ?>
      <div class="flex flex-wrap gap-2 mt-2">
        <?php if($item['making_discount_percent'] > 0): ?><span class="badge badge-disc"><?php echo $item['making_discount_percent']; ?>% off Making</span><?php endif; ?>
        <?php if($item['diamond_discount_percent'] > 0): ?><span class="badge badge-disc"><?php echo $item['diamond_discount_percent']; ?>% off Diamond</span><?php endif; ?>
      </div>
      <?php endif; ?>
      <div class="text-[10px] text-[#8c7b6b] mt-2 font-light">* If there is any difference in metal weight, an additional payment or refund will be applicable.</div>
    </div>

    <!-- PCJ-STYLE TABS -->
    <div class="bg-white rounded-2xl border border-[#e8e0d5] overflow-hidden shadow-sm mb-6">
      <div class="flex border-b border-[#e8e0d5] px-6 space-x-6">
        <button class="tab-btn active" onclick="switchTab('price')">Price Breakup</button>
        <button class="tab-btn" onclick="switchTab('product')">Product Details</button>
        <button class="tab-btn" onclick="switchTab('gold')">Gold Details</button>
        <?php if($item['has_diamond']): ?><button class="tab-btn" onclick="switchTab('diamond')">Diamond Details</button><?php endif; ?>
      </div>

      <div class="p-6">
        <!-- PRICE BREAKUP TAB -->
        <div id="tab-price" class="tab-pane active">
          <div class="price-row">
            <span class="text-sm text-[#2d2926]">Gold</span>
            <span class="font-semibold text-[#2d2926] text-sm">₹<?php echo number_format($goldCost); ?></span>
          </div>
          <?php if($item['has_diamond'] && $item['diamond_carat'] > 0): ?>
          <div class="price-row">
            <div>
              <span class="text-sm text-[#2d2926]">Diamond</span>
              <?php if($item['diamond_discount_percent'] > 0): ?>
              <span class="text-[#b8860b] text-xs ml-2">(<?php echo $item['diamond_discount_percent']; ?>%)</span>
              <?php endif; ?>
            </div>
            <div class="text-right">
              <?php if($item['diamond_discount_percent'] > 0): ?>
              <span class="text-xs text-[#8c7b6b] line-through mr-2">₹<?php echo number_format($diamondCostFull); ?></span>
              <?php endif; ?>
              <span class="font-semibold text-[#2d2926] text-sm">₹<?php echo number_format($diamondCostFinal); ?></span>
            </div>
          </div>
          <?php endif; ?>
          <div class="price-row">
            <div>
              <span class="text-sm text-[#2d2926]">Making Charges</span>
              <?php if($item['making_discount_percent'] > 0): ?>
              <span class="text-[#b8860b] text-xs ml-2">(<?php echo $item['making_discount_percent']; ?>%)</span>
              <?php endif; ?>
            </div>
            <div class="text-right">
              <?php if($item['making_discount_percent'] > 0): ?>
              <span class="text-xs text-[#8c7b6b] line-through mr-2">₹<?php echo number_format($makingFull); ?></span>
              <?php endif; ?>
              <span class="font-semibold text-[#2d2926] text-sm">₹<?php echo number_format($makingFinal); ?></span>
            </div>
          </div>
          <div class="price-row">
            <span class="text-sm text-[#2d2926]">GST (3%)</span>
            <span class="font-semibold text-[#2d2926] text-sm">₹<?php echo number_format($gstAmount); ?></span>
          </div>
          <div class="price-row" style="border-bottom:none; padding-top:16px; padding-bottom:0;">
            <span class="font-semibold text-base text-[#2d2926]">Total</span>
            <span class="font-display text-2xl font-bold text-[#b8860b]">₹<?php echo number_format($finalPrice); ?></span>
          </div>
        </div>

        <!-- PRODUCT DETAILS TAB -->
        <div id="tab-product" class="tab-pane">
          <?php if(!empty($prodDetails)): ?>
          <table class="detail-table w-full">
            <?php foreach($prodDetails as $k => $v): ?>
            <tr><td><?php echo htmlspecialchars($k); ?></td><td><?php echo htmlspecialchars($v); ?></td></tr>
            <?php endforeach; ?>
          </table>
          <?php elseif($item['full_description']): ?>
          <p class="text-sm text-[#5c5047] font-light leading-relaxed"><?php echo nl2br(htmlspecialchars($item['full_description'])); ?></p>
          <?php else: ?>
          <p class="text-sm text-[#8c7b6b] italic">No product details available.</p>
          <?php endif; ?>
        </div>

        <!-- GOLD DETAILS TAB -->
        <div id="tab-gold" class="tab-pane">
          <?php
          $gd = !empty($goldDetails) ? $goldDetails : [
            'Metal' => $item['metal_type'],
            'Purity' => $item['purity'],
            'Weight' => $item['weight_grams'] . ' g',
            'Hallmark' => 'BIS Certified',
          ];
          ?>
          <table class="detail-table w-full">
            <?php foreach($gd as $k => $v): ?>
            <tr><td><?php echo htmlspecialchars($k); ?></td><td><?php echo htmlspecialchars($v); ?></td></tr>
            <?php endforeach; ?>
            <tr><td>Rate Applied (<?php echo $item['purity']; ?>)</td><td>₹<?php echo number_format($ratePerGram); ?>/g</td></tr>
          </table>
        </div>

        <!-- DIAMOND DETAILS TAB -->
        <?php if($item['has_diamond']): ?>
        <div id="tab-diamond" class="tab-pane">
          <?php
          $dd = !empty($diamondDetails) ? $diamondDetails : [
            'Diamond Type' => 'Natural',
            'Total Carat' => $item['diamond_carat'] . ' ct',
            'Rate Applied' => '₹' . number_format($latestRate['diamond_rate_per_ct'] ?? 25000) . '/ct',
          ];
          ?>
          <table class="detail-table w-full">
            <?php foreach($dd as $k => $v): ?>
            <tr><td><?php echo htmlspecialchars($k); ?></td><td><?php echo htmlspecialchars($v); ?></td></tr>
            <?php endforeach; ?>
          </table>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- CTA BUTTONS -->
    <div class="space-y-3">
      <button onclick="addToCartAjax(<?php echo $item['id']; ?>, '<?php echo addslashes($item['product_name']); ?>', this)" class="btn-gold w-full py-4 rounded-xl text-sm font-semibold tracking-wider uppercase text-center block cursor-pointer">🛒 Add to Cart Bag</button>
      <button id="wishBtn" onclick="toggleWish(<?php echo $item['id']; ?>)"
        class="w-full py-3.5 rounded-xl text-sm font-semibold tracking-wider uppercase <?php echo $inWishlist ? 'bg-red-50 border border-red-200 text-red-500 hover:bg-red-100' : 'btn-outline-gold'; ?> transition-all">
        <?php echo $inWishlist ? '❤️ Remove from Wishlist' : '🤍 Add to Wishlist'; ?>
      </button>
      <div class="flex items-center justify-center space-x-6 text-[10px] text-[#8c7b6b] uppercase tracking-wider pt-2">
        <span>🔒 BIS Hallmark</span>
        <span>🔄 7-Day Return</span>
        <span>🛡️ Lifetime Care</span>
      </div>
    </div>
  </div>
</div>

<!-- RELATED PRODUCTS -->
<?php if(!empty($relatedProducts)): ?>
<div class="bg-[#f5efe7] py-16">
  <div class="max-w-7xl mx-auto px-8">
    <div class="text-center mb-10">
      <p class="text-[10px] tracking-[3px] uppercase text-[#b8860b] font-semibold mb-2">You May Also Like</p>
      <h2 class="font-display text-3xl text-[#2d2926] font-semibold">Similar Masterpieces</h2>
      <div class="gold-line w-16 mx-auto mt-3"></div>
    </div>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
      <?php foreach($relatedProducts as $rel):
        $relRate = $latestRate['rate_22kt'] ?? 6820;
        switch($rel['purity']) {
          case '24Kt': $relRate = $latestRate['rate_24kt']; break;
          case '22Kt': $relRate = $latestRate['rate_22kt']; break;
          case '18Kt': $relRate = $latestRate['rate_18kt']; break;
          case '14Kt': $relRate = $latestRate['rate_14kt']; break;
        }
        $rGold = $rel['weight_grams'] * $relRate;
        $rMaking = $rel['weight_grams'] * $rel['making_charges_per_gram'];
        $rMakingD = ($rMaking * ($rel['making_discount_percent']??0)) / 100;
        $rDiam = 0;
        if($rel['has_diamond'] && $rel['diamond_carat']>0) {
          $rD = $rel['diamond_carat'] * ($latestRate['diamond_rate_per_ct']??25000);
          $rDd = ($rD * ($rel['diamond_discount_percent']??0))/100;
          $rDiam = $rD - $rDd;
        }
        $rSub = $rGold + ($rMaking - $rMakingD) + $rDiam;
        $rFinal = round($rSub * 1.03);
        $rImg = str_contains($rel['primary_image'],'http') ? $rel['primary_image'] : 'assets/uploads/'.basename($rel['primary_image']);
      ?>
      <a href="product-details.php?id=<?php echo $rel['id']; ?>" class="bg-white rounded-2xl overflow-hidden border border-[#e8e0d5] shadow-sm card-hover group block">
        <div class="img-zoom aspect-square overflow-hidden bg-[#f5efe7]">
          <img src="<?php echo $rImg; ?>" class="w-full h-full object-cover">
        </div>
        <div class="p-4">
          <p class="text-[9px] uppercase tracking-widest text-[#b8860b] font-semibold mb-1"><?php echo $rel['purity']; ?></p>
          <h3 class="font-display text-sm font-semibold text-[#2d2926] group-hover:text-[#b8860b] transition-colors mb-1"><?php echo htmlspecialchars($rel['product_name']); ?></h3>
          <div class="font-semibold text-[#2d2926] text-sm">₹<?php echo number_format($rFinal); ?></div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>

<?php include 'components/footer.php'; ?>

<script>
function switchTab(tab) {
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
  document.getElementById('tab-' + tab).classList.add('active');
  event.currentTarget.classList.add('active');
}

function toggleWish(id) {
  fetch('../api/toggle-wishlist.php?id=' + id)
    .then(r => r.json()).then(d => {
      const btn = document.getElementById('wishBtn');
      if(d.status === 'added') {
        btn.textContent = '❤️ Remove from Wishlist';
        btn.className = 'w-full py-3.5 rounded-xl text-sm font-semibold tracking-wider uppercase bg-red-50 border border-red-200 text-red-500 hover:bg-red-100 transition-all';
      } else if(d.status === 'removed') {
        btn.textContent = '🤍 Add to Wishlist';
        btn.className = 'w-full py-3.5 rounded-xl text-sm font-semibold tracking-wider uppercase btn-outline-gold transition-all';
      } else if(d.status === 'login_required') {
        window.location = 'login.php';
      }
    });
}
</script>
</body>
</html>
