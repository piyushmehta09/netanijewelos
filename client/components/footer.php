<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
/* ── FOOTER LIGHT THEME ─────────────────────── */
.nf{font-family:'Inter',sans-serif;background:#F4F1EC;color:#4A4A4A;border-top:1px solid #E7E0D4;}

/* NEWSLETTER */
.nf-nl{background:#fff;border-bottom:1px solid #E7E0D4;padding:48px 0;}
.nf-nl-inner{max-width:1200px;margin:0 auto;padding:0 40px;display:flex;align-items:center;justify-content:space-between;gap:32px;flex-wrap:wrap;}
.nf-nl-left h4{font-family:'Cormorant Garamond',serif;font-size:26px;font-weight:600;color:#1A1A1A;margin-bottom:6px;}
.nf-nl-left p{font-family:'Inter',sans-serif;font-size:13px;color:#888;font-weight:300;}
.nf-nl-form{display:flex;border:1.5px solid #E7E0D4;border-radius:50px;overflow:hidden;max-width:380px;width:100%;background:#fff;transition:border-color 0.3s;}
.nf-nl-form:focus-within{border-color:#C6A769;}
.nf-nl-inp{flex:1;border:none;padding:13px 22px;font-family:'Inter',sans-serif;font-size:13px;color:#1A1A1A;outline:none;background:none;}
.nf-nl-inp::placeholder{color:#BBBBBB;}
.nf-nl-btn{background:#1A1A1A;color:#fff;border:none;padding:13px 24px;font-family:'Inter',sans-serif;font-size:11px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;cursor:pointer;transition:background 0.2s;white-space:nowrap;border-radius:0 50px 50px 0;}
.nf-nl-btn:hover{background:#C6A769;}

/* TRUST BAR */
.nf-trust{background:linear-gradient(135deg,#1A1A1A,#2A2A2A);padding:32px 0;}
.nf-trust-inner{max-width:1200px;margin:0 auto;padding:0 40px;display:grid;grid-template-columns:repeat(5,1fr);gap:0;}
.nf-ti{display:flex;flex-direction:column;align-items:center;gap:10px;padding:0 20px;border-right:1px solid rgba(255,255,255,0.08);text-align:center;}
.nf-ti:last-child{border-right:none;}
.nf-ti-icon{width:44px;height:44px;background:rgba(198,167,105,0.12);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#C6A769;font-size:17px;transition:all 0.3s;}
.nf-ti:hover .nf-ti-icon{background:#C6A769;color:#fff;}
.nf-ti-label{font-family:'Inter',sans-serif;font-size:11px;font-weight:600;color:rgba(255,255,255,0.85);letter-spacing:0.06em;text-transform:uppercase;}
.nf-ti-sub{font-family:'Inter',sans-serif;font-size:10px;color:rgba(255,255,255,0.35);font-weight:300;}

/* MAP */
.nf-map{background:#EEEAE4;border-top:1px solid #E7E0D4;padding:0;}
.nf-map-hdr{max-width:1200px;margin:0 auto;padding:28px 40px 0;}
.nf-map-title{font-family:'Cormorant Garamond',serif;font-size:22px;font-weight:600;color:#1A1A1A;margin-bottom:4px;}
.nf-map-sub{font-family:'Inter',sans-serif;font-size:12px;color:#888;}
.nf-map-frame{width:100%;height:280px;border:0;display:block;margin-top:20px;filter:saturate(0.7) contrast(1.05);}

/* MAIN FOOTER GRID */
.nf-main{max-width:1200px;margin:0 auto;padding:56px 40px 40px;display:grid;grid-template-columns:2fr 1fr 1fr 1fr 1.5fr;gap:48px;border-bottom:1px solid #E7E0D4;}

/* BRAND COL */
.nf-brand-logo{font-family:'Cormorant Garamond',serif;font-size:28px;font-weight:700;color:#1A1A1A;letter-spacing:0.06em;line-height:1;}
.nf-brand-logo em{font-style:normal;color:#C6A769;}
.nf-brand-tag{font-family:'Inter',sans-serif;font-size:8px;letter-spacing:0.4em;color:#C6A769;text-transform:uppercase;font-weight:600;margin-top:3px;display:block;}
.nf-brand-desc{font-family:'Inter',sans-serif;font-size:13px;color:#888;font-weight:300;line-height:1.8;margin:18px 0 24px;}
.nf-socials{display:flex;gap:10px;}
.nf-social-btn{width:38px;height:38px;border-radius:50%;border:1.5px solid #E7E0D4;display:flex;align-items:center;justify-content:center;font-size:14px;text-decoration:none;color:#888;transition:all 0.3s;}
.nf-social-btn:hover{background:#1A1A1A;border-color:#1A1A1A;color:#fff;transform:translateY(-3px);}
.nf-social-btn.wa:hover{background:#25D366;border-color:#25D366;}
.nf-social-btn.ig:hover{background:linear-gradient(135deg,#f09433,#e6683c,#dc2743);border-color:#dc2743;}

/* LINK COLS */
.nf-col-title{font-family:'Inter',sans-serif;font-size:11px;font-weight:700;letter-spacing:0.16em;text-transform:uppercase;color:#1A1A1A;margin-bottom:18px;padding-bottom:12px;border-bottom:1px solid #E7E0D4;}
.nf-link{display:flex;align-items:center;gap:8px;font-family:'Inter',sans-serif;font-size:12.5px;color:#777;text-decoration:none;margin-bottom:9px;font-weight:400;transition:all 0.2s;}
.nf-link i{font-size:10px;color:#C6A769;width:14px;flex-shrink:0;}
.nf-link:hover{color:#1A1A1A;padding-left:4px;}

/* CONTACT COL */
.nf-contact-row{display:flex;align-items:flex-start;gap:12px;margin-bottom:14px;}
.nf-contact-icon{width:32px;height:32px;background:#fff;border:1px solid #E7E0D4;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#C6A769;font-size:13px;flex-shrink:0;}
.nf-contact-label{font-family:'Inter',sans-serif;font-size:11px;font-weight:600;color:#1A1A1A;margin-bottom:2px;}
.nf-contact-val{font-family:'Inter',sans-serif;font-size:12px;color:#888;font-weight:300;}
.nf-contact-val a{color:#C6A769;text-decoration:none;}
.nf-contact-val a:hover{text-decoration:underline;}

/* BOTTOM BAR */
.nf-bottom{max-width:1200px;margin:0 auto;padding:20px 40px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;}
.nf-copy{font-family:'Inter',sans-serif;font-size:12px;color:#AAAAAA;font-weight:300;}
.nf-badges{display:flex;gap:8px;}
.nf-badge{background:#fff;border:1px solid #E7E0D4;color:#888;font-family:'Inter',sans-serif;font-size:10px;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;padding:5px 12px;border-radius:20px;}

/* WhatsApp Floating */
.wa-float{position:fixed;bottom:24px;right:24px;z-index:9999;width:52px;height:52px;background:#25D366;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:24px;text-decoration:none;box-shadow:0 8px 24px rgba(37,211,102,0.4);transition:all 0.3s;}
.wa-float:hover{background:#1ebe5d;transform:scale(1.1);box-shadow:0 12px 36px rgba(37,211,102,0.5);}
.wa-float-tooltip{position:absolute;right:64px;top:50%;transform:translateY(-50%);background:#1A1A1A;color:#fff;font-family:'Inter',sans-serif;font-size:11px;font-weight:600;white-space:nowrap;padding:7px 14px;border-radius:6px;opacity:0;pointer-events:none;transition:opacity 0.3s;}
.wa-float:hover .wa-float-tooltip{opacity:1;}

@media(max-width:1024px){
  .nf-main{grid-template-columns:1fr 1fr;gap:32px;}
  .nf-trust-inner{grid-template-columns:repeat(3,1fr);}
}
</style>

<!-- NEWSLETTER -->
<div class="nf-nl">
  <div class="nf-nl-inner">
    <div class="nf-nl-left">
      <h4>Stay in the Loop</h4>
      <p>Exclusive offers, new arrivals & live gold rate alerts — delivered to you.</p>
    </div>
    <div class="nf-nl-form">
      <input type="email" class="nf-nl-inp" placeholder="Enter your email address...">
      <button class="nf-nl-btn" onclick="this.innerHTML='<i class=\'fa-solid fa-check\'></i> Done!';this.style.background='#2d7a3a';">Subscribe</button>
    </div>
  </div>
</div>

<!-- TRUST BAR -->
<div class="nf-trust">
  <div class="nf-trust-inner">
    <?php foreach([
      ['fa-certificate','BIS Hallmark','All gold certified'],
      ['fa-gem','IGI Certified','Genuine diamonds'],
      ['fa-truck','Free Delivery','Pan India shipping'],
      ['fa-rotate-left','7-Day Returns','Easy exchange'],
      ['fa-headset','24/7 Support','WhatsApp help'],
    ] as $t): ?>
    <div class="nf-ti">
      <div class="nf-ti-icon"><i class="fa-solid <?php echo $t[0]; ?>"></i></div>
      <div class="nf-ti-label"><?php echo $t[1]; ?></div>
      <div class="nf-ti-sub"><?php echo $t[2]; ?></div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- MAP -->
<div class="nf-map">
  <div class="nf-map-hdr">
    <div class="nf-map-title"><i class="fa-solid fa-location-dot" style="color:#C6A769;margin-right:8px;"></i>Visit Our Store</div>
    <div class="nf-map-sub">Netanis Jewelos · Sardar Market, Clock Tower, Jodhpur · Mon–Sat 10am–8pm</div>
  </div>
  <iframe class="nf-map-frame" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"
    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14388.85326516!2d73.0197193!3d26.2388826!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39418c47d9f4f035%3A0xd573779f0f58c40a!2sJodhpur%2C%20Rajasthan!5e0!3m2!1sen!2sin!4v1700000000000">
  </iframe>
</div>

<!-- MAIN FOOTER -->
<div class="nf">
  <div class="nf-main">
    <!-- BRAND -->
    <div>
      <img src="assets/uploads/logo.png" alt="Netanis Jewelos" style="height:56px;width:auto;object-fit:contain;filter:brightness(0.9);">
      <p class="nf-brand-desc">Crafting absolute heritage since 2020 — every piece at Netanis Jewelos is born from BIS-hallmarked gold and IGI-certified diamonds, with live market pricing always transparent.</p>
      <div class="nf-socials">
        <a href="#" class="nf-social-btn ig" title="Instagram"><i class="fa-brands fa-instagram"></i></a>
        <a href="https://wa.me/919321387018" class="nf-social-btn wa" title="WhatsApp" target="_blank"><i class="fa-brands fa-whatsapp"></i></a>
        <a href="#" class="nf-social-btn" title="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
        <a href="#" class="nf-social-btn" title="YouTube"><i class="fa-brands fa-youtube"></i></a>
      </div>
    </div>

    <!-- SHOP -->
    <div>
      <div class="nf-col-title">Shop</div>
      <a href="shop.php?cat=Rings" class="nf-link"><i class="fa-solid fa-ring"></i>Gold Rings</a>
      <a href="shop.php?cat=Earrings" class="nf-link"><i class="fa-solid fa-droplet"></i>Earrings</a>
      <a href="shop.php?cat=Necklaces" class="nf-link"><i class="fa-solid fa-link"></i>Necklaces</a>
      <a href="shop.php?cat=Bangles" class="nf-link"><i class="fa-solid fa-circle-notch"></i>Bangles</a>
      <a href="shop.php?has_diamond=1" class="nf-link"><i class="fa-solid fa-gem"></i>Diamond Jewellery</a>
      <a href="shop.php?collection=Bridal" class="nf-link"><i class="fa-solid fa-crown"></i>Bridal Collection</a>
      <a href="shop.php?cat=Anklets" class="nf-link"><i class="fa-solid fa-circle-notch"></i>Anklets</a>
      <a href="shop.php?sub=NazarBattu" class="nf-link"><i class="fa-solid fa-eye"></i>Nazar Battu</a>
    </div>

    <!-- INFO -->
    <div>
      <div class="nf-col-title">Information</div>
      <a href="about-us.php" class="nf-link"><i class="fa-solid fa-book-open"></i>Our Story</a>
      <a href="#" class="nf-link"><i class="fa-solid fa-chart-line"></i>Gold Rate Today</a>
      <a href="#" class="nf-link"><i class="fa-solid fa-shield-halved"></i>Hallmark Info</a>
      <a href="#" class="nf-link"><i class="fa-solid fa-sparkles"></i>Jewellery Care</a>
      <a href="#" class="nf-link"><i class="fa-solid fa-file-contract"></i>Privacy Policy</a>
      <a href="#" class="nf-link"><i class="fa-solid fa-file-lines"></i>Terms & Conditions</a>
      <a href="#" class="nf-link"><i class="fa-solid fa-truck"></i>Shipping Policy</a>
      <a href="#" class="nf-link"><i class="fa-solid fa-rotate-left"></i>Return Policy</a>
    </div>

    <!-- ACCOUNT -->
    <div>
      <div class="nf-col-title">Account</div>
      <a href="login.php" class="nf-link"><i class="fa-solid fa-right-to-bracket"></i>Login / Register</a>
      <a href="dashboard.php" class="nf-link"><i class="fa-solid fa-box-open"></i>My Orders</a>
      <a href="wishlist.php" class="nf-link"><i class="fa-regular fa-heart"></i>Wishlist</a>
      <a href="cart.php" class="nf-link"><i class="fa-solid fa-bag-shopping"></i>Shopping Bag</a>
      <div class="nf-col-title" style="margin-top:20px;">Quick</div>
      <a href="shop.php?purity=22Kt" class="nf-link"><i class="fa-solid fa-circle-dot"></i>22Kt Hallmark Gold</a>
      <a href="shop.php?metal=Silver" class="nf-link"><i class="fa-solid fa-circle-half-stroke"></i>Silver Jewellery</a>
    </div>

    <!-- CONTACT -->
    <div>
      <div class="nf-col-title">Get in Touch</div>
      <div class="nf-contact-row">
        <div class="nf-contact-icon"><i class="fa-solid fa-location-dot"></i></div>
        <div>
          <div class="nf-contact-label">Our Store</div>
          <div class="nf-contact-val">Sardar Market, Clock Tower<br>Jodhpur, Rajasthan – 342001</div>
        </div>
      </div>
      <div class="nf-contact-row">
        <div class="nf-contact-icon"><i class="fa-solid fa-phone"></i></div>
        <div>
          <div class="nf-contact-label">Phone</div>
          <div class="nf-contact-val"><a href="tel:+918147349242">+91-814-734-9242</a><br>Mon–Sat · 10am to 8pm</div>
        </div>
      </div>
      <div class="nf-contact-row">
        <div class="nf-contact-icon" style="color:#25D366;"><i class="fa-brands fa-whatsapp"></i></div>
        <div>
          <div class="nf-contact-label">WhatsApp</div>
          <div class="nf-contact-val"><a href="https://wa.me/918147349242">Chat with us instantly</a></div>
        </div>
      </div>
      <div class="nf-contact-row">
        <div class="nf-contact-icon"><i class="fa-solid fa-envelope"></i></div>
        <div>
          <div class="nf-contact-label">Email</div>
          <div class="nf-contact-val"><a href="mailto:orders@netanisjewelos.com">orders@netanisjewelos.com</a></div>
        </div>
      </div>
    </div>
  </div>

  <!-- BOTTOM -->
  <div class="nf-bottom">
    <div class="nf-copy">© <?php echo date('Y'); ?> Netanis Jewelos. All rights reserved. Crafted with <i class="fa-solid fa-heart fa-xs" style="color:#C6A769;"></i> in Jodhpur, Rajasthan.</div>
    <div class="nf-badges">
      <span class="nf-badge"><i class="fa-solid fa-certificate fa-xs" style="color:#C6A769;margin-right:4px;"></i>BIS Hallmark</span>
      <span class="nf-badge"><i class="fa-solid fa-gem fa-xs" style="color:#C6A769;margin-right:4px;"></i>IGI Certified</span>
      <span class="nf-badge"><i class="fa-solid fa-flag fa-xs" style="color:#C6A769;margin-right:4px;"></i>Made in India</span>
    </div>
  </div>
</div>

<!-- WhatsApp Floating Button -->
<a href="https://wa.me/919321387018" class="wa-float" target="_blank">
  <i class="fa-brands fa-whatsapp"></i>
  <span class="wa-float-tooltip">Chat on WhatsApp</span>
</a>
