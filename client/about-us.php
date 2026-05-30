<?php session_start(); require_once '../config/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Our Story | Netanis Jewelos — Crafted in Jodhpur</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,600&family=Jost:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;}
body{font-family:'Jost',sans-serif;background:#fdfaf7;color:#3a2a1e;}
.serif{font-family:'Cormorant Garamond',serif;}
.gold{color:#b8860b;}
@keyframes fadeUp{from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:translateY(0)}}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}
@keyframes scaleIn{from{opacity:0;transform:scale(0.95)}to{opacity:1;transform:scale(1)}}
.anim-up{animation:fadeUp 0.8s cubic-bezier(0.25,0.8,0.25,1) both;}
.anim-fade{animation:fadeIn 1s ease both;}
.anim-scale{animation:scaleIn 0.7s ease both;}
.delay-1{animation-delay:0.1s}.delay-2{animation-delay:0.25s}.delay-3{animation-delay:0.4s}.delay-4{animation-delay:0.55s}.delay-5{animation-delay:0.7s}
.gold-line{background:linear-gradient(90deg,#b8860b,#d4a82a,#b8860b);height:2px;width:60px;}
.value-card{background:#fff;border:1px solid #ede8e0;border-radius:6px;padding:28px;transition:all 0.4s cubic-bezier(0.25,0.8,0.25,1);}
.value-card:hover{transform:translateY(-6px);box-shadow:0 20px 50px rgba(26,15,10,0.1);border-color:#d4c4b4;}
.team-img{position:relative;overflow:hidden;border-radius:8px;}
.team-img img{transition:transform 0.7s ease;}
.team-img:hover img{transform:scale(1.05);}
.stat-num{font-family:'Cormorant Garamond',serif;font-size:48px;font-weight:700;color:#b8860b;line-height:1;}
.timeline-item{position:relative;padding-left:40px;padding-bottom:32px;}
.timeline-item::before{content:'';position:absolute;left:10px;top:8px;bottom:-8px;width:2px;background:linear-gradient(#b8860b,#e8d8c8);}
.timeline-item:last-child::before{display:none;}
.timeline-dot{position:absolute;left:0;top:2px;width:22px;height:22px;background:#b8860b;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;color:#fff;font-weight:700;z-index:1;}
.section-tag{font-size:10px;font-weight:700;letter-spacing:0.25em;text-transform:uppercase;color:#b8860b;}
.hero-about{position:relative;height:500px;overflow:hidden;display:flex;align-items:center;}
.hero-about-bg{position:absolute;inset:0;background:url('https://images.unsplash.com/photo-1573408301185-9519f94816b5?q=80&w=1600') center/cover;filter:brightness(0.35);}
.cert-badge{background:#fff;border:1px solid #ede8e0;border-radius:6px;padding:16px 20px;display:flex;align-items:center;gap:14px;transition:all 0.3s;}
.cert-badge:hover{border-color:#b8860b;box-shadow:0 8px 24px rgba(184,134,11,0.1);}
</style>
</head>
<body>
<?php include 'components/header.php'; ?>

<!-- HERO -->
<div class="hero-about">
  <div class="hero-about-bg"></div>
  <div style="position:relative;z-index:2;max-width:1280px;margin:0 auto;padding:0 40px;width:100%;">
    <div class="anim-up" style="max-width:600px;">
      <p class="section-tag" style="color:#d4a82a;margin-bottom:16px;">Est. 2020 · Jodhpur, Rajasthan</p>
      <h1 class="serif" style="font-size:60px;font-weight:600;color:#fff;line-height:1.05;margin-bottom:16px;">
        Where Every Piece<br><em style="color:#d4a82a;font-weight:300;">Tells a Story.</em>
      </h1>
      <div class="gold-line" style="width:80px;"></div>
      <p style="font-size:15px;color:rgba(255,255,255,0.65);font-weight:300;line-height:1.8;margin-top:18px;max-width:480px;">
        Born from the artisan lanes of Jodhpur, Netanis Jewelos is a celebration of craftsmanship, heritage, and pure gold.
      </p>
    </div>
  </div>
</div>

<!-- STATS BAR -->
<div style="background:#1a0f0a;padding:28px 0;">
  <div style="max-width:1280px;margin:0 auto;padding:0 40px;display:grid;grid-template-columns:repeat(4,1fr);gap:0;text-align:center;">
    <?php foreach([['500+','Happy Families'],['100%','BIS Hallmark'],['6+','Collections'],['2020','Est. Year']] as $s): ?>
    <div style="padding:8px 20px;border-right:1px solid #2e1e14;" class="last:border-0">
      <div class="stat-num"><?php echo $s[0]; ?></div>
      <div style="font-size:11px;color:#9a7a60;letter-spacing:0.1em;text-transform:uppercase;margin-top:4px;font-weight:500;"><?php echo $s[1]; ?></div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- MAIN CONTENT -->
<div style="max-width:1100px;margin:0 auto;padding:80px 40px;">

  <!-- OUR STORY -->
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:80px;align-items:center;margin-bottom:100px;" class="anim-up">
    <div>
      <p class="section-tag" style="margin-bottom:14px;">Our Story</p>
      <h2 class="serif" style="font-size:42px;font-weight:600;color:#1a0f0a;line-height:1.1;margin-bottom:16px;">A Legacy Born in<br>the Heart of <span class="gold">Jodhpur</span></h2>
      <div class="gold-line" style="margin-bottom:24px;"></div>
      <p style="font-size:14px;color:#5a4030;font-weight:300;line-height:1.9;margin-bottom:18px;">
        Founded in 2020 by <strong style="color:#1a0f0a;font-weight:600;">Jatin Netani</strong>
      </p>
      <p style="font-size:14px;color:#5a4030;font-weight:300;line-height:1.9;margin-bottom:18px;">
        What began as a small atelier has blossomed into a trusted destination for families across Rajasthan — where every piece is crafted with BIS-certified gold, every diamond is IGI-authenticated, and every price is transparently calculated live from market rates.
      </p>
      <p style="font-size:14px;color:#5a4030;font-weight:300;line-height:1.9;">
        Our name "Netanis" is a blend of commitment and craftsmanship — a promise that what you wear reflects who you are. <em style="color:#b8860b;font-style:italic;">Heritage, honesty, and heart</em> — that's the Netanis way.
      </p>
    </div>
    <div class="team-img anim-scale delay-2">
      <img src="assets/uploads/JATIN.jpeg" alt="Jatin Netani — Founder, Netanis Jewelos" style="width:100%;border-radius:8px;box-shadow:0 24px 64px rgba(26,15,10,0.2);object-fit:cover;height:480px;"> 
      <div style="background:#fff;border:1px solid #ede8e0;border-radius:6px;padding:16px 20px;margin-top:-40px;margin-right:20px;margin-left:20px;position:relative;z-index:2;box-shadow:0 8px 24px rgba(26,15,10,0.08);">
        <div class="serif" style="font-size:18px;font-weight:600;color:#1a0f0a;">JATIN NETANI</div>
        <div style="font-size:11px;color:#b8860b;letter-spacing:0.1em;text-transform:uppercase;font-weight:600;margin-top:3px;">Founder & Master Craftsman</div>
        <div style="font-size:12px;color:#9a8070;margin-top:6px;line-height:1.6;">15+ years in jewellery craftsmanship · Jodhpur, Rajasthan</div>
      </div>
    </div>
  </div>

  <!-- TIMELINE -->
  <div style="margin-bottom:100px;" class="anim-up delay-2">
    <div style="text-align:center;margin-bottom:48px;">
      <p class="section-tag" style="margin-bottom:12px;">Our Journey</p>
      <h2 class="serif" style="font-size:38px;font-weight:600;color:#1a0f0a;">Milestones That Define Us</h2>
      <div class="gold-line" style="margin:14px auto 0;"></div>
    </div>
    <div style="max-width:640px;margin:0 auto;">
      <?php foreach([
        ['2020','The Beginning','Netanis Jewelos founded in Sardar Market, Jodhpur. First collection of 22Kt gold rings launched with full BIS hallmark certification.'],
        ['2021','Diamond Division','Launched our IGI-certified diamond jewellery line. Partnered with certified diamond cutters to offer authentic stones with full traceability.'],
        ['2022','Bridal Collection','Introduced the Royal Rajwadi Bridal Collection — handcrafted sets for the discerning Rajasthani bride.'],
        ['2023','Live Pricing','Became one of the first jewellers in Jodhpur to offer fully transparent, live gold-rate-based pricing online.'],
        ['2024','Online Store','Launched our complete digital platform with real-time pricing, wishlist, and WhatsApp ordering.'],
        ['2026','500+ Families','Proudly serving over 500 families across Rajasthan with certified, authentic jewellery.'],
      ] as $tl): ?>
      <div class="timeline-item">
        <div class="timeline-dot"><?php echo substr($tl[0],-2); ?></div>
        <div style="font-size:11px;color:#b8860b;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;margin-bottom:4px;"><?php echo $tl[0]; ?></div>
        <div class="serif" style="font-size:18px;font-weight:600;color:#1a0f0a;margin-bottom:6px;"><?php echo $tl[1]; ?></div>
        <div style="font-size:13px;color:#7a6050;font-weight:300;line-height:1.7;"><?php echo $tl[2]; ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- VALUES -->
  <div style="margin-bottom:100px;" class="anim-up delay-3">
    <div style="text-align:center;margin-bottom:48px;">
      <p class="section-tag" style="margin-bottom:12px;">What We Stand For</p>
      <h2 class="serif" style="font-size:38px;font-weight:600;color:#1a0f0a;">Our Core Values</h2>
      <div class="gold-line" style="margin:14px auto 0;"></div>
    </div>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:24px;">
      <?php foreach([
        ['🔒','Purity First','Every gram of gold is BIS hallmarked. Every diamond is IGI certified. We never compromise on quality.'],
        ['💰','Live Pricing','Our prices are calculated in real-time from actual gold market rates — transparent and always fair.'],
        ['🤝','Family Trust','We treat every customer as family. 7-day returns, lifetime maintenance, and personal WhatsApp support.'],
        ['🏺','Craftsmanship','Each piece is handcrafted by skilled artisans carrying forward the 400-year jewellery tradition of Jodhpur.'],
        ['🌿','Ethical Sourcing','All diamonds and precious metals are sourced ethically and responsibly — from certified suppliers only.'],
        ['📱','Transparent Tech','Our digital platform gives you complete price breakup — gold cost, making, diamond, GST — nothing hidden.'],
      ] as $v): ?>
      <div class="value-card">
        <div style="font-size:32px;margin-bottom:14px;"><?php echo $v[0]; ?></div>
        <div class="serif" style="font-size:18px;font-weight:600;color:#1a0f0a;margin-bottom:8px;"><?php echo $v[1]; ?></div>
        <div style="font-size:13px;color:#7a6050;font-weight:300;line-height:1.7;"><?php echo $v[2]; ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- CERTIFICATIONS -->
  <div style="margin-bottom:80px;" class="anim-up delay-4">
    <div style="text-align:center;margin-bottom:36px;">
      <p class="section-tag" style="margin-bottom:12px;">Certifications</p>
      <h2 class="serif" style="font-size:34px;font-weight:600;color:#1a0f0a;">Our Trust Credentials</h2>
    </div>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
      <?php foreach([
        ['🏛️','BIS Hallmark','Bureau of Indian Standards hallmark on every gold product'],
        ['💎','IGI Certified','International Gemological Institute certified diamonds'],
        ['🇮🇳','Made in India','100% handcrafted by Indian artisans in Jodhpur'],
        ['🔄','7-Day Returns','No-questions-asked return and exchange policy'],
        ['🛡️','Lifetime Care','Complimentary maintenance and polishing for life'],
        ['📦','Insured Delivery','Every shipment fully insured for complete peace of mind'],
      ] as $cert): ?>
      <div class="cert-badge">
        <span style="font-size:28px;"><?php echo $cert[0]; ?></span>
        <div>
          <div style="font-size:13px;font-weight:600;color:#1a0f0a;"><?php echo $cert[1]; ?></div>
          <div style="font-size:11px;color:#9a8070;margin-top:2px;"><?php echo $cert[2]; ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- CONTACT CTA -->
  <div style="background:linear-gradient(135deg,#1a0f0a,#3d2010);border-radius:12px;padding:56px;text-align:center;" class="anim-up delay-5">
    <p class="section-tag" style="color:#b8860b;margin-bottom:12px;">Visit Our Store</p>
    <h2 class="serif" style="font-size:38px;color:#fff;font-weight:600;margin-bottom:12px;">Come Experience Netanis</h2>
    <p style="font-size:14px;color:rgba(255,255,255,0.55);font-weight:300;margin-bottom:28px;max-width:500px;margin-left:auto;margin-right:auto;line-height:1.8;">
      Visit us at Sardar Market, Clock Tower, Jodhpur. Our craftsmen will help you find or create the perfect piece.
    </p>
    <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap;">
      <a href="https://wa.me/918147349242" target="_blank" style="background:#25d366;color:#fff;text-decoration:none;padding:14px 28px;font-size:12px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;border-radius:4px;display:inline-flex;align-items:center;gap:8px;transition:background 0.2s;" onmouseover="this.style.background='#1ebe5d'" onmouseout="this.style.background='#25d366'">💬 WhatsApp Us</a>
      <a href="shop.php" style="background:transparent;color:#d4a82a;border:1.5px solid #b8860b;text-decoration:none;padding:14px 28px;font-size:12px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;border-radius:4px;transition:all 0.2s;" onmouseover="this.style.background='#b8860b';this.style.color='#fff'" onmouseout="this.style.background='transparent';this.style.color='#d4a82a'">Explore Collection →</a>
    </div>
    <div style="margin-top:24px;font-size:12px;color:rgba(255,255,255,0.4);">
      📍 Sardar Market, Clock Tower Area, Jodhpur, Rajasthan – 342001 &nbsp;|&nbsp; 📞 +91-814-734-9242 &nbsp;|&nbsp; Mon–Sat 10am–8pm
    </div>
  </div>

</div>

<?php include 'components/footer.php'; ?>
</body>
</html>
