<?php
session_start();
require_once '../config/db.php';
$latestRate = $pdo->query("SELECT * FROM gold_rates ORDER BY id DESC LIMIT 1")->fetch();
$featured   = $pdo->query("SELECT * FROM products WHERE is_active=1 ORDER BY id DESC LIMIT 6")->fetchAll();

function calcPrice($p,$r){
  $rG=match($p['purity']){'24Kt'=>$r['rate_24kt'],'18Kt'=>$r['rate_18kt'],'14Kt'=>$r['rate_14kt'],default=>$r['rate_22kt']}??6820;
  $m=$p['weight_grams']*$rG;
  $mk=$p['weight_grams']*$p['making_charges_per_gram']*(1-$p['making_discount_percent']/100);
  $d=0;
  if($p['has_diamond']&&$p['diamond_carat']>0&&$r){$d=$p['diamond_carat']*$r['diamond_rate_per_ct']*(1-$p['diamond_discount_percent']/100);}
  return round(($m+$mk+$d)*1.03);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Netanis Jewelos | Crafted Heritage, Cherished Forever</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,600&family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
:root{--gold:#C6A769;--gold2:#D4B483;--gold-dark:#A68B52;--dark:#1A1A1A;--mid:#4A4A4A;--muted:#888888;--cream:#F9F7F2;--warm:#FCFBF8;--white:#FFFFFF;--border:#E7E0D4;--border2:#F0EBE3;}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Inter',sans-serif;background:var(--cream);color:var(--dark);overflow-x:hidden;}

/* FONTS PER SECTION */
.f-cinzel{font-family:'Inter',sans-serif;text-transform:uppercase;letter-spacing:0.2em;}
.f-playfair{font-family:'Cormorant Garamond',serif;}
.f-cormorant{font-family:'Cormorant Garamond',serif;}
.f-baskerville{font-family:'Cormorant Garamond',serif;}
.f-jost{font-family:'Inter',sans-serif;}
.f-dm{font-family:'Inter',sans-serif;}

/* ANIMATIONS */
@keyframes fadeUp{from{opacity:0;transform:translateY(40px)}to{opacity:1;transform:translateY(0)}}
@keyframes fadeLeft{from{opacity:0;transform:translateX(50px)}to{opacity:1;transform:translateX(0)}}
@keyframes fadeRight{from{opacity:0;transform:translateX(-50px)}to{opacity:1;transform:translateX(0)}}
@keyframes scaleIn{from{opacity:0;transform:scale(0.93)}to{opacity:1;transform:scale(1)}}
@keyframes shimmer{0%{background-position:200% 0}100%{background-position:-200% 0}}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:0.4}}
@keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-12px)}}
@keyframes rotateSlow{from{transform:rotate(0)}to{transform:rotate(360deg)}}
@keyframes goldGlow{0%,100%{box-shadow:0 0 0 0 rgba(184,134,11,0)}50%{box-shadow:0 0 30px 8px rgba(184,134,11,0.15)}}

.anim-up{opacity:0;animation:fadeUp 0.9s cubic-bezier(0.16,1,0.3,1) forwards;}
.anim-left{opacity:0;animation:fadeLeft 0.9s cubic-bezier(0.16,1,0.3,1) forwards;}
.anim-right{opacity:0;animation:fadeRight 0.9s cubic-bezier(0.16,1,0.3,1) forwards;}
.anim-scale{opacity:0;animation:scaleIn 0.8s cubic-bezier(0.16,1,0.3,1) forwards;}
.d1{animation-delay:0.1s}.d2{animation-delay:0.2s}.d3{animation-delay:0.35s}.d4{animation-delay:0.5s}.d5{animation-delay:0.65s}.d6{animation-delay:0.8s}

/* GOLD LINE */
.gold-rule{height:2px;background:linear-gradient(90deg,transparent,var(--gold),var(--gold2),var(--gold),transparent);}
.gold-rule-short{width:56px;height:2px;background:linear-gradient(90deg,var(--gold),var(--gold2));}

/* SECTION LABELS */
.sec-tag{font-family:'Inter',sans-serif;text-transform:uppercase;letter-spacing:0.2em;font-size:10px;letter-spacing:0.3em;text-transform:uppercase;color:var(--gold);font-weight:500;}

/* ── HERO ──────────────────────────────────── */
.hero{position:relative;min-height:92vh;display:flex;align-items:center;overflow:hidden;background:#0e0705;}
.hero-bg{position:absolute;inset:0;background:url('https://images.unsplash.com/photo-1573408301185-9519f94816b5?q=80&w=1800') center/cover;opacity:0.18;transform:scale(1.05);animation:heroZoom 18s ease-in-out infinite alternate;}
@keyframes heroZoom{0%{transform:scale(1.05)}100%{transform:scale(1.12)}}
.hero-overlay{position:absolute;inset:0;background:linear-gradient(135deg,rgba(14,7,5,0.98) 0%,rgba(26,15,10,0.88) 45%,rgba(26,15,10,0.3) 100%);}
.hero-content{position:relative;z-index:2;max-width:1380px;margin:0 auto;padding:100px 60px;width:100%;display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:center;}
.hero-eyebrow{font-family:'Inter',sans-serif;text-transform:uppercase;letter-spacing:0.2em;font-size:10px;letter-spacing:0.5em;text-transform:uppercase;color:var(--gold);margin-bottom:24px;display:flex;align-items:center;gap:12px;}
.hero-eyebrow::after{content:'';flex:1;max-width:60px;height:1px;background:var(--gold);opacity:0.5;}
.hero-h1{font-family:'Cormorant Garamond',serif;font-size:72px;font-weight:600;color:#fff;line-height:1.06;letter-spacing:0.01em;margin-bottom:24px;}
.hero-h1 em{font-style:italic;color:var(--gold2);font-weight:300;}
.hero-sub{font-family:'Inter',sans-serif;font-size:15px;color:rgba(255,255,255,0.5);font-weight:300;line-height:1.8;margin-bottom:36px;max-width:440px;}
.hero-btns{display:flex;gap:14px;margin-bottom:48px;flex-wrap:wrap;}
.btn-hero-primary{font-family:'Inter',sans-serif;font-size:11px;font-weight:700;letter-spacing:0.18em;text-transform:uppercase;background:var(--gold);color:#fff;padding:16px 36px;text-decoration:none;transition:all 0.3s;border:2px solid var(--gold);}
.btn-hero-primary:hover{background:var(--gold2);border-color:var(--gold2);transform:translateY(-2px);box-shadow:0 12px 36px rgba(184,134,11,0.3);}
.btn-hero-outline{font-family:'Inter',sans-serif;font-size:11px;font-weight:700;letter-spacing:0.18em;text-transform:uppercase;color:#fff;padding:16px 36px;text-decoration:none;border:2px solid rgba(255,255,255,0.25);transition:all 0.3s;}
.btn-hero-outline:hover{border-color:var(--gold);color:var(--gold);}
.hero-rates{display:flex;gap:28px;flex-wrap:wrap;}
.hero-rate-item{font-family:'Inter',sans-serif;font-size:12px;color:rgba(255,255,255,0.45);}
.hero-rate-item strong{color:var(--gold2);font-weight:600;}

/* HERO RIGHT - IMAGE COLLAGE */
.hero-collage{position:relative;height:520px;}
.hc-main{position:absolute;right:0;top:0;width:300px;height:380px;border-radius:4px;overflow:hidden;border:1px solid rgba(184,134,11,0.3);animation:float 6s ease-in-out infinite;}
.hc-main img{width:100%;height:100%;object-fit:cover;}
.hc-s1{position:absolute;left:0;bottom:0;width:200px;height:240px;border-radius:4px;overflow:hidden;border:1px solid rgba(184,134,11,0.2);animation:float 8s ease-in-out infinite 1s;}
.hc-s1 img{width:100%;height:100%;object-fit:cover;}
.hc-badge{position:absolute;top:40px;left:20px;background:rgba(26,15,10,0.9);border:1px solid var(--gold);padding:14px 18px;backdrop-filter:blur(10px);}
.hc-badge-label{font-family:'Inter',sans-serif;text-transform:uppercase;letter-spacing:0.2em;font-size:9px;letter-spacing:0.2em;color:var(--gold);text-transform:uppercase;}
.hc-badge-val{font-family:'Cormorant Garamond',serif;font-size:22px;color:#fff;font-weight:600;margin-top:2px;}
.hc-ornament{position:absolute;right:-20px;bottom:80px;width:100px;height:100px;border:1px solid rgba(184,134,11,0.2);border-radius:50%;animation:rotateSlow 30s linear infinite;}
.hc-ornament::before{content:'';position:absolute;inset:12px;border:1px solid rgba(184,134,11,0.15);border-radius:50%;}

/* ── TICKER TRUST ──────────────────────────── */
.trust-row{background:#fff;border-top:1px solid var(--border);border-bottom:1px solid var(--border);padding:28px 0;}
.trust-inner{max-width:1380px;margin:0 auto;padding:0 60px;display:grid;grid-template-columns:repeat(4,1fr);gap:0;text-align:center;}
.trust-item{padding:0 24px;border-right:1px solid var(--border);display:flex;flex-direction:column;align-items:center;gap:10px;transition:all 0.3s;}
.trust-item:hover{transform:translateY(-3px);}
.trust-item:last-child{border-right:none;}
.trust-icon{width:46px;height:46px;background:linear-gradient(135deg,#fdf3e0,#f5e4c0);border-radius:50%;display:flex;align-items:center;justify-content:center;color:var(--gold);font-size:18px;transition:all 0.3s;}
.trust-item:hover .trust-icon{background:var(--gold);color:#fff;animation:goldGlow 1.5s ease infinite;}
.trust-title{font-family:'Inter',sans-serif;font-size:12px;font-weight:700;color:var(--dark);letter-spacing:0.04em;}
.trust-sub{font-family:'Inter',sans-serif;font-size:11px;color:var(--muted);font-weight:300;}

/* ── CATEGORIES ────────────────────────────── */
.categories-section{padding:90px 60px;max-width:1380px;margin:0 auto;}
.section-header{text-align:center;margin-bottom:56px;}
.sec-h2-playfair{font-family:'Cormorant Garamond',serif;font-size:44px;font-weight:600;color:var(--dark);line-height:1.15;margin:12px 0;}
.sec-h2-cormorant{font-family:'Cormorant Garamond',serif;font-size:48px;font-weight:600;color:var(--dark);line-height:1.1;margin:12px 0;}
.sec-h2-cinzel{font-family:'Inter',sans-serif;text-transform:uppercase;letter-spacing:0.2em;font-size:32px;font-weight:500;color:var(--dark);line-height:1.2;margin:12px 0;letter-spacing:0.06em;}

.cat-grid{display:grid;grid-template-columns:repeat(3,1fr);grid-template-rows:repeat(2,220px);gap:14px;}
.cat-card{position:relative;overflow:hidden;border-radius:4px;cursor:pointer;}
.cat-card.span2{grid-column:span 2;}
.cat-card img{width:100%;height:100%;object-fit:cover;transition:transform 0.8s cubic-bezier(0.25,0.8,0.25,1);}
.cat-card:hover img{transform:scale(1.08);}
.cat-overlay{position:absolute;inset:0;background:linear-gradient(to top,rgba(14,7,5,0.75) 0%,rgba(14,7,5,0.1) 55%,transparent 100%);transition:all 0.4s;}
.cat-card:hover .cat-overlay{background:linear-gradient(to top,rgba(14,7,5,0.85) 0%,rgba(14,7,5,0.25) 55%,transparent 100%);}
.cat-content{position:absolute;bottom:0;left:0;right:0;padding:24px 22px;}
.cat-label{font-family:'Inter',sans-serif;text-transform:uppercase;letter-spacing:0.2em;font-size:14px;font-weight:500;color:#fff;letter-spacing:0.12em;text-transform:uppercase;margin-bottom:6px;transition:all 0.3s;}
.cat-card:hover .cat-label{color:var(--gold2);}
.cat-sub{font-family:'Inter',sans-serif;font-size:11px;color:rgba(255,255,255,0.55);font-weight:300;letter-spacing:0.04em;transform:translateY(8px);opacity:0;transition:all 0.35s;}
.cat-card:hover .cat-sub{transform:translateY(0);opacity:1;}
.cat-arrow{display:inline-flex;align-items:center;gap:6px;font-family:'Inter',sans-serif;font-size:10px;font-weight:600;letter-spacing:0.12em;text-transform:uppercase;color:var(--gold);margin-top:8px;text-decoration:none;transform:translateX(-8px);opacity:0;transition:all 0.35s;}
.cat-card:hover .cat-arrow{transform:translateX(0);opacity:1;}

/* ── FEATURED PRODUCTS ─────────────────────── */
.featured-section{background:linear-gradient(180deg,#f9f4ee 0%,#f5ede0 100%);padding:90px 0;}
.featured-inner{max-width:1380px;margin:0 auto;padding:0 60px;}
.featured-header{display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:48px;}
.products-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px;}

/* PRODUCT CARD */
.pcard{background:#fff;border:1px solid var(--border);overflow:hidden;transition:all 0.45s cubic-bezier(0.25,0.8,0.25,1);}
.pcard:hover{transform:translateY(-10px);box-shadow:0 32px 80px rgba(26,15,10,0.13);border-color:#d4c4b4;}
.pcard-img{position:relative;aspect-ratio:1;overflow:hidden;background:var(--warm);}
.pcard-img img{width:100%;height:100%;object-fit:cover;transition:transform 0.8s cubic-bezier(0.25,0.8,0.25,1);}
.pcard:hover .pcard-img img{transform:scale(1.1);}
.pcard-overlay{position:absolute;inset:0;background:linear-gradient(to top,rgba(26,15,10,0.6) 0%,transparent 50%);opacity:0;transition:opacity 0.4s;}
.pcard:hover .pcard-overlay{opacity:1;}
.pcard-actions{position:absolute;bottom:0;left:0;right:0;padding:18px;display:flex;gap:8px;transform:translateY(20px);opacity:0;transition:all 0.4s cubic-bezier(0.25,0.8,0.25,1);}
.pcard:hover .pcard-actions{transform:translateY(0);opacity:1;}
.pcard-badge-d{position:absolute;top:12px;left:12px;background:var(--gold);color:#fff;font-family:'Inter',sans-serif;font-size:9px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;padding:4px 10px;}
.pcard-badge-sale{position:absolute;top:12px;right:12px;background:#2d7a3a;color:#fff;font-family:'Inter',sans-serif;font-size:9px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;padding:4px 10px;}
.btn-pc-cart{flex:1;background:#fff;color:var(--dark);font-family:'Inter',sans-serif;font-size:10px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;border:none;padding:10px 0;cursor:pointer;transition:all 0.25s;}
.btn-pc-cart:hover{background:var(--gold);color:#fff;}
.btn-pc-view{background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.35);color:#fff;font-family:'Inter',sans-serif;font-size:10px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;padding:10px 18px;text-decoration:none;transition:all 0.25s;white-space:nowrap;}
.btn-pc-view:hover{background:#fff;color:var(--dark);}
.btn-pc-wish{background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.35);color:#fff;padding:10px 12px;font-size:14px;cursor:pointer;transition:all 0.25s;}
.btn-pc-wish:hover{background:#e53e3e;border-color:#e53e3e;}
.pcard-body{padding:20px 22px;}
.pcard-meta{font-family:'Inter',sans-serif;text-transform:uppercase;letter-spacing:0.2em;font-size:9px;letter-spacing:0.2em;text-transform:uppercase;color:var(--gold);margin-bottom:7px;}
.pcard-name{font-family:'Cormorant Garamond',serif;font-size:18px;font-weight:600;color:var(--dark);margin-bottom:6px;line-height:1.25;text-decoration:none;display:block;transition:color 0.2s;}
.pcard-name:hover{color:var(--gold);}
.pcard-desc{font-family:'Inter',sans-serif;font-size:12px;color:var(--muted);font-weight:300;line-height:1.6;margin-bottom:16px;}
.pcard-foot{display:flex;justify-content:space-between;align-items:center;border-top:1px solid #f0ebe4;padding-top:14px;}
.pcard-price{font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:700;color:#7c2424;}
.pcard-gst{font-family:'Inter',sans-serif;font-size:9px;color:var(--muted);margin-top:1px;}
.btn-pc-add{background:var(--dark);color:#fff;font-family:'Inter',sans-serif;font-size:9px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;border:none;padding:10px 18px;cursor:pointer;transition:all 0.25s;display:flex;align-items:center;gap:6px;}
.btn-pc-add:hover{background:var(--gold);}

/* ── MARQUEE STRIP ─────────────────────────── */
.marquee-strip{background:var(--dark);padding:18px 0;overflow:hidden;}
.marquee-track{display:inline-flex;white-space:nowrap;animation:marquee 25s linear infinite;}
.marquee-item{font-family:'Inter',sans-serif;text-transform:uppercase;letter-spacing:0.2em;font-size:11px;letter-spacing:0.3em;text-transform:uppercase;color:rgba(212,168,42,0.7);padding:0 40px;display:inline-flex;align-items:center;gap:16px;}
.marquee-item::after{content:'✦';color:var(--gold);font-size:8px;}
@keyframes marquee{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}

/* ── PROMISE / ABOUT STRIP ─────────────────── */
.promise-section{padding:100px 60px;max-width:1380px;margin:0 auto;display:grid;grid-template-columns:1fr 1fr;gap:80px;align-items:center;}
.promise-img{position:relative;}
.promise-img-main{width:100%;height:480px;object-fit:cover;border-radius:2px;}
.promise-img-accent{position:absolute;bottom:-30px;right:-30px;width:220px;height:280px;object-fit:cover;border:6px solid var(--cream);border-radius:2px;box-shadow:0 20px 60px rgba(26,15,10,0.2);}
.promise-content{padding-left:20px;}
.promise-list{list-style:none;margin-top:28px;space-y:14px;}
.promise-list li{font-family:'Inter',sans-serif;font-size:14px;color:#5a4030;font-weight:300;line-height:1.7;display:flex;align-items:flex-start;gap:12px;padding:10px 0;border-bottom:1px solid var(--border);}
.promise-list li:last-child{border-bottom:none;}
.promise-list li i{color:var(--gold);margin-top:3px;font-size:13px;flex-shrink:0;}

/* ── OFFER BANNER ──────────────────────────── */
.offer-banner{position:relative;overflow:hidden;padding:100px 60px;text-align:center;}
.offer-bg{position:absolute;inset:0;background:url('https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?q=80&w=1800') center/cover;opacity:0.12;}
.offer-overlay{position:absolute;inset:0;background:linear-gradient(135deg,#0a0503 0%,#1a0f0a 100%);}
.offer-content{position:relative;z-index:2;max-width:680px;margin:0 auto;}

/* ── TESTIMONIALS ──────────────────────────── */
.testi-section{padding:90px 60px;max-width:1380px;margin:0 auto;}
.testi-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;margin-top:48px;}
.testi-card{background:#fff;border:1px solid var(--border);padding:32px;position:relative;transition:all 0.4s;}
.testi-card:hover{transform:translateY(-6px);box-shadow:0 24px 60px rgba(26,15,10,0.08);border-color:#d4c4b4;}
.testi-stars{color:var(--gold);font-size:13px;margin-bottom:16px;letter-spacing:2px;}
.testi-text{font-family:'Cormorant Garamond',serif;font-size:14px;font-style:italic;color:#5a4030;line-height:1.8;margin-bottom:20px;}
.testi-author{font-family:'Inter',sans-serif;font-size:12px;font-weight:600;color:var(--dark);}
.testi-city{font-family:'Inter',sans-serif;font-size:11px;color:var(--muted);margin-top:2px;}
.testi-quote{position:absolute;top:20px;right:24px;font-family:'Cormorant Garamond',serif;font-size:60px;color:rgba(184,134,11,0.08);line-height:1;font-weight:700;}

/* BUTTONS */
.btn-dark{font-family:'Inter',sans-serif;font-size:11px;font-weight:700;letter-spacing:0.18em;text-transform:uppercase;background:var(--dark);color:#fff;padding:14px 32px;text-decoration:none;transition:all 0.3s;border:2px solid var(--dark);}
.btn-dark:hover{background:var(--gold);border-color:var(--gold);}
.btn-gold-outline{font-family:'Inter',sans-serif;font-size:11px;font-weight:700;letter-spacing:0.18em;text-transform:uppercase;color:var(--gold);padding:14px 32px;text-decoration:none;border:2px solid var(--gold);transition:all 0.3s;}
.btn-gold-outline:hover{background:var(--gold);color:#fff;}

/* MOBILE RESPONSIVE */
@media(max-width:1024px){
  .hero-content{grid-template-columns:1fr;padding:80px 32px;}
  .hero-collage{display:none;}
  .hero-h1{font-size:48px;}
  .cat-grid{grid-template-columns:repeat(2,1fr);grid-template-rows:auto;}
  .cat-card.span2{grid-column:span 1;}
  .promise-section{grid-template-columns:1fr;gap:40px;}
  .products-grid{grid-template-columns:repeat(2,1fr);}
  .testi-grid{grid-template-columns:1fr;}
  .trust-inner{grid-template-columns:repeat(2,1fr);}
}
</style>
</head>
<body>
<?php include 'components/header.php'; ?>

<!-- ══ HERO ════════════════════════════════════════ -->
<section class="hero">
  <div class="hero-bg"></div>
  <div class="hero-overlay"></div>
  <div class="hero-content">

    <div>
      <div class="hero-eyebrow anim-up d1"><i class="fa-solid fa-gem fa-xs" style="color:var(--gold);margin-right:6px;"></i> New Collection 2026</div>
      <h1 class="hero-h1 anim-up d2">Crafted Heritage,<br><em>Cherished Forever.</em></h1>
      <p class="hero-sub anim-up d3">Discover timeless jewellery from Jodhpur's finest artisans — BIS-certified gold & IGI-certified diamonds at live market prices.</p>
      <div class="hero-btns anim-up d4">
        <a href="shop.php" class="btn-hero-primary"><i class="fa-solid fa-sparkles fa-xs"></i>&ensp;Explore Collection</a>
        <a href="shop.php?has_diamond=1" class="btn-hero-outline"><i class="fa-solid fa-gem fa-xs"></i>&ensp;Diamond Jewellery</a>
      </div>
      <?php if($latestRate): ?>
      <div class="hero-rates anim-up d5">
        <div class="hero-rate-item"><i class="fa-solid fa-circle fa-2xs" style="color:var(--gold);margin-right:6px;animation:pulse 2s infinite;"></i>22Kt: <strong>₹<?php echo number_format($latestRate['rate_22kt']); ?>/g</strong></div>
        <div class="hero-rate-item"><i class="fa-solid fa-circle fa-2xs" style="color:rgba(255,255,255,0.2);margin-right:6px;"></i>24Kt: <strong>₹<?php echo number_format($latestRate['rate_24kt']); ?>/g</strong></div>
        <div class="hero-rate-item"><i class="fa-solid fa-circle fa-2xs" style="color:rgba(255,255,255,0.2);margin-right:6px;"></i>Diamond: <strong>₹<?php echo number_format($latestRate['diamond_rate_per_ct']); ?>/ct</strong></div>
      </div>
      <?php endif; ?>
    </div>

    <div class="hero-collage anim-scale d3">
      <div class="hc-main">
        <img src="https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?q=80&w=600" alt="Necklace">
      </div>
      <div class="hc-s1">
        <img src="https://images.unsplash.com/photo-1605100804763-247f67b3557e?q=80&w=400" alt="Ring">
      </div>
      <?php if($latestRate): ?>
      <div class="hc-badge anim-up d6">
        <div class="hc-badge-label"><i class="fa-solid fa-circle fa-2xs" style="color:var(--gold);margin-right:5px;animation:pulse 2s infinite;"></i>Live Gold 22Kt</div>
        <div class="hc-badge-val">₹<?php echo number_format($latestRate['rate_22kt']); ?><small style="font-size:14px;color:var(--gold);">/g</small></div>
      </div>
      <?php endif; ?>
      <div class="hc-ornament"></div>
    </div>
  </div>
</section>

<!-- ══ TRUST BAR ══════════════════════════════════ -->
<div class="trust-row">
  <div class="trust-inner">
    <?php foreach([
      ['fa-certificate','BIS Hallmark Certified','Every gold piece guaranteed'],
      ['fa-gem','IGI Diamond Certified','Authentic, traced diamonds only'],
      ['fa-rotate-left','7-Day Easy Returns','Hassle-free exchange policy'],
      ['fa-shield-halved','Lifetime Jewellery Care','Free cleaning & polishing'],
    ] as $t): ?>
    <div class="trust-item">
      <div class="trust-icon"><i class="fa-solid <?php echo $t[0]; ?>"></i></div>
      <div>
        <div class="trust-title"><?php echo $t[1]; ?></div>
        <div class="trust-sub"><?php echo $t[2]; ?></div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- ══ SHOP BY CATEGORY ═══════════════════════════ -->
<section style="padding:90px 60px;max-width:1380px;margin:0 auto;">
  <div class="section-header anim-up">
    <div class="sec-tag"><i class="fa-solid fa-layer-group fa-xs" style="margin-right:8px;"></i>Curated For You</div>
    <h2 class="sec-h2-playfair" style="margin-top:10px;">Shop by Category</h2>
    <div class="gold-rule-short" style="margin:14px auto 0;"></div>
  </div>
  <div class="cat-grid anim-scale d2">
    <a href="shop.php?cat=Rings" class="cat-card span2" style="grid-row:span 1;">
      <img src="https://images.unsplash.com/photo-1605100804763-247f67b3557e?q=80&w=900" alt="Rings" loading="lazy">
      <div class="cat-overlay"></div>
      <div class="cat-content">
        <div class="cat-label">Rings</div>
        <div class="cat-sub"><i class="fa-solid fa-ring fa-xs"></i>&ensp;Engagement · Solitaire · Daily Wear</div>
        <a href="shop.php?cat=Rings" class="cat-arrow">Shop Rings <i class="fa-solid fa-arrow-right fa-xs"></i></a>
      </div>
    </a>
    <a href="shop.php?cat=Earrings" class="cat-card">
      <img src="https://images.unsplash.com/photo-1535632787350-4e68ef0ac584?q=80&w=500" alt="Earrings" loading="lazy">
      <div class="cat-overlay"></div>
      <div class="cat-content">
        <div class="cat-label">Earrings</div>
        <div class="cat-sub"><i class="fa-solid fa-droplet fa-xs"></i>&ensp;Studs · Jhumkas · Hoops</div>
        <a href="shop.php?cat=Earrings" class="cat-arrow">Shop <i class="fa-solid fa-arrow-right fa-xs"></i></a>
      </div>
    </a>
    <a href="shop.php?cat=Necklaces" class="cat-card">
      <img src="https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?q=80&w=500" alt="Necklaces" loading="lazy">
      <div class="cat-overlay"></div>
      <div class="cat-content">
        <div class="cat-label">Necklaces</div>
        <div class="cat-sub"><i class="fa-solid fa-link fa-xs"></i>&ensp;Choker · Bridal · Layered</div>
        <a href="shop.php?cat=Necklaces" class="cat-arrow">Shop <i class="fa-solid fa-arrow-right fa-xs"></i></a>
      </div>
    </a>
    <a href="shop.php?cat=Bangles" class="cat-card">
      <img src="https://images.unsplash.com/photo-1611591437281-460bfbe1220a?q=80&w=500" alt="Bangles" loading="lazy">
      <div class="cat-overlay"></div>
      <div class="cat-content">
        <div class="cat-label">Bangles</div>
        <div class="cat-sub"><i class="fa-solid fa-circle-notch fa-xs"></i>&ensp;Gold · Diamond · Bridal</div>
        <a href="shop.php?cat=Bangles" class="cat-arrow">Shop <i class="fa-solid fa-arrow-right fa-xs"></i></a>
      </div>
    </a>
    <a href="shop.php?has_diamond=1" class="cat-card">
      <img src="https://images.unsplash.com/photo-1608042314453-ae338d682c93?q=80&w=500" alt="Diamond" loading="lazy">
      <div class="cat-overlay"></div>
      <div class="cat-content">
        <div class="cat-label" style="color:var(--gold2);"><i class="fa-solid fa-gem fa-xs"></i>&ensp;Diamond</div>
        <div class="cat-sub">IGI Certified · Natural & Lab Grown</div>
        <a href="shop.php?has_diamond=1" class="cat-arrow">Explore <i class="fa-solid fa-arrow-right fa-xs"></i></a>
      </div>
    </a>
    <a href="shop.php?collection=Bridal" class="cat-card">
      <img src="https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?q=80&w=500" alt="Bridal" loading="lazy">
      <div class="cat-overlay"></div>
      <div class="cat-content">
        <div class="cat-label"><i class="fa-solid fa-crown fa-xs"></i>&ensp;Bridal</div>
        <div class="cat-sub">Royal Rajwadi · Wedding · Reception</div>
        <a href="shop.php?collection=Bridal" class="cat-arrow">View Bridal <i class="fa-solid fa-arrow-right fa-xs"></i></a>
      </div>
    </a>
  </div>
</section>

<!-- ══ MARQUEE ═════════════════════════════════════ -->
<div class="marquee-strip">
  <div class="marquee-track">
    <?php foreach(['BIS Hallmark Certified','IGI Diamond Certified','22Kt Pure Gold','Handcrafted in Jodhpur','Free Pan India Delivery','7-Day Returns','Live Market Pricing','Rajwadi Bridal Collection','Transparent Pricing','Lifetime Jewellery Care','BIS Hallmark Certified','IGI Diamond Certified','22Kt Pure Gold','Handcrafted in Jodhpur','Free Pan India Delivery','7-Day Returns','Live Market Pricing'] as $m): ?>
    <span class="marquee-item"><?php echo $m; ?></span>
    <?php endforeach; ?>
  </div>
</div>

<!-- ══ FEATURED PRODUCTS ══════════════════════════ -->
<section class="featured-section">
  <div class="featured-inner">
    <div class="featured-header anim-up">
      <div>
        <div class="sec-tag"><i class="fa-solid fa-fire-flame-curved fa-xs" style="margin-right:8px;"></i>Handpicked Masterpieces</div>
        <h2 class="sec-h2-cormorant" style="margin-top:10px;">Trending Now</h2>
        <div class="gold-rule-short" style="margin-top:12px;"></div>
      </div>
      <a href="shop.php" class="btn-gold-outline" style="align-self:center;">View All <i class="fa-solid fa-arrow-right fa-xs"></i></a>
    </div>

    <div class="products-grid">
      <?php foreach($featured as $p):
        $final = calcPrice($p, $latestRate ?? ['rate_22kt'=>6820,'rate_24kt'=>7450,'rate_18kt'=>5580,'rate_14kt'=>4340,'diamond_rate_per_ct'=>25000]);
        $img = str_contains($p['primary_image'],'http') ? $p['primary_image'] : 'assets/uploads/'.basename($p['primary_image']);
      ?>
      <div class="pcard anim-up">
        <div class="pcard-img">
          <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($p['product_name']); ?>" loading="lazy">
          <?php if($p['has_diamond']): ?><span class="pcard-badge-d"><i class="fa-solid fa-gem fa-xs"></i> Diamond</span><?php endif; ?>
          <?php if($p['making_discount_percent']>0||$p['diamond_discount_percent']>0): ?><span class="pcard-badge-sale"><i class="fa-solid fa-tag fa-xs"></i> Sale</span><?php endif; ?>
          <div class="pcard-overlay"></div>
          <div class="pcard-actions">
            <button class="btn-pc-cart" onclick="addToCartAjax(<?php echo $p['id']; ?>,'<?php echo addslashes($p['product_name']); ?>',this)">
              <i class="fa-solid fa-bag-shopping fa-xs"></i>&ensp;Add to Bag
            </button>
            <a href="product-details.php?id=<?php echo $p['id']; ?>" class="btn-pc-view"><i class="fa-solid fa-eye fa-xs"></i></a>
            <button class="btn-pc-wish" onclick="toggleWish(<?php echo $p['id']; ?>,this)"><i class="fa-regular fa-heart fa-xs"></i></button>
          </div>
        </div>
        <div class="pcard-body">
          <div class="pcard-meta"><?php echo $p['purity']; ?> <?php echo $p['metal_type']; ?><?php if($p['has_diamond']): ?> · Diamond<?php endif; ?></div>
          <a href="product-details.php?id=<?php echo $p['id']; ?>" class="pcard-name"><?php echo htmlspecialchars($p['product_name']); ?></a>
          <div class="pcard-desc"><?php echo htmlspecialchars(substr($p['short_description'],0,75)); ?>...</div>
          <div class="pcard-foot">
            <div>
              <div class="pcard-price">₹<?php echo number_format($final); ?></div>
              <div class="pcard-gst">Incl. 3% GST + Making</div>
            </div>
            <button class="btn-pc-add" onclick="addToCartAjax(<?php echo $p['id']; ?>,'<?php echo addslashes($p['product_name']); ?>',this)">
              <i class="fa-solid fa-plus fa-xs"></i> Bag
            </button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══ OUR PROMISE ════════════════════════════════ -->
<section class="promise-section">
  <div class="promise-img anim-right">
    <img class="promise-img-main" src="https://images.unsplash.com/photo-1583736902935-6b52b2b2359e?q=80&w=800" alt="Craftsmanship">
    <img class="promise-img-accent" src="https://images.unsplash.com/photo-1611591437281-460bfbe1220a?q=80&w=400" alt="Gold bangles">
  </div>
  <div class="promise-content anim-left">
    <div class="sec-tag"><i class="fa-solid fa-handshake fa-xs" style="margin-right:8px;"></i>The Netanis Promise</div>
    <h2 class="sec-h2-cinzel" style="margin-top:12px;font-size:28px;line-height:1.4;">Jodhpur's Heritage.<br>Your <span style="color:var(--gold);">Lifelong Treasure.</span></h2>
    <div class="gold-rule-short" style="margin:16px 0 20px;"></div>
    <p style="font-family:'Inter',sans-serif;font-size:14px;color:#5a4030;font-weight:300;line-height:1.9;margin-bottom:8px;">
      Founded in the artisan lanes of Jodhpur, we craft every piece with the same love that 400-year-old Rajasthani jewellers put into their work — and we do it at prices that are always fair and transparent.
    </p>
    <ul class="promise-list">
      <li><i class="fa-solid fa-check-circle"></i>Live market rate pricing — no hidden markup, always honest</li>
      <li><i class="fa-solid fa-check-circle"></i>BIS hallmark on every gold piece, IGI certification on every diamond</li>
      <li><i class="fa-solid fa-check-circle"></i>Full price breakup visible before purchase — Gold + Making + GST</li>
      <li><i class="fa-solid fa-check-circle"></i>7-day returns, free lifetime maintenance, pan-India delivery</li>
    </ul>
    <a href="about-us.php" class="btn-dark" style="margin-top:28px;display:inline-block;">Our Story <i class="fa-solid fa-arrow-right fa-xs"></i></a>
  </div>
</section>

<!-- ══ OFFER BANNER ═══════════════════════════════ -->
<section class="offer-banner">
  <div class="offer-bg"></div>
  <div class="offer-overlay"></div>
  <div class="offer-content anim-up">
    <div class="sec-tag" style="color:var(--gold2);"><i class="fa-solid fa-bolt fa-xs" style="margin-right:8px;"></i>Limited Period Offer</div>
    <h2 style="font-family:'Cormorant Garamond',serif;font-size:58px;font-weight:600;color:#fff;line-height:1.1;margin:16px 0;">
      Up to <span style="color:var(--gold2);">20% OFF</span><br>on Making Charges
    </h2>
    <p style="font-family:'Inter',sans-serif;font-size:15px;color:rgba(255,255,255,0.5);font-weight:300;margin-bottom:36px;line-height:1.8;">
      Exclusively on diamond jewellery — every masterpiece, at its most precious price.
    </p>
    <a href="shop.php?has_diamond=1" class="btn-hero-primary">
      <i class="fa-solid fa-gem fa-xs"></i>&ensp;Shop Diamond Collection
    </a>
  </div>
</section>

<!-- ══ TESTIMONIALS ═══════════════════════════════ -->
<section class="testi-section">
  <div class="section-header anim-up">
    <div class="sec-tag"><i class="fa-solid fa-quote-left fa-xs" style="margin-right:8px;"></i>What Our Families Say</div>
    <h2 class="sec-h2-playfair" style="margin-top:10px;font-size:38px;">Loved Across Rajasthan</h2>
    <div class="gold-rule-short" style="margin:14px auto 0;"></div>
  </div>
  <div class="testi-grid">
    <?php foreach([
      ['Priya Sharma','Jodhpur','Absolutely in love with the bridal set! The quality is outstanding and the price was completely transparent. Exactly what I expected — and more.',5],
      ['Ramesh Mehta','Jaipur','Ordered a diamond ring for my wife\'s anniversary. The IGI certificate gave us complete confidence. Delivered in 5 days, beautifully packed.',5],
      ['Sunita Rathore','Jodhpur','The gold bangles are exactly 22Kt as promised. The live gold rate pricing is something I\'ve never seen before — so honest and fair!',5],
    ] as $t): ?>
    <div class="testi-card anim-up">
      <div class="testi-quote">"</div>
      <div class="testi-stars"><?php echo str_repeat('★',$t[3]); ?></div>
      <div class="testi-text"><?php echo $t[2]; ?></div>
      <div class="testi-author"><i class="fa-solid fa-user-circle" style="color:var(--gold);margin-right:6px;"></i><?php echo $t[0]; ?></div>
      <div class="testi-city"><i class="fa-solid fa-location-dot fa-xs" style="color:var(--muted);margin-right:5px;"></i><?php echo $t[1]; ?></div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- ══ INSTAGRAM STRIP ════════════════════════════ -->
<div style="background:var(--dark);padding:36px 60px;display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;">
  <div>
    <div style="font-family:'Cormorant Garamond',serif;font-size:24px;color:#fff;font-weight:600;">Follow Our Journey</div>
    <div style="font-family:'Inter',sans-serif;font-size:13px;color:rgba(255,255,255,0.4);margin-top:4px;"><i class="fa-brands fa-instagram" style="color:#e1306c;margin-right:8px;"></i>@netanisjewelos</div>
  </div>
  <div style="display:flex;gap:12px;">
    <a href="https://wa.me/918147349242" target="_blank" style="width:44px;height:44px;background:#25d366;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px;text-decoration:none;transition:all 0.3s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform=''"><i class="fa-brands fa-whatsapp"></i></a>
    <a href="#" style="width:44px;height:44px;background:linear-gradient(135deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px;text-decoration:none;transition:all 0.3s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform=''"><i class="fa-brands fa-instagram"></i></a>
    <a href="#" style="width:44px;height:44px;background:#1877f2;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px;text-decoration:none;transition:all 0.3s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform=''"><i class="fa-brands fa-facebook-f"></i></a>
  </div>
</div>

<?php include 'components/footer.php'; ?>

<script>
function toggleWish(id,btn){
  fetch(`../api/toggle-wishlist.php?id=${id}`)
    .then(r=>r.json()).then(d=>{
      const i=btn.querySelector('i');
      if(d.status==='added'&&i){i.className='fa-solid fa-heart fa-xs';i.style.color='#e53e3e';}
      else if(d.status==='removed'&&i){i.className='fa-regular fa-heart fa-xs';i.style.color='';}
      else if(d.status==='login_required') window.location='login.php';
    });
}

// Intersection Observer for scroll animations
const obs=new IntersectionObserver((entries)=>{
  entries.forEach(e=>{if(e.isIntersecting){e.target.style.animationPlayState='running';}});
},{threshold:0.1});
document.querySelectorAll('.anim-up,.anim-left,.anim-right,.anim-scale').forEach(el=>{
  el.style.animationPlayState='paused';obs.observe(el);
});
</script>
</body>
</html>
