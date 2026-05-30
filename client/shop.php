<?php
session_start();
require_once '../config/db.php';
$rate = $pdo->query("SELECT * FROM gold_rates ORDER BY id DESC LIMIT 1")->fetch();

$cat      = $_GET['cat'] ?? '';
$sub      = $_GET['sub'] ?? '';
$purity   = $_GET['purity'] ?? '';
$metal    = $_GET['metal'] ?? '';
$diamond  = $_GET['has_diamond'] ?? '';
$collection = $_GET['collection'] ?? '';
$sort     = $_GET['sort'] ?? 'newest';
$minPrice = intval($_GET['min_price'] ?? 0);
$maxPrice = intval($_GET['max_price'] ?? 999999);

function calcPrice($p, $rate) {
    $rG = match($p['purity']) {
        '24Kt' => $rate['rate_24kt'], '18Kt' => $rate['rate_18kt'],
        '14Kt' => $rate['rate_14kt'], default => $rate['rate_22kt']
    } ?? 6820;
    $m  = $p['weight_grams'] * $rG;
    $mk = $p['weight_grams'] * $p['making_charges_per_gram'] * (1 - $p['making_discount_percent'] / 100);
    $d  = 0;
    if ($p['has_diamond'] && $p['diamond_carat'] > 0 && $rate) {
        $d = $p['diamond_carat'] * $rate['diamond_rate_per_ct'] * (1 - $p['diamond_discount_percent'] / 100);
    }
    return round(($m + $mk + $d) * 1.03);
}

$sql = "SELECT * FROM products WHERE is_active=1";
$params = [];
if ($cat)       { $sql .= " AND category=?";      $params[] = $cat; }
if ($sub)       { $sql .= " AND subcategory=?";   $params[] = $sub; }
if ($purity)    { $sql .= " AND purity=?";        $params[] = $purity; }
if ($metal)     { $sql .= " AND metal_type=?";    $params[] = $metal; }
if ($diamond === '1') { $sql .= " AND has_diamond=1"; }
if ($collection){ $sql .= " AND collection_tag LIKE ?"; $params[] = "%{$collection}%"; }

$stmt = $pdo->prepare($sql); $stmt->execute($params);
$allProducts = $stmt->fetchAll();

// Calculate prices + apply price filter
$productsWithPrice = [];
foreach ($allProducts as $p) {
    $price = calcPrice($p, $rate ?? ['rate_22kt'=>6820,'rate_24kt'=>7450,'rate_18kt'=>5580,'rate_14kt'=>4340,'diamond_rate_per_ct'=>25000]);
    if ($minPrice > 0 && $price < $minPrice) continue;
    if ($maxPrice < 999999 && $price > $maxPrice) continue;
    $p['_price'] = $price;
    $productsWithPrice[] = $p;
}

// Sort
usort($productsWithPrice, function($a, $b) use ($sort) {
    return match($sort) {
        'price_asc'  => $a['_price'] - $b['_price'],
        'price_desc' => $b['_price'] - $a['_price'],
        default      => $b['id'] - $a['id']
    };
});
$products = $productsWithPrice;

$cats = $pdo->query("SELECT DISTINCT category FROM products WHERE is_active=1 ORDER BY category")->fetchAll();
$pageTitle = $cat ?: ($diamond ? 'Diamond Collection' : ($collection ? $collection : 'All Jewellery'));

// Hero images per category
$heroImages = [
    'Rings'       => 'https://images.unsplash.com/photo-1605100804763-247f67b3557e?q=80&w=1600',
    'Earrings'    => 'https://images.unsplash.com/photo-1535632787350-4e68ef0ac584?q=80&w=1600',
    'Necklaces'   => 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?q=80&w=1600',
    'Bangles'     => 'https://images.unsplash.com/photo-1611591437281-460bfbe1220a?q=80&w=1600',
    'Bracelets'   => 'https://images.unsplash.com/photo-1573408301185-9519f94816b5?q=80&w=1600',
    'Mangalsutra' => 'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?q=80&w=1600',
    'default'     => 'https://images.unsplash.com/photo-1608042314453-ae338d682c93?q=80&w=1600',
];
$heroBg = $heroImages[$cat] ?? $heroImages['default'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?php echo htmlspecialchars($pageTitle); ?> | Netanis Jewelos</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400&family=Jost:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;}
body{font-family:'Jost',sans-serif;background:#fdfaf7;color:#3a2a1e;}
.serif{font-family:'Cormorant Garamond',serif;}

/* HERO */
.shop-hero{position:relative;height:280px;overflow:hidden;display:flex;align-items:center;}
.shop-hero-bg{position:absolute;inset:0;background-size:cover;background-position:center;transition:transform 8s ease;filter:brightness(0.45);}
.shop-hero:hover .shop-hero-bg{transform:scale(1.04);}
.shop-hero-content{position:relative;z-index:2;max-width:1280px;margin:0 auto;padding:0 32px;width:100%;}
.hero-breadcrumb{font-size:11px;color:rgba(255,255,255,0.5);letter-spacing:0.1em;text-transform:uppercase;margin-bottom:10px;}
.hero-breadcrumb a{color:rgba(255,255,255,0.5);text-decoration:none;transition:color 0.2s;}
.hero-breadcrumb a:hover{color:#d4a82a;}
.hero-title{font-family:'Cormorant Garamond',serif;font-size:48px;font-weight:600;color:#fff;letter-spacing:0.04em;line-height:1.1;}
.hero-count{font-size:12px;color:rgba(255,255,255,0.5);margin-top:8px;letter-spacing:0.08em;}
.hero-gold-line{width:48px;height:2px;background:#b8860b;margin-top:12px;}

/* FILTER SIDEBAR */
.filter-sidebar{background:#fff;border:1px solid #ede8e0;border-radius:6px;padding:24px;position:sticky;top:116px;}
.filter-section{margin-bottom:24px;padding-bottom:20px;border-bottom:1px solid #f5f0ea;}
.filter-section:last-child{border-bottom:none;margin-bottom:0;padding-bottom:0;}
.filter-title{font-size:10px;font-weight:700;letter-spacing:0.2em;text-transform:uppercase;color:#b8860b;margin-bottom:12px;}
.filter-link{display:flex;align-items:center;justify-content:space-between;font-size:12px;color:#5a4030;text-decoration:none;padding:6px 10px;border-radius:3px;transition:all 0.15s;cursor:pointer;}
.filter-link:hover,.filter-link.active{background:#f5efe7;color:#7c2424;font-weight:600;padding-left:14px;}
.filter-link.active::before{content:'›';margin-right:6px;color:#b8860b;}

/* PRICE RANGE */
.price-range{display:flex;flex-direction:column;gap:8px;}
.range-inputs{display:flex;gap:8px;}
.range-input{flex:1;padding:7px 10px;border:1px solid #ede8e0;border-radius:3px;font-size:11px;font-family:'Jost',sans-serif;color:#3a2a1e;outline:none;}
.range-input:focus{border-color:#b8860b;}
.range-btn{background:#b8860b;color:#fff;border:none;padding:8px 16px;font-size:10px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;border-radius:3px;cursor:pointer;font-family:'Jost',sans-serif;transition:background 0.2s;width:100%;}
.range-btn:hover{background:#9a7009;}
.price-slider{width:100%;accent-color:#b8860b;cursor:pointer;}
.quick-price{display:flex;flex-wrap:wrap;gap:6px;margin-top:8px;}
.qp-btn{font-size:10px;border:1px solid #ede8e0;color:#5a4030;padding:4px 10px;border-radius:20px;cursor:pointer;transition:all 0.2s;background:#fdfaf7;}
.qp-btn:hover,.qp-btn.active{border-color:#b8860b;background:#b8860b;color:#fff;}

/* PRODUCT CARDS */
.pcard{background:#fff;border:1px solid #ede8e0;border-radius:6px;overflow:hidden;transition:all 0.4s cubic-bezier(0.25,0.8,0.25,1);}
.pcard:hover{transform:translateY(-8px);box-shadow:0 24px 64px rgba(26,15,10,0.12);border-color:#d4c4b4;}
.pcard-img{position:relative;aspect-ratio:1;overflow:hidden;background:#f5f0ea;}
.pcard-img img{width:100%;height:100%;object-fit:cover;transition:transform 0.7s cubic-bezier(0.25,0.8,0.25,1);}
.pcard:hover .pcard-img img{transform:scale(1.08);}
.pcard-overlay{position:absolute;inset:0;background:linear-gradient(to top,rgba(26,15,10,0.5) 0%,transparent 50%);opacity:0;transition:opacity 0.4s;}
.pcard:hover .pcard-overlay{opacity:1;}
.pcard-actions{position:absolute;bottom:12px;left:0;right:0;display:flex;justify-content:center;gap:8px;opacity:0;transform:translateY(12px);transition:all 0.35s cubic-bezier(0.25,0.8,0.25,1);}
.pcard:hover .pcard-actions{opacity:1;transform:translateY(0);}
.pcard-badge{position:absolute;top:10px;left:10px;font-size:9px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;padding:4px 10px;border-radius:2px;}
.badge-diamond{background:#b8860b;color:#fff;}
.badge-sale{position:absolute;top:10px;right:10px;background:#7c2424;color:#fff;font-size:9px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;padding:4px 10px;border-radius:2px;}
.act-btn{padding:8px 16px;font-size:10px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;border:none;border-radius:3px;cursor:pointer;font-family:'Jost',sans-serif;transition:all 0.2s;white-space:nowrap;}
.act-cart{background:#fff;color:#1a0f0a;}
.act-cart:hover{background:#b8860b;color:#fff;}
.act-view{background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.4);}
.act-view:hover{background:#fff;color:#1a0f0a;}
.act-wish{background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.4);padding:8px 10px;}
.act-wish:hover{background:#ff4d6d;border-color:#ff4d6d;color:#fff;}
.pcard-body{padding:16px 18px;}
.pcard-meta{font-size:10px;color:#b8967a;letter-spacing:0.12em;text-transform:uppercase;font-weight:600;margin-bottom:5px;}
.pcard-name{font-family:'Cormorant Garamond',serif;font-size:17px;font-weight:600;color:#1a0f0a;margin-bottom:4px;line-height:1.25;text-decoration:none;display:block;transition:color 0.2s;}
.pcard-name:hover{color:#7c2424;}
.pcard-desc{font-size:12px;color:#9a8070;font-weight:300;margin-bottom:14px;line-height:1.5;}
.pcard-footer{display:flex;justify-content:space-between;align-items:center;border-top:1px solid #f5f0ea;padding-top:12px;}
.pcard-price{font-family:'Cormorant Garamond',serif;font-size:20px;font-weight:700;color:#7c2424;}
.pcard-incl{font-size:9px;color:#b8967a;margin-top:1px;}

/* SORT BAR */
.sort-bar{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;padding:14px 20px;background:#fff;border:1px solid #ede8e0;border-radius:4px;}
.sort-label{font-size:13px;color:#5a4030;}
.sort-label strong{color:#1a0f0a;}
.sort-sel{border:1px solid #ede8e0;background:#fff;padding:7px 12px;font-size:12px;font-family:'Jost',sans-serif;color:#3a2a1e;border-radius:3px;outline:none;cursor:pointer;}
.sort-sel:focus{border-color:#b8860b;}

/* LIST VIEW toggle */
.view-toggle{display:flex;gap:6px;}
.vt-btn{width:32px;height:32px;border:1px solid #ede8e0;background:#fff;border-radius:3px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#8c7b6b;transition:all 0.2s;font-size:14px;}
.vt-btn.active,.vt-btn:hover{border-color:#b8860b;color:#b8860b;background:#fdf8f0;}

/* EMPTY STATE */
.empty-state{text-align:center;padding:80px 20px;background:#fff;border:1px solid #ede8e0;border-radius:6px;}

/* Active filters chips */
.filter-chips{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:16px;}
.chip{background:#f5efe7;border:1px solid #e8d8c8;color:#5a4030;font-size:11px;padding:5px 12px 5px 10px;border-radius:20px;display:flex;align-items:center;gap:6px;font-weight:500;}
.chip-x{color:#b8860b;cursor:pointer;font-weight:700;font-size:13px;line-height:1;}
.chip-x:hover{color:#7c2424;}
</style>
</head>
<body>
<?php include 'components/header.php'; ?>

<!-- HERO BANNER -->
<div class="shop-hero">
  <div class="shop-hero-bg" style="background-image:url('<?php echo $heroBg; ?>');"></div>
  <div class="shop-hero-content">
    <div class="hero-breadcrumb">
      <a href="index.php">Home</a> <span style="margin:0 8px;opacity:0.4;">›</span>
      <a href="shop.php">Jewellery</a>
      <?php if($cat): ?> <span style="margin:0 8px;opacity:0.4;">›</span> <?php echo htmlspecialchars($cat); ?><?php endif; ?>
    </div>
    <h1 class="hero-title"><?php echo htmlspecialchars($pageTitle); ?></h1>
    <div class="hero-count"><?php echo count($products); ?> Pieces Found</div>
    <div class="hero-gold-line"></div>
  </div>
</div>

<div style="max-width:1280px;margin:0 auto;padding:32px;display:grid;grid-template-columns:240px 1fr;gap:28px;">

  <!-- ====== SIDEBAR ====== -->
  <aside>
    <div class="filter-sidebar">

      <!-- Active Filters -->
      <?php $hasFilters = $cat||$purity||$metal||$diamond||$minPrice>0||($maxPrice<999999&&$maxPrice>0); ?>
      <?php if($hasFilters): ?>
      <div class="filter-section">
        <div class="filter-title">Active Filters</div>
        <div class="filter-chips" style="margin:0;">
          <?php if($cat): ?><div class="chip"><?php echo htmlspecialchars($cat); ?> <a href="shop.php?purity=<?php echo urlencode($purity); ?>&min_price=<?php echo $minPrice; ?>&max_price=<?php echo $maxPrice<999999?$maxPrice:''; ?>" class="chip-x">×</a></div><?php endif; ?>
          <?php if($purity): ?><div class="chip"><?php echo $purity; ?> <a href="shop.php?cat=<?php echo urlencode($cat); ?>&min_price=<?php echo $minPrice; ?>" class="chip-x">×</a></div><?php endif; ?>
          <?php if($diamond): ?><div class="chip">Diamond <a href="shop.php?cat=<?php echo urlencode($cat); ?>" class="chip-x">×</a></div><?php endif; ?>
          <?php if($minPrice>0||($maxPrice<999999&&$maxPrice>0)): ?><div class="chip">₹<?php echo number_format($minPrice); ?>–<?php echo $maxPrice<999999?'₹'.number_format($maxPrice):'Any'; ?> <a href="shop.php?cat=<?php echo urlencode($cat); ?>&purity=<?php echo urlencode($purity); ?>" class="chip-x">×</a></div><?php endif; ?>
        </div>
        <a href="shop.php" style="font-size:11px;color:#7c2424;text-decoration:none;font-weight:600;">Clear All ×</a>
      </div>
      <?php endif; ?>

      <!-- Category -->
      <div class="filter-section">
        <div class="filter-title">Category</div>
        <a href="shop.php<?php echo $purity?'?purity='.urlencode($purity):''; ?>" class="filter-link <?php echo!$cat?'active':''; ?>">All Jewellery</a>
        <?php foreach($cats as $c): ?>
        <a href="shop.php?cat=<?php echo urlencode($c['category']); ?><?php echo $purity?'&purity='.urlencode($purity):''; ?>" class="filter-link <?php echo$cat===$c['category']?'active':''; ?>">
          <?php echo htmlspecialchars($c['category']); ?>
        </a>
        <?php endforeach; ?>
      </div>

      <!-- Gold Purity -->
      <div class="filter-section">
        <div class="filter-title">Gold Purity</div>
        <a href="shop.php?cat=<?php echo urlencode($cat); ?>" class="filter-link <?php echo!$purity?'active':''; ?>">All Purities</a>
        <?php foreach(['22Kt','18Kt','24Kt','14Kt'] as $p): ?>
        <a href="shop.php?cat=<?php echo urlencode($cat); ?>&purity=<?php echo $p; ?>" class="filter-link <?php echo$purity===$p?'active':''; ?>">
          <?php echo $p; ?> Gold
        </a>
        <?php endforeach; ?>
      </div>

      <!-- Price Range -->
      <div class="filter-section">
        <div class="filter-title">Price Range</div>
        <form method="GET" action="shop.php">
          <input type="hidden" name="cat" value="<?php echo htmlspecialchars($cat); ?>">
          <input type="hidden" name="purity" value="<?php echo htmlspecialchars($purity); ?>">
          <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
          <?php if($diamond): ?><input type="hidden" name="has_diamond" value="1"><?php endif; ?>
          <div class="price-range">
            <div class="range-inputs">
              <input type="number" name="min_price" class="range-input" placeholder="Min ₹" value="<?php echo $minPrice>0?$minPrice:''; ?>">
              <input type="number" name="max_price" class="range-input" placeholder="Max ₹" value="<?php echo $maxPrice<999999?$maxPrice:''; ?>">
            </div>
            <div class="quick-price">
              <?php $qpBtns = [['Under ₹10K','0','10000'],['₹10K–₹25K','10000','25000'],['₹25K–₹50K','25000','50000'],['₹50K+','50000','']];
              foreach($qpBtns as $qp):
                $isActive = ($minPrice==intval($qp[1]) && ($qp[2]===''?$maxPrice>=999999:$maxPrice==intval($qp[2]))); ?>
              <button type="button" class="qp-btn <?php echo$isActive?'active':''; ?>"
                onclick="document.querySelector('[name=min_price]').value='<?php echo $qp[1]; ?>';document.querySelector('[name=max_price]').value='<?php echo $qp[2]; ?>';this.closest('form').submit()">
                <?php echo $qp[0]; ?>
              </button>
              <?php endforeach; ?>
            </div>
            <button type="submit" class="range-btn">Apply Filter</button>
          </div>
        </form>
      </div>

      <!-- Special -->
      <div class="filter-section">
        <div class="filter-title">Special</div>
        <a href="shop.php?cat=<?php echo urlencode($cat); ?>&has_diamond=1" class="filter-link <?php echo$diamond==='1'?'active':''; ?>">✦ Diamond Jewellery</a>
        <a href="shop.php?metal=Silver" class="filter-link <?php echo$metal==='Silver'?'active':''; ?>">🪙 Silver Jewellery</a>
        <a href="shop.php?collection=Bridal" class="filter-link">👰 Bridal Collection</a>
        <a href="shop.php?sub=NazarBattu" class="filter-link">🧿 Nazar Battu</a>
      </div>

    </div>
  </aside>

  <!-- ====== PRODUCT GRID ====== -->
  <div>
    <!-- Sort Bar -->
    <div class="sort-bar">
      <div class="sort-label"><strong><?php echo count($products); ?></strong> pieces found<?php echo $cat?' in '.htmlspecialchars($cat):''; ?></div>
      <div style="display:flex;align-items:center;gap:16px;">
        <div class="view-toggle">
          <div class="vt-btn active" id="gridViewBtn" onclick="setView('grid')" title="Grid">⊞</div>
          <div class="vt-btn" id="listViewBtn" onclick="setView('list')" title="List">≡</div>
        </div>
        <select class="sort-sel" onchange="applySort(this.value)">
          <option value="newest" <?php echo$sort==='newest'?'selected':''; ?>>Newest First</option>
          <option value="price_asc" <?php echo$sort==='price_asc'?'selected':''; ?>>Price: Low to High</option>
          <option value="price_desc" <?php echo$sort==='price_desc'?'selected':''; ?>>Price: High to Low</option>
        </select>
      </div>
    </div>

    <?php if(count($products) > 0): ?>
    <div id="productGrid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;">
      <?php foreach($products as $p):
        $price = $p['_price'];
        $img = str_contains($p['primary_image'],'http') ? $p['primary_image'] : 'assets/uploads/'.basename($p['primary_image']);
        $wishActive = false;
        if(isset($_SESSION['user_id'])) {
          $wc = $pdo->prepare("SELECT id FROM wishlist WHERE user_id=? AND product_id=?");
          $wc->execute([$_SESSION['user_id'], $p['id']]);
          $wishActive = (bool)$wc->fetch();
        }
      ?>
      <div class="pcard" data-price="<?php echo $price; ?>">
        <div class="pcard-img">
          <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($p['product_name']); ?>" loading="lazy">
          <?php if($p['has_diamond']): ?><span class="pcard-badge badge-diamond">✦ Diamond</span><?php endif; ?>
          <?php if($p['making_discount_percent']>0 || $p['diamond_discount_percent']>0): ?><span class="badge-sale">SALE</span><?php endif; ?>
          <div class="pcard-overlay"></div>
          <div class="pcard-actions">
            <button class="act-btn act-cart" onclick="addToCartAjax(<?php echo $p['id']; ?>, '<?php echo addslashes($p['product_name']); ?>', this)">🛒 Add to Bag</button>
            <a href="product-details.php?id=<?php echo $p['id']; ?>" class="act-btn act-view">View</a>
            <button class="act-btn act-wish <?php echo $wishActive?'wished':''; ?>" onclick="toggleWish(<?php echo $p['id']; ?>, this)"><?php echo $wishActive?'❤️':'🤍'; ?></button>
          </div>
        </div>
        <div class="pcard-body">
          <div class="pcard-meta"><?php echo $p['purity']; ?> <?php echo $p['metal_type']; ?></div>
          <a href="product-details.php?id=<?php echo $p['id']; ?>" class="pcard-name"><?php echo htmlspecialchars($p['product_name']); ?></a>
          <div class="pcard-desc"><?php echo htmlspecialchars(substr($p['short_description'],0,70)); ?>...</div>
          <div class="pcard-footer">
            <div>
              <div class="pcard-price">₹<?php echo number_format($price); ?></div>
              <div class="pcard-incl">Incl. 3% GST</div>
            </div>
            <button class="act-btn act-cart" style="background:#1a0f0a;color:#fff;padding:9px 16px;"
              onclick="addToCartAjax(<?php echo $p['id']; ?>, '<?php echo addslashes($p['product_name']); ?>', this)">
              + Bag
            </button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="empty-state">
      <div style="font-size:48px;margin-bottom:16px;">💍</div>
      <p class="serif" style="font-size:24px;color:#9a8070;margin-bottom:8px;">No pieces found</p>
      <p style="font-size:13px;color:#b8967a;margin-bottom:20px;">Try adjusting your filters or browse our full collection</p>
      <a href="shop.php" style="background:#7c2424;color:#fff;text-decoration:none;padding:12px 32px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;border-radius:3px;">View All Jewellery</a>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php include 'components/footer.php'; ?>

<script>
function applySort(val) {
  const url = new URL(window.location.href);
  url.searchParams.set('sort', val);
  window.location = url.toString();
}

function setView(type) {
  const grid = document.getElementById('productGrid');
  const gb = document.getElementById('gridViewBtn');
  const lb = document.getElementById('listViewBtn');
  if(type === 'list') {
    grid.style.gridTemplateColumns = '1fr';
    grid.querySelectorAll('.pcard').forEach(c => c.style.display = 'flex');
    grid.querySelectorAll('.pcard-img').forEach(c => { c.style.width = '200px'; c.style.flexShrink = '0'; });
    lb.classList.add('active'); gb.classList.remove('active');
  } else {
    grid.style.gridTemplateColumns = 'repeat(3,1fr)';
    grid.querySelectorAll('.pcard').forEach(c => c.style.display = '');
    grid.querySelectorAll('.pcard-img').forEach(c => { c.style.width = ''; c.style.flexShrink = ''; });
    gb.classList.add('active'); lb.classList.remove('active');
  }
}

function toggleWish(id, btn) {
  fetch(`../api/toggle-wishlist.php?id=${id}`)
    .then(r=>r.json()).then(d=>{
      if(d.status==='added') btn.textContent='❤️';
      else if(d.status==='removed') btn.textContent='🤍';
      else if(d.status==='login_required') window.location='login.php';
    });
}
</script>
</body>
</html>
