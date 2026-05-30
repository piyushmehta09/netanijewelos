<?php
if(session_status()===PHP_SESSION_NONE) session_start();
require_once __DIR__.'/../../config/db.php';
$latestRate=$pdo->query("SELECT * FROM gold_rates ORDER BY id DESC LIMIT 1")->fetch();
$cartCount=!empty($_SESSION['jewellery_cart'])?array_sum($_SESSION['jewellery_cart']):0;
$wishlistCount=0;
if(isset($_SESSION['user_id'])){
  $ws=$pdo->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id=?");
  $ws->execute([$_SESSION['user_id']]);
  $wishlistCount=$ws->fetchColumn();
}
?>
<!-- FONTS + FA -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,600&family=Inter:wght@300;400;500;600;700&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<!-- GOLD TICKER -->
<?php if($latestRate): ?>
<div class="nj-ticker">
  <div class="nj-ticker-track">
    <?php $items = [
      "✦ LIVE GOLD RATE",
      "24Kt &nbsp;<strong>₹".number_format($latestRate['rate_24kt'])."/g</strong>",
      "22Kt &nbsp;<strong>₹".number_format($latestRate['rate_22kt'])."/g</strong>",
      "18Kt &nbsp;<strong>₹".number_format($latestRate['rate_18kt'])."/g</strong>",
      "Silver &nbsp;<strong>₹".number_format($latestRate['silver_rate'])."/g</strong>",
      "Diamond &nbsp;<strong>₹".number_format($latestRate['diamond_rate_per_ct'])."/ct</strong>",
      "✦ Free Pan India Delivery",
      "✦ BIS Hallmark Certified",
      "✦ 7-Day Easy Returns",
    ];
    $full = array_merge($items,$items); // duplicate for seamless loop
    foreach($full as $i) echo "<span class='nj-tick-item'>{$i}</span>"; ?>
  </div>
</div>
<?php endif; ?>

<style>
/* ─── GLOBAL RESET & FONTS ───────────────────────── */
*{box-sizing:border-box;}
:root{
  --gold:#C6A769;--gold2:#D4B483;--gold-dark:#A68B52;
  --dark:#1A1A1A;--mid:#4A4A4A;--muted:#888888;
  --cream:#F9F7F2;--warm:#FCFBF8;--white:#FFFFFF;
  --border:#E7E0D4;--border2:#F0EBE3;
  --shadow:0 4px 24px rgba(0,0,0,0.06);
  --shadow-lg:0 20px 60px rgba(0,0,0,0.10);
}
body,html{font-family:'Inter',sans-serif;background:var(--cream);color:var(--dark);}
.f-serif{font-family:'Cormorant Garamond',serif;}
.f-dm{font-family:'DM Serif Display',serif;}

/* ─── TICKER ─────────────────────────────────────── */
.nj-ticker{background:var(--dark);overflow:hidden;height:34px;display:flex;align-items:center;}
.nj-ticker-track{display:inline-flex;white-space:nowrap;animation:ticker 30s linear infinite;}
.nj-tick-item{font-family:'Inter',sans-serif;font-size:10.5px;font-weight:400;color:rgba(255,255,255,0.55);padding:0 28px;letter-spacing:0.06em;}
.nj-tick-item strong{color:var(--gold2);font-weight:600;}
@keyframes ticker{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}

/* ─── HEADER SHELL ───────────────────────────────── */
.nj-header{background:rgba(255,255,255,0.97);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border-bottom:1px solid var(--border);position:sticky;top:0;z-index:9000;transition:all 0.3s;}
.nj-header.scrolled{box-shadow:0 2px 30px rgba(0,0,0,0.08);}
.nj-topbar{max-width:1440px;margin:0 auto;padding:0 40px;display:flex;align-items:center;gap:24px;height:72px;}

/* LOGO */
.nj-logo{text-decoration:none;display:flex;flex-direction:column;flex-shrink:0;line-height:1;}
.nj-logo-main{font-family:'Cormorant Garamond',serif;font-size:28px;font-weight:700;color:var(--dark);letter-spacing:0.08em;line-height:1;}
.nj-logo-main em{font-style:normal;color:var(--gold);}
.nj-logo-sub{font-family:'Inter',sans-serif;font-size:8px;letter-spacing:0.5em;color:var(--gold);text-transform:uppercase;font-weight:600;margin-top:3px;}

/* SEARCH */
.nj-search{position:relative;flex:1;max-width:420px;margin:0 auto;}
.nj-search-wrap{display:flex;align-items:center;background:var(--warm);border:1.5px solid var(--border);border-radius:40px;padding:0 18px 0 44px;height:42px;transition:all 0.3s;}
.nj-search-wrap:focus-within{border-color:var(--gold);background:#fff;box-shadow:0 0 0 3px rgba(198,167,105,0.12);}
.nj-search-icon{position:absolute;left:17px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:13px;}
.nj-search-input{width:100%;border:none;background:none;outline:none;font-family:'Inter',sans-serif;font-size:13px;color:var(--dark);font-weight:400;}
.nj-search-input::placeholder{color:#BBBBBB;}
.nj-sdrop{position:absolute;top:calc(100%+8px);left:0;right:0;background:#fff;border:1px solid var(--border);border-radius:16px;box-shadow:0 24px 60px rgba(0,0,0,0.12);z-index:500;overflow:hidden;display:none;max-height:360px;overflow-y:auto;}
.nj-sdrop.open{display:block;animation:dropIn 0.2s ease;}
.nj-si{display:flex;align-items:center;gap:14px;padding:13px 18px;text-decoration:none;border-bottom:1px solid var(--border2);transition:background 0.15s;}
.nj-si:last-child{border:none;}
.nj-si:hover{background:var(--warm);}
.nj-si-img{width:48px;height:48px;object-fit:cover;border-radius:10px;border:1px solid var(--border);background:var(--warm);flex-shrink:0;}
.nj-si-name{font-family:'Inter',sans-serif;font-size:13px;font-weight:500;color:var(--dark);}
.nj-si-meta{font-family:'Inter',sans-serif;font-size:11px;color:var(--muted);margin-top:2px;}

/* ICONS */
.nj-icons{display:flex;align-items:center;gap:4px;margin-left:auto;flex-shrink:0;}
.nj-ibtn{position:relative;width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;text-decoration:none;color:var(--dark);background:none;border:none;cursor:pointer;transition:all 0.2s;font-size:16px;}
.nj-ibtn:hover{background:var(--warm);color:var(--gold);}
.nj-badge{position:absolute;top:2px;right:2px;min-width:16px;height:16px;background:#C9370D;color:#fff;font-size:9px;font-weight:700;border-radius:8px;display:flex;align-items:center;justify-content:center;border:2px solid #fff;padding:0 3px;font-family:'Inter',sans-serif;}
.nj-cart-pop{animation:cartPop 0.4s ease;}
@keyframes cartPop{0%,100%{transform:scale(1)}40%{transform:scale(1.3)}70%{transform:scale(0.9)}}

/* AUTH */
.nj-auth-btn{font-family:'Inter',sans-serif;font-size:12px;font-weight:600;letter-spacing:0.02em;text-decoration:none;padding:8px 20px;border-radius:6px;transition:all 0.2s;white-space:nowrap;border:1.5px solid;}
.nj-auth-login{color:var(--dark);border-color:var(--border);}
.nj-auth-login:hover{border-color:var(--gold);color:var(--gold);}
.nj-auth-reg{color:#fff;background:var(--dark);border-color:var(--dark);}
.nj-auth-reg:hover{background:var(--gold);border-color:var(--gold);}

/* USER DROPDOWN */
.nj-user{position:relative;}
.nj-user-trigger{display:flex;align-items:center;gap:8px;padding:6px 14px 6px 6px;border-radius:24px;border:1.5px solid var(--border);background:#fff;cursor:pointer;transition:all 0.2s;}
.nj-user-trigger:hover{border-color:var(--gold);}
.nj-avatar{width:30px;height:30px;background:linear-gradient(135deg,var(--gold),var(--gold2));border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:12px;font-weight:700;font-family:'Inter',sans-serif;}
.nj-uname{font-family:'Inter',sans-serif;font-size:12px;font-weight:600;color:var(--dark);}
.nj-udrop{position:absolute;right:0;top:calc(100%+8px);background:#fff;border:1px solid var(--border);border-radius:16px;box-shadow:var(--shadow-lg);min-width:210px;z-index:500;overflow:hidden;display:none;}
.nj-user:hover .nj-udrop{display:block;animation:dropIn 0.2s ease;}
.nj-udi{display:flex;align-items:center;gap:12px;padding:13px 18px;font-family:'Inter',sans-serif;font-size:13px;color:var(--dark);text-decoration:none;transition:background 0.15s;border-bottom:1px solid var(--border2);}
.nj-udi:last-child{border:none;color:#C0392B;}
.nj-udi:hover{background:var(--warm);}
.nj-udi i{width:16px;font-size:12px;text-align:center;color:var(--gold);}
.nj-udi:last-child i{color:#C0392B;}

/* ─── NAV BAR ─────────────────────────────────────── */
.nj-nav{background:#fff;border-top:1px solid var(--border2);}
.nj-nav-inner{max-width:1440px;margin:0 auto;padding:0 40px;display:flex;align-items:stretch;position:relative;}
.nj-nav-item{position:static;}/* KEY FIX: static so mega uses nav-inner as ref */
.nj-nav-link{font-family:'Inter',sans-serif;font-size:11.5px;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;color:var(--mid);text-decoration:none;padding:15px 18px;display:flex;align-items:center;gap:5px;transition:color 0.2s;border-bottom:2px solid transparent;white-space:nowrap;cursor:pointer;}
.nj-nav-link:hover,.nj-nav-item:hover>.nj-nav-link{color:var(--gold);border-bottom-color:var(--gold);}
.nj-nav-link .fa-chevron-down{font-size:8px;transition:transform 0.3s;}
.nj-nav-item:hover>.nj-nav-link .fa-chevron-down{transform:rotate(180deg);}
.nj-nav-link.special-link{color:var(--gold);}

/* ─── MEGA MENU — FULL WIDTH FIXED BELOW NAV ─────── */
/* nav-inner is relative → mega is absolute inside it */
.nj-nav-inner{position:relative;}

.nj-mega{
  display:none;
  position:fixed;
  left:0;
  right:0;
  max-height:340px;
  overflow:hidden;
   background:#fff !important;
  isolation:isolate;
}
.nj-nav-item:hover>.nj-mega{
  display:flex;
  animation:dropIn 0.25s cubic-bezier(0.16,1,0.3,1);
}
@keyframes dropIn{from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:translateY(0)}}

.nj-mega-inner{display:flex;width:100%;overflow:hidden;}
.nj-mega-inner{display:flex;width:100%;overflow:hidden;}

/* Inline second head ka margin fix */
.nj-mega-col .nj-mega-head + .nj-mega-head,
.nj-mega-col [style*="margin-top:10px"]{margin-top:12px !important;}
.nj-mega-col{padding:12px 20px;border-right:1px solid var(--border2);flex:1;min-width:0;}
.nj-mega-col:last-child{border-right:none;}
.nj-mega-head{font-family:'Inter',sans-serif;font-size:10px;font-weight:700;letter-spacing:0.22em;text-transform:uppercase;color:var(--gold);margin-bottom:6px;margin-top:0;padding-bottom:5px;border-bottom:1px solid var(--border2);}
.nj-mega-link{display:flex;align-items:center;gap:8px;font-family:'Inter',sans-serif;font-size:12.5px;font-weight:400;color:var(--mid);text-decoration:none;padding:3px 0;}
.nj-mega-link::before{content:'';position:absolute;left:-18px;width:6px;height:6px;background:var(--gold);border-radius:50%;transition:all 0.25s;opacity:0;}
.nj-mega-link:hover{color:var(--dark);padding-left:16px;}
.nj-mega-link:hover::before{left:0;opacity:1;}
.nj-mega-link i{width:16px;font-size:11px;color:#CCBBAA;transition:color 0.2s;flex-shrink:0;}
.nj-mega-link:hover i{color:var(--gold);}

/* MEGA PROMO PANEL */
.nj-mega-promo{background:linear-gradient(145deg,#1A1A1A,#2A2A2A);padding:16px 20px;}
.nj-mega-promo::after{content:'';position:absolute;bottom:-30px;right:-30px;width:120px;height:120px;border:1px solid rgba(198,167,105,0.2);border-radius:50%;}
.nj-mega-promo::before{content:'';position:absolute;bottom:-60px;right:-60px;width:180px;height:180px;border:1px solid rgba(198,167,105,0.08);border-radius:50%;}
.nj-promo-tag{font-family:'Inter',sans-serif;font-size:9px;letter-spacing:0.25em;text-transform:uppercase;color:var(--gold);font-weight:700;margin-bottom:10px;}
.nj-promo-title{font-family:'Cormorant Garamond',serif;font-size:22px;color:#fff;font-weight:600;line-height:1.3;margin-bottom:10px;}
.nj-promo-desc{font-family:'Inter',sans-serif;font-size:12px;color:rgba(255,255,255,0.45);line-height:1.7;margin-bottom:20px;font-weight:300;}
.nj-promo-btn{display:inline-block;font-family:'Inter',sans-serif;font-size:10px;font-weight:700;letter-spacing:0.15em;text-transform:uppercase;color:var(--gold);border:1.5px solid var(--gold);padding:9px 18px;text-decoration:none;transition:all 0.25s;align-self:flex-start;}
.nj-promo-btn:hover{background:var(--gold);color:#fff;}

/* ─── CART TOAST ─────────────────────────────────── */
.nj-toast{position:fixed;top:90px;right:20px;background:#fff;border:1px solid var(--border);border-left:3px solid var(--gold);border-radius:12px;box-shadow:0 16px 48px rgba(0,0,0,0.12);padding:14px 16px;z-index:99999;width:300px;display:none;align-items:center;gap:14px;}
.nj-toast.show{display:flex;animation:toastIn 0.35s cubic-bezier(0.16,1,0.3,1);}
.nj-toast.hide{animation:toastOut 0.25s ease forwards;}
@keyframes toastIn{from{opacity:0;transform:translateX(120%)}to{opacity:1;transform:translateX(0)}}
@keyframes toastOut{from{opacity:1;transform:translateX(0)}to{opacity:0;transform:translateX(120%)}}
.nj-ti{width:38px;height:38px;background:var(--warm);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;color:var(--gold);font-size:16px;}
.nj-tt{font-family:'Inter',sans-serif;font-size:13px;font-weight:600;color:var(--dark);}
.nj-ts{font-family:'Inter',sans-serif;font-size:11px;color:var(--muted);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:160px;}
.nj-tv{margin-left:auto;font-family:'Inter',sans-serif;font-size:10px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:var(--gold);text-decoration:none;padding:6px 12px;border:1.5px solid var(--gold);border-radius:5px;transition:all 0.2s;white-space:nowrap;}
.nj-tv:hover{background:var(--gold);color:#fff;}
</style>

<script>
(function(){
  function setMegaTop(){
    var nav = document.querySelector('.nj-nav');
    if(!nav) return;
    var top = nav.getBoundingClientRect().bottom;
    document.querySelectorAll('.nj-mega').forEach(function(m){
      m.style.top = top + 'px';
    });
  }
  setMegaTop();
  window.addEventListener('scroll', setMegaTop, {passive:true});
  window.addEventListener('resize', setMegaTop, {passive:true});
})();
</script>

<!-- ══ HEADER ══════════════════════════════════════ -->
<header class="nj-header" id="njHeader">
  <div class="nj-topbar">
    <a href="index.php" class="nj-logo">
  <img src="assets/uploads/logo.png" alt="Netanis Jewelos" style="height:52px;width:auto;object-fit:contain;">
</a>

    <div class="nj-search">
      <i class="fa-solid fa-magnifying-glass nj-search-icon"></i>
      <div class="nj-search-wrap">
        <input type="text" id="njSearch" class="nj-search-input" placeholder="Search rings, necklaces, diamonds..." autocomplete="off">
      </div>
      <div class="nj-sdrop" id="njSdrop"></div>
    </div>

    <div class="nj-icons">
      <?php if(isset($_SESSION['user_id'])): ?>
        <div class="nj-user">
          <div class="nj-user-trigger">
            <div class="nj-avatar"><?php echo strtoupper(substr($_SESSION['user_name'],0,1)); ?></div>
            <span class="nj-uname"><?php echo htmlspecialchars(explode(' ',$_SESSION['user_name'])[0]); ?></span>
            <i class="fa-solid fa-chevron-down" style="font-size:8px;color:var(--muted);margin-left:2px;"></i>
          </div>
          <div class="nj-udrop">
            <a href="dashboard.php" class="nj-udi"><i class="fa-solid fa-box-open"></i>My Orders</a>
            <a href="wishlist.php" class="nj-udi"><i class="fa-regular fa-heart"></i>Wishlist<?php if($wishlistCount>0): ?> <span style="color:var(--gold);margin-left:auto;font-size:11px;font-weight:700;">(<?php echo $wishlistCount; ?>)</span><?php endif; ?></a>
            <?php if(isset($_SESSION['user_role'])&&in_array($_SESSION['user_role'],['admin','staff'])): ?>
            <a href="../admin/index.php" class="nj-udi"><i class="fa-solid fa-gear"></i>Admin Panel</a>
            <?php endif; ?>
            <a href="../api/logout.php" class="nj-udi"><i class="fa-solid fa-right-from-bracket"></i>Logout</a>
          </div>
        </div>
        <a href="wishlist.php" class="nj-ibtn" title="Wishlist">
          <i class="fa-<?php echo $wishlistCount>0?'solid':'regular'; ?> fa-heart"></i>
          <?php if($wishlistCount>0): ?><span class="nj-badge"><?php echo $wishlistCount; ?></span><?php endif; ?>
        </a>
      <?php else: ?>
        <a href="login.php" class="nj-auth-btn nj-auth-login">Login</a>
        <a href="signup.php" class="nj-auth-btn nj-auth-reg">Register</a>
      <?php endif; ?>
      <a href="cart.php" class="nj-ibtn" id="njCartIcon" title="Cart">
        <i class="fa-solid fa-bag-shopping"></i>
        <span class="nj-badge" id="njCartBadge" <?php if($cartCount==0):?>style="display:none"<?php endif;?>><?php echo $cartCount; ?></span>
      </a>
    </div>
  </div>

  <!-- NAV -->
  <nav class="nj-nav">
    <div class="nj-nav-inner">
      <a href="index.php" class="nj-nav-link">Home</a>

      <!-- RINGS -->
      <div class="nj-nav-item">
        <a href="shop.php?cat=Rings" class="nj-nav-link">Rings <i class="fa-solid fa-chevron-down"></i></a>
        <div class="nj-mega"><div class="nj-mega-inner">
          <div class="nj-mega-col">
            <div class="nj-mega-head">By Style</div>
            <a href="shop.php?cat=Rings&sub=Engagement" class="nj-mega-link"><i class="fa-solid fa-ring"></i>Engagement Rings</a>
            <a href="shop.php?cat=Rings&has_diamond=1" class="nj-mega-link"><i class="fa-solid fa-gem"></i>Diamond Rings</a>
            <a href="shop.php?cat=Rings&purity=22Kt" class="nj-mega-link"><i class="fa-solid fa-coins"></i>Gold Rings</a>
            <a href="shop.php?cat=Rings&sub=Couple" class="nj-mega-link"><i class="fa-solid fa-heart"></i>Couple Rings</a>
            <a href="shop.php?cat=Rings&sub=Solitaire" class="nj-mega-link"><i class="fa-regular fa-star"></i>Solitaire Rings</a>
            <a href="shop.php?cat=Rings&sub=Adjustable" class="nj-mega-link"><i class="fa-solid fa-sliders"></i>Adjustable Rings</a>
          </div>
          <div class="nj-mega-col">
            <div class="nj-mega-head">By Metal</div>
            <a href="shop.php?cat=Rings&purity=22Kt" class="nj-mega-link"><i class="fa-solid fa-circle-dot"></i>22Kt Gold Rings</a>
            <a href="shop.php?cat=Rings&purity=18Kt" class="nj-mega-link"><i class="fa-solid fa-circle-dot"></i>18Kt Gold Rings</a>
            <a href="shop.php?cat=Rings&purity=24Kt" class="nj-mega-link"><i class="fa-solid fa-circle-dot"></i>24Kt Pure Gold</a>
            <a href="shop.php?cat=Rings&metal=Silver" class="nj-mega-link"><i class="fa-solid fa-circle-half-stroke"></i>Silver Rings</a>
            <div class="nj-mega-head" style="margin-top:20px;">For Occasion</div>
            <a href="shop.php?cat=Rings&sub=Wedding" class="nj-mega-link"><i class="fa-solid fa-church"></i>Wedding Rings</a>
            <a href="shop.php?cat=Rings&sub=DailyWear" class="nj-mega-link"><i class="fa-regular fa-sun"></i>Daily Wear</a>
            <a href="shop.php?cat=Rings&sub=Men" class="nj-mega-link"><i class="fa-solid fa-mars"></i>Men's Rings</a>
          </div>
          <div class="nj-mega-promo">
            <div>
              <div class="nj-promo-tag"><i class="fa-solid fa-fire-flame-curved fa-xs"></i>&nbsp;New Arrival</div>
              <div class="nj-promo-title">Solitaire Dreams</div>
              <div class="nj-promo-desc">IGI certified diamonds in 18Kt gold — brilliance that lasts forever.</div>
            </div>
            <a href="shop.php?cat=Rings&has_diamond=1" class="nj-promo-btn">Explore Rings &rarr;</a>
          </div>
        </div></div>
      </div>

      <!-- EARRINGS -->
      <div class="nj-nav-item">
        <a href="shop.php?cat=Earrings" class="nj-nav-link">Earrings <i class="fa-solid fa-chevron-down"></i></a>
        <div class="nj-mega"><div class="nj-mega-inner">
          <div class="nj-mega-col">
            <div class="nj-mega-head">By Style</div>
            <a href="shop.php?cat=Earrings&sub=Studs" class="nj-mega-link"><i class="fa-regular fa-circle-dot"></i>Studs</a>
            <a href="shop.php?cat=Earrings&sub=Hoops" class="nj-mega-link"><i class="fa-solid fa-circle-notch"></i>Hoops</a>
            <a href="shop.php?cat=Earrings&sub=Jhumkas" class="nj-mega-link"><i class="fa-solid fa-droplet"></i>Jhumkas</a>
            <a href="shop.php?cat=Earrings&sub=Chandbali" class="nj-mega-link"><i class="fa-regular fa-moon"></i>Chandbali</a>
            <a href="shop.php?cat=Earrings&sub=Drops" class="nj-mega-link"><i class="fa-solid fa-water"></i>Drop Earrings</a>
          </div>
          <div class="nj-mega-col">
            <div class="nj-mega-head">By Material</div>
            <a href="shop.php?cat=Earrings&has_diamond=1" class="nj-mega-link"><i class="fa-solid fa-gem"></i>Diamond Earrings</a>
            <a href="shop.php?cat=Earrings&purity=22Kt" class="nj-mega-link"><i class="fa-solid fa-coins"></i>22Kt Gold</a>
            <a href="shop.php?cat=Earrings&metal=Silver" class="nj-mega-link"><i class="fa-solid fa-circle-half-stroke"></i>Silver</a>
            <div class="nj-mega-head" style="margin-top:20px;">Special</div>
            <a href="shop.php?cat=NosePins" class="nj-mega-link"><i class="fa-solid fa-circle-dot"></i>Nose Pins</a>
            <a href="shop.php?cat=Earrings&sub=Nath" class="nj-mega-link"><i class="fa-solid fa-circle"></i>Nath</a>
            <a href="shop.php?cat=Earrings&sub=Kids" class="nj-mega-link"><i class="fa-solid fa-child"></i>Kids Earrings</a>
          </div>
          <div class="nj-mega-promo">
            <div>
              <div class="nj-promo-tag"><i class="fa-solid fa-award fa-xs"></i>&nbsp;Bestseller</div>
              <div class="nj-promo-title">Eternal Solitaire Studs</div>
              <div class="nj-promo-desc">Classic IGI-certified diamond studs — timeless elegance.</div>
            </div>
            <a href="shop.php?cat=Earrings&has_diamond=1" class="nj-promo-btn">Shop Earrings &rarr;</a>
          </div>
        </div></div>
      </div>

      <!-- NECKLACES -->
      <div class="nj-nav-item">
        <a href="shop.php?cat=Necklaces" class="nj-nav-link">Necklaces <i class="fa-solid fa-chevron-down"></i></a>
        <div class="nj-mega"><div class="nj-mega-inner">
          <div class="nj-mega-col">
            <div class="nj-mega-head">Necklaces</div>
            <a href="shop.php?cat=Necklaces&sub=Choker" class="nj-mega-link"><i class="fa-solid fa-link"></i>Choker</a>
            <a href="shop.php?cat=Necklaces&has_diamond=1" class="nj-mega-link"><i class="fa-solid fa-gem"></i>Diamond Necklace</a>
            <a href="shop.php?cat=Necklaces&sub=Bridal" class="nj-mega-link"><i class="fa-solid fa-crown"></i>Bridal Necklace</a>
            <a href="shop.php?cat=Necklaces&sub=Layered" class="nj-mega-link"><i class="fa-solid fa-layer-group"></i>Layered Necklace</a>
            <a href="shop.php?sub=Mangalsutra" class="nj-mega-link"><i class="fa-solid fa-heart"></i>Mangalsutra</a>
          </div>
          <div class="nj-mega-col">
            <div class="nj-mega-head">Chains</div>
            <a href="shop.php?cat=Chains&metal=Gold" class="nj-mega-link"><i class="fa-solid fa-link"></i>Gold Chains</a>
            <a href="shop.php?cat=Chains&sub=DailyWear" class="nj-mega-link"><i class="fa-regular fa-sun"></i>Daily Wear Chains</a>
            <a href="shop.php?cat=Chains&sub=Men" class="nj-mega-link"><i class="fa-solid fa-mars"></i>Men Chains</a>
            <div class="nj-mega-head" style="margin-top:20px;">Pendants</div>
            <a href="shop.php?cat=Pendants&sub=Alphabet" class="nj-mega-link"><i class="fa-solid fa-a"></i>Alphabet Pendant</a>
            <a href="shop.php?cat=Pendants&sub=Religious" class="nj-mega-link"><i class="fa-solid fa-om"></i>Religious Pendant</a>
            <a href="shop.php?cat=Pendants&sub=Couple" class="nj-mega-link"><i class="fa-solid fa-heart"></i>Couple Pendant</a>
          </div>
          <div class="nj-mega-promo">
            <div>
              <div class="nj-promo-tag"><i class="fa-solid fa-star fa-xs"></i>&nbsp;Heritage</div>
              <div class="nj-promo-title">Royal Heritage</div>
              <div class="nj-promo-desc">Traditional 22Kt necklaces crafted for the modern queen.</div>
            </div>
            <a href="shop.php?cat=Necklaces" class="nj-promo-btn">Discover &rarr;</a>
          </div>
        </div></div>
      </div>

      <!-- BANGLES -->
      <div class="nj-nav-item">
        <a href="shop.php?cat=Bangles" class="nj-nav-link">Bangles <i class="fa-solid fa-chevron-down"></i></a>
        <div class="nj-mega"><div class="nj-mega-inner">
          <div class="nj-mega-col">
            <div class="nj-mega-head">Bangles</div>
            <a href="shop.php?cat=Bangles&purity=22Kt" class="nj-mega-link"><i class="fa-solid fa-circle-notch"></i>Gold Bangles</a>
            <a href="shop.php?cat=Bangles&has_diamond=1" class="nj-mega-link"><i class="fa-solid fa-gem"></i>Diamond Bangles</a>
            <a href="shop.php?cat=Bangles&sub=Bridal" class="nj-mega-link"><i class="fa-solid fa-crown"></i>Bridal Bangles</a>
            <a href="shop.php?cat=Bangles&sub=DailyWear" class="nj-mega-link"><i class="fa-regular fa-sun"></i>Daily Wear</a>
          </div>
          <div class="nj-mega-col">
            <div class="nj-mega-head">Bracelets</div>
            <a href="shop.php?cat=Bracelets&sub=Charm" class="nj-mega-link"><i class="fa-solid fa-star"></i>Charm Bracelets</a>
            <a href="shop.php?cat=Bracelets&sub=Kada" class="nj-mega-link"><i class="fa-solid fa-circle-notch"></i>Kada</a>
            <a href="shop.php?cat=Bracelets&sub=Tennis" class="nj-mega-link"><i class="fa-solid fa-gem"></i>Tennis Bracelet</a>
            <div class="nj-mega-head" style="margin-top:20px;">Anklets</div>
            <a href="shop.php?cat=Anklets&metal=Silver" class="nj-mega-link"><i class="fa-solid fa-circle-half-stroke"></i>Silver Anklets</a>
            <a href="shop.php?cat=Anklets&sub=Designer" class="nj-mega-link"><i class="fa-solid fa-wand-sparkles"></i>Designer Anklets</a>
          </div>
          <div class="nj-mega-promo">
            <div>
              <div class="nj-promo-tag"><i class="fa-solid fa-fire fa-xs"></i>&nbsp;Trending</div>
              <div class="nj-promo-title">Stacked & Styled</div>
              <div class="nj-promo-desc">Mix bangles, kadas & bracelets — your signature stack.</div>
            </div>
            <a href="shop.php?cat=Bangles" class="nj-promo-btn">Shop Now &rarr;</a>
          </div>
        </div></div>
      </div>

      <!-- BRIDAL -->
      <div class="nj-nav-item">
        <a href="shop.php?collection=Bridal" class="nj-nav-link special-link"><i class="fa-solid fa-crown fa-xs"></i>&nbsp;Bridal <i class="fa-solid fa-chevron-down"></i></a>
        <div class="nj-mega"><div class="nj-mega-inner">
          <div class="nj-mega-col">
            <div class="nj-mega-head">Bridal Sets</div>
            <a href="shop.php?collection=Bridal&sub=BridalSet" class="nj-mega-link"><i class="fa-solid fa-gifts"></i>Complete Bridal Sets</a>
            <a href="shop.php?collection=Bridal&sub=Wedding" class="nj-mega-link"><i class="fa-solid fa-church"></i>Wedding Jewellery</a>
            <a href="shop.php?collection=Bridal&sub=Reception" class="nj-mega-link"><i class="fa-solid fa-star"></i>Reception Collection</a>
            <a href="shop.php?collection=Bridal&sub=Traditional" class="nj-mega-link"><i class="fa-solid fa-om"></i>Traditional Sets</a>
            <a href="shop.php?collection=Bridal&sub=Rajwadi" class="nj-mega-link"><i class="fa-solid fa-crown"></i>Royal Rajwadi</a>
          </div>
          <div class="nj-mega-col">
            <div class="nj-mega-head">Bridal Pieces</div>
            <a href="shop.php?cat=Necklaces&sub=Bridal" class="nj-mega-link"><i class="fa-solid fa-link"></i>Bridal Necklace</a>
            <a href="shop.php?cat=Bangles&sub=Bridal" class="nj-mega-link"><i class="fa-solid fa-circle-notch"></i>Bridal Bangles</a>
            <a href="shop.php?cat=Earrings&sub=Bridal" class="nj-mega-link"><i class="fa-solid fa-droplet"></i>Bridal Earrings</a>
            <a href="shop.php?cat=Rings&sub=Wedding" class="nj-mega-link"><i class="fa-solid fa-ring"></i>Wedding Rings</a>
            <a href="shop.php?sub=Mangalsutra" class="nj-mega-link"><i class="fa-solid fa-heart"></i>Mangalsutra</a>
          </div>
          <div class="nj-mega-promo" style="background:linear-gradient(145deg,#2A0A0A,#3D1515);">
            <div>
              <div class="nj-promo-tag" style="color:#FFD700;"><i class="fa-solid fa-crown fa-xs"></i>&nbsp;Royal Collection</div>
              <div class="nj-promo-title">Rajwadi Bridal</div>
              <div class="nj-promo-desc" style="color:rgba(255,215,0,0.4);">Handcrafted 22Kt gold — for the royal bride of Jodhpur.</div>
            </div>
            <a href="shop.php?collection=Bridal" class="nj-promo-btn" style="border-color:#FFD700;color:#FFD700;">View Bridal &rarr;</a>
          </div>
        </div></div>
      </div>

      <!-- DIAMOND -->
      <div class="nj-nav-item">
        <a href="shop.php?has_diamond=1" class="nj-nav-link"><i class="fa-solid fa-gem fa-xs" style="color:var(--gold);"></i>&nbsp;Diamond <i class="fa-solid fa-chevron-down"></i></a>
        <div class="nj-mega"><div class="nj-mega-inner">
          <div class="nj-mega-col">
            <div class="nj-mega-head">By Category</div>
            <a href="shop.php?cat=Rings&has_diamond=1" class="nj-mega-link"><i class="fa-solid fa-ring"></i>Diamond Rings</a>
            <a href="shop.php?cat=Earrings&has_diamond=1" class="nj-mega-link"><i class="fa-solid fa-droplet"></i>Diamond Earrings</a>
            <a href="shop.php?cat=Necklaces&has_diamond=1" class="nj-mega-link"><i class="fa-solid fa-link"></i>Diamond Necklaces</a>
            <a href="shop.php?cat=Bangles&has_diamond=1" class="nj-mega-link"><i class="fa-solid fa-circle-notch"></i>Diamond Bangles</a>
          </div>
          <div class="nj-mega-col">
            <div class="nj-mega-head">Collections</div>
            <a href="shop.php?has_diamond=1&sub=Solitaire" class="nj-mega-link"><i class="fa-regular fa-star"></i>Solitaire Collection</a>
            <a href="shop.php?has_diamond=1&sub=LabGrown" class="nj-mega-link"><i class="fa-solid fa-flask"></i>Lab Grown Diamonds</a>
            <a href="shop.php?purity=18Kt&has_diamond=1" class="nj-mega-link"><i class="fa-solid fa-coins"></i>18K Gold Diamond</a>
            <a href="shop.php?has_diamond=1&collection=Bridal" class="nj-mega-link"><i class="fa-solid fa-crown"></i>Bridal Diamond</a>
          </div>
          <div class="nj-mega-promo">
            <div>
              <div class="nj-promo-tag"><i class="fa-solid fa-certificate fa-xs"></i>&nbsp;IGI Certified</div>
              <div class="nj-promo-title">Eternal Diamond Collection</div>
              <div class="nj-promo-desc">Every diamond verified for brilliance, clarity & cut.</div>
            </div>
            <a href="shop.php?has_diamond=1" class="nj-promo-btn">Explore &rarr;</a>
          </div>
        </div></div>
      </div>

      <!-- MORE -->
      <div class="nj-nav-item">
        <a href="shop.php" class="nj-nav-link">More <i class="fa-solid fa-chevron-down"></i></a>
        <div class="nj-mega"><div class="nj-mega-inner">
          <div class="nj-mega-col">
            <div class="nj-mega-head">Gold</div>
            <a href="shop.php?purity=22Kt" class="nj-mega-link"><i class="fa-solid fa-circle-dot"></i>22Kt Hallmark Gold</a>
            <a href="shop.php?purity=18Kt" class="nj-mega-link"><i class="fa-solid fa-circle-dot"></i>18Kt Gold</a>
            <a href="shop.php?purity=24Kt" class="nj-mega-link"><i class="fa-solid fa-circle-dot"></i>24Kt Pure Gold</a>
          </div>
          <div class="nj-mega-col">
            <div class="nj-mega-head">Silver</div>
            <a href="shop.php?metal=Silver&cat=Rings" class="nj-mega-link"><i class="fa-solid fa-ring"></i>Silver Rings</a>
            <a href="shop.php?metal=Silver&cat=Chains" class="nj-mega-link"><i class="fa-solid fa-link"></i>Silver Chains</a>
            <a href="shop.php?metal=Silver&sub=Oxidised" class="nj-mega-link"><i class="fa-solid fa-wand-sparkles"></i>Oxidised Jewellery</a>
            <a href="shop.php?cat=Anklets&metal=Silver" class="nj-mega-link"><i class="fa-solid fa-circle-notch"></i>Silver Anklets</a>
          </div>
          <div class="nj-mega-col">
            <div class="nj-mega-head">Men & Kids</div>
            <a href="shop.php?cat=Chains&sub=Men" class="nj-mega-link"><i class="fa-solid fa-link"></i>Men Chains</a>
            <a href="shop.php?cat=Bracelets&sub=Kada" class="nj-mega-link"><i class="fa-solid fa-circle-notch"></i>Kada</a>
            <a href="shop.php?cat=Rings&sub=Men" class="nj-mega-link"><i class="fa-solid fa-ring"></i>Men Rings</a>
            <a href="shop.php?cat=Kids" class="nj-mega-link"><i class="fa-solid fa-child"></i>Kids Jewellery</a>
            <a href="shop.php?sub=NazarBattu" class="nj-mega-link"><i class="fa-solid fa-eye"></i>Nazar Battu</a>
          </div>
          <div class="nj-mega-col">
            <div class="nj-mega-head">Special</div>
            <a href="shop.php?cat=Pendants&sub=Religious" class="nj-mega-link"><i class="fa-solid fa-om"></i>Religious Pendants</a>
            <a href="shop.php?cat=Pendants&sub=Alphabet" class="nj-mega-link"><i class="fa-solid fa-a"></i>Alphabet Pendants</a>
            <a href="shop.php?cat=Pendants&sub=Couple" class="nj-mega-link"><i class="fa-solid fa-heart"></i>Couple Pendants</a>
          </div>
        </div></div>
      </div>

      <a href="about-us.php" class="nj-nav-link">Our Story</a>
    </div>
  </nav>
</header>

<!-- CART TOAST -->
<div class="nj-toast" id="njToast">
  <div class="nj-ti"><i class="fa-solid fa-bag-shopping"></i></div>
  <div style="min-width:0;">
    <div class="nj-tt">Added to Bag!</div>
    <div class="nj-ts" id="njToastName"></div>
  </div>
  <a href="cart.php" class="nj-tv">View Bag</a>
</div>

<script>
/* SCROLL HEADER */
window.addEventListener('scroll',()=>{
  document.getElementById('njHeader')?.classList.toggle('scrolled',window.scrollY>20);
});

/* SEARCH */
const _si=document.getElementById('njSearch'),_sd=document.getElementById('njSdrop');
let _st;
if(_si){
  _si.addEventListener('input',function(){
    clearTimeout(_st);const q=this.value.trim();
    if(q.length<2){_sd.classList.remove('open');return;}
    _st=setTimeout(()=>{
      fetch(`../api/search-suggestions.php?q=${encodeURIComponent(q)}`)
        .then(r=>r.json()).then(d=>{
          if(!d.length){_sd.classList.remove('open');return;}
          _sd.innerHTML=d.map(i=>`
            <a href="product-details.php?id=${i.id}" class="nj-si">
              <img src="${i.image}" class="nj-si-img">
              <div>
                <div class="nj-si-name">${i.name}</div>
                <div class="nj-si-meta">${i.purity} · ${i.category} · ₹${Number(i.price||0).toLocaleString('en-IN')}</div>
              </div>
            </a>`).join('');
          _sd.classList.add('open');
        }).catch(()=>{});
    },280);
  });
  document.addEventListener('click',e=>{if(!_si.closest('.nj-search').contains(e.target))_sd.classList.remove('open');});
}

/* AJAX ADD TO CART */
function addToCartAjax(id,name,btn){
  const orig=btn?btn.innerHTML:'';
  if(btn){btn.disabled=true;btn.innerHTML='<i class="fa-solid fa-spinner fa-spin fa-xs"></i> Adding...';}
  fetch(`../api/add-to-cart.php?id=${id}`,{headers:{'X-Requested-With':'XMLHttpRequest'}})
    .then(r=>r.json()).then(d=>{
      if(d.status==='added'){
        const b=document.getElementById('njCartBadge');
        if(b){b.textContent=d.cart_count;b.style.display='flex';}
        const ci=document.getElementById('njCartIcon');
        if(ci){ci.classList.add('nj-cart-pop');setTimeout(()=>ci.classList.remove('nj-cart-pop'),500);}
        _showToast(name);
        if(btn){btn.disabled=false;btn.innerHTML='<i class="fa-solid fa-check fa-xs"></i> Added';setTimeout(()=>btn.innerHTML=orig,2000);}
      }
    }).catch(()=>{if(btn){btn.disabled=false;btn.innerHTML=orig;}window.location='../api/add-to-cart.php?id='+id;});
}
function _showToast(n){
  const t=document.getElementById('njToast'),el=document.getElementById('njToastName');
  if(!t)return;el.textContent=n;
  t.classList.remove('hide');t.classList.add('show');
  clearTimeout(window._tt);
  window._tt=setTimeout(()=>{t.classList.add('hide');setTimeout(()=>t.classList.remove('show','hide'),300);},3800);
}
</script>
