<?php
// ============================================================
// EMAIL CONFIGURATION — Edit these before going live
// ============================================================

define('MAIL_FROM_EMAIL', 'orders@netanisjewelos.com');
define('MAIL_FROM_NAME', 'Netanis Jewelos');
define('OWNER_EMAIL', 'owner@netanisjewelos.com'); // Owner gets order notification here
define('OWNER_NAME', 'Netanis Admin');
define('STORE_NAME', 'Netanis Jewelos');
define('STORE_PHONE', '+91-8147349242');
define('STORE_WHATSAPP', '918147349242'); // Without + or spaces
define('STORE_ADDRESS', 'Jodhpur, Rajasthan, India');

// If using SMTP (recommended for production), install PHPMailer via composer
// For XAMPP local: PHP mail() function will work via Mailtrap or similar
// Set USE_SMTP = true and fill SMTP settings for production
define('USE_SMTP', false);
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your@gmail.com');
define('SMTP_PASS', 'your_app_password');
?>
