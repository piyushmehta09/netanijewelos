======================================================
  NETANIS JEWELOS - Premium Jewellery Web App
  Setup Guide
======================================================

REQUIREMENTS:
- XAMPP / WAMP / LAMP (PHP 8.0+, MySQL 5.7+)
- Place this folder in: htdocs/premium-jewellery/

STEP 1 — DATABASE SETUP:
  1. Open phpMyAdmin
  2. Click "Import"
  3. Select: database.sql
  4. Click Go

STEP 2 — CONFIG (if needed):
  Edit: config/db.php
  Change password if MySQL has one (default is empty for XAMPP)

STEP 3 — OPEN IN BROWSER:
  Client: http://localhost/premium-jewellery/client/index.php
  Admin:  http://localhost/premium-jewellery/admin/index.php

DEFAULT LOGIN CREDENTIALS:
  Admin: admin@netanis.com / password
  (Change this in phpMyAdmin after setup!)

FEATURES:
  ✅ Live gold rate ticker in header
  ✅ Dynamic price calculation (metal + making + GST)
  ✅ Product catalogue with filters (category, purity, metal)
  ✅ Live search with suggestions
  ✅ Shopping cart
  ✅ Wishlist (login required)
  ✅ Checkout with shipping address saved
  ✅ Order tracking with progress bar
  ✅ WhatsApp invoice notification
  ✅ Customer dashboard
  ✅ Admin panel: Products, Orders, Gold Rate Manager
  ✅ Login / Register / Logout

======================================================
