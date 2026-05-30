<?php
require_once '../config/db.php';
require_once '../config/mail-config.php';

/**
 * Main notification sender — called after order is placed
 * Sends:
 *   1. Customer: HTML Email receipt
 *   2. Owner: HTML Email new order alert
 * WhatsApp invoice link is generated client-side (browser opens wa.me link)
 */
function sendOrderNotifications($orderId, $pdo) {
    // Fetch order details
    $stmt = $pdo->prepare("SELECT o.*, u.full_name, u.email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();
    if (!$order) return false;

    // Fetch order items
    $items = $pdo->prepare("SELECT oi.*, p.product_name, p.sku, p.purity, p.weight_grams FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
    $items->execute([$orderId]);
    $orderItems = $items->fetchAll();

    $itemsHtml = '';
    $itemsText = '';
    foreach ($orderItems as $oi) {
        $itemsHtml .= "<tr>
            <td style='padding:10px;border-bottom:1px solid #f0ebe4;'>{$oi['product_name']}<br><small style='color:#8c7b6b;'>{$oi['purity']} · SKU: {$oi['sku']}</small></td>
            <td style='padding:10px;border-bottom:1px solid #f0ebe4;text-align:center;'>{$oi['quantity']}</td>
            <td style='padding:10px;border-bottom:1px solid #f0ebe4;text-align:right;font-weight:600;'>₹" . number_format($oi['price_at_purchase'] * $oi['quantity']) . "</td>
        </tr>";
        $itemsText .= "• {$oi['product_name']} (Qty: {$oi['quantity']}) — ₹" . number_format($oi['price_at_purchase'] * $oi['quantity']) . "\n";
    }

    $orderRef = '#NETANIS-' . str_pad($orderId, 5, '0', STR_PAD_LEFT);
    $shippingAddr = implode(', ', array_filter([
        $order['shipping_address'],
        $order['shipping_city'],
        $order['shipping_state'],
        $order['shipping_pincode']
    ]));
    $customerName = $order['shipping_name'] ?: $order['full_name'];
    $orderDate = date('d M Y, h:i A', strtotime($order['created_at']));

    // ---- CUSTOMER EMAIL ----
    $customerEmailHtml = buildCustomerEmail($orderRef, $customerName, $orderDate, $itemsHtml, $order, $shippingAddr);
    $customerSubject = "✅ Order Confirmed — {$orderRef} | " . STORE_NAME;
    sendMail($order['email'], $customerName, $customerSubject, $customerEmailHtml);

    // ---- OWNER EMAIL ----
    $ownerEmailHtml = buildOwnerEmail($orderRef, $customerName, $order, $orderDate, $itemsHtml, $shippingAddr, $itemsText);
    $ownerSubject = "🔔 NEW ORDER: {$orderRef} — ₹" . number_format($order['total_amount']);
    sendMail(OWNER_EMAIL, OWNER_NAME, $ownerSubject, $ownerEmailHtml);

    // Mark as notified
    $pdo->prepare("UPDATE orders SET owner_notified = 1 WHERE id = ?")->execute([$orderId]);
    return true;
}

function buildCustomerEmail($ref, $name, $date, $itemsHtml, $order, $addr) {
    return "<!DOCTYPE html><html><head><meta charset='UTF-8'>
    <style>
      body{font-family:'DM Sans',Arial,sans-serif;background:#faf9f7;color:#2d2926;margin:0;padding:0;}
      .wrapper{max-width:580px;margin:0 auto;background:#fff;border:1px solid #e8e0d5;}
      .header{background:#2d2926;padding:30px;text-align:center;}
      .header h1{font-family:Georgia,serif;color:#d4a82a;font-size:26px;margin:0;letter-spacing:2px;}
      .header p{color:#8c7b6b;font-size:11px;margin:6px 0 0;letter-spacing:3px;text-transform:uppercase;}
      .body{padding:32px;}
      .success-box{background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:18px;text-align:center;margin-bottom:24px;}
      .success-box .check{font-size:32px;}
      .success-box p{color:#166534;font-size:14px;margin:8px 0 0;font-weight:600;}
      .info-table{width:100%;border-collapse:collapse;margin:20px 0;font-size:12px;}
      .info-table td{padding:8px 0;border-bottom:1px solid #f0ebe4;}
      .info-table td:first-child{color:#8c7b6b;width:40%;}
      .info-table td:last-child{font-weight:600;}
      .items-table{width:100%;border-collapse:collapse;margin:20px 0;font-size:13px;}
      .items-table th{background:#f5efe7;padding:10px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:1px;color:#8c7b6b;}
      .total-row{background:#2d2926;color:white;padding:14px;}
      .footer{background:#f5efe7;padding:20px;text-align:center;font-size:11px;color:#8c7b6b;}
      .btn{background:#b8860b;color:white;padding:12px 28px;border-radius:6px;text-decoration:none;font-weight:600;font-size:12px;display:inline-block;letter-spacing:1px;text-transform:uppercase;}
    </style></head><body>
    <div class='wrapper'>
      <div class='header'><h1>" . STORE_NAME . "</h1><p>Order Confirmation</p></div>
      <div class='body'>
        <div class='success-box'><div class='check'>✅</div><p>Order Confirmed! Your masterpiece is being prepared.</p></div>
        <p style='font-size:15px;'>Dear <strong>{$name}</strong>,</p>
        <p style='color:#5c5047;font-size:13px;line-height:1.7;'>Thank you for shopping with " . STORE_NAME . ". We've received your order and our artisans are carefully preparing your jewellery.</p>
        <table class='info-table'>
          <tr><td>Order Reference</td><td style='color:#b8860b;font-size:14px;'>{$ref}</td></tr>
          <tr><td>Order Date</td><td>{$date}</td></tr>
          <tr><td>Delivery Address</td><td>{$addr}</td></tr>
          <tr><td>Payment Status</td><td><span style='color:#166534;'>COD / Pending</span></td></tr>
        </table>
        <h3 style='font-family:Georgia,serif;color:#2d2926;margin:24px 0 8px;font-size:16px;'>Order Summary</h3>
        <table class='items-table'>
          <thead><tr><th>Item</th><th style='text-align:center;'>Qty</th><th style='text-align:right;'>Price</th></tr></thead>
          <tbody>{$itemsHtml}</tbody>
        </table>
        <table style='width:100%;font-size:13px;'>
          <tr><td style='color:#8c7b6b;'>GST Amount (3%)</td><td style='text-align:right;'>₹" . number_format($order['gst_amount']) . "</td></tr>
        </table>
        <div class='total-row' style='border-radius:8px;margin-top:8px;'>
          <table style='width:100%;'><tr>
            <td style='color:#d4a82a;font-weight:700;font-size:14px;'>TOTAL PAYABLE</td>
            <td style='text-align:right;color:white;font-size:20px;font-weight:700;font-family:Georgia,serif;'>₹" . number_format($order['total_amount']) . "</td>
          </tr></table>
        </div>
        <div style='text-align:center;margin-top:28px;'>
          <a href='http://localhost/premium-jewellery/client/dashboard.php' class='btn'>Track My Order</a>
        </div>
        <hr style='border:none;border-top:1px solid #e8e0d5;margin:28px 0;'>
        <div style='font-size:11px;color:#8c7b6b;text-align:center;line-height:1.9;'>
          <p>Need help? Contact us at <strong>" . STORE_WHATSAPP . "</strong> via WhatsApp</p>
          <p style='color:#8c7b6b;'>" . STORE_ADDRESS . " | " . STORE_PHONE . "</p>
        </div>
      </div>
      <div class='footer'>© 2026 " . STORE_NAME . " | BIS Hallmark Certified | 7-Day Return Policy</div>
    </div></body></html>";
}

function buildOwnerEmail($ref, $customerName, $order, $date, $itemsHtml, $addr, $itemsText) {
    return "<!DOCTYPE html><html><head><meta charset='UTF-8'>
    <style>
      body{font-family:Arial,sans-serif;background:#f0f0f0;margin:0;padding:20px;}
      .wrapper{max-width:600px;margin:0 auto;background:#fff;border-radius:10px;overflow:hidden;border:1px solid #e8e0d5;}
      .alert-header{background:#b8860b;padding:24px;text-align:center;}
      .alert-header h1{color:white;font-size:22px;margin:0;}
      .alert-header p{color:rgba(255,255,255,0.8);font-size:12px;margin:6px 0 0;}
      .body{padding:28px;}
      .kv-table{width:100%;font-size:13px;border-collapse:collapse;margin:16px 0;}
      .kv-table tr td{padding:8px 0;border-bottom:1px solid #f5f5f5;}
      .kv-table tr td:first-child{color:#666;width:38%;}
      .kv-table tr td:last-child{font-weight:600;}
      .items-table{width:100%;border-collapse:collapse;margin:16px 0;font-size:13px;}
      .items-table th{background:#2d2926;color:white;padding:10px;text-align:left;font-size:11px;}
      .amount-box{background:#2d2926;color:white;padding:16px;border-radius:8px;text-align:right;margin:16px 0;}
      .action-btn{background:#2d2926;color:white;padding:12px 24px;border-radius:6px;text-decoration:none;font-weight:600;font-size:12px;display:inline-block;}
    </style></head><body>
    <div class='wrapper'>
      <div class='alert-header'><h1>🔔 New Order Received!</h1><p>" . STORE_NAME . " — Admin Notification</p></div>
      <div class='body'>
        <p style='font-size:15px;margin:0 0 16px;'>A new order has been placed. Please review and confirm:</p>
        <table class='kv-table'>
          <tr><td>Order Reference</td><td style='color:#b8860b;font-size:15px;'>{$ref}</td></tr>
          <tr><td>Customer Name</td><td>{$customerName}</td></tr>
          <tr><td>WhatsApp/Phone</td><td style='color:#059669;'>{$order['shipping_phone']}</td></tr>
          <tr><td>Order Date & Time</td><td>{$date}</td></tr>
          <tr><td>Delivery Address</td><td>{$addr}</td></tr>
        </table>
        <h3 style='font-size:14px;color:#2d2926;margin:20px 0 8px;'>Items Ordered</h3>
        <table class='items-table'>
          <thead><tr><th>Product</th><th style='text-align:center;'>Qty</th><th style='text-align:right;'>Price</th></tr></thead>
          <tbody>{$itemsHtml}</tbody>
        </table>
        <div class='amount-box'>
          <div style='color:#d4a82a;font-size:12px;margin-bottom:4px;'>TOTAL ORDER VALUE</div>
          <div style='font-size:28px;font-weight:700;'>₹" . number_format($order['total_amount']) . "</div>
          <div style='color:rgba(255,255,255,0.6);font-size:11px;'>Incl. GST ₹" . number_format($order['gst_amount']) . "</div>
        </div>
        <div style='text-align:center;margin-top:24px;'>
          <a href='http://localhost/premium-jewellery/admin/orders.php' class='action-btn'>View in Admin Panel →</a>
        </div>
        <div style='margin-top:20px;background:#fff8e7;border:1px solid #fde68a;border-radius:8px;padding:12px;font-size:12px;color:#92400e;'>
          ⚡ <strong>Action Required:</strong> Confirm this order in the admin panel and contact the customer to arrange delivery.
        </div>
      </div>
    </div></body></html>";
}

function sendMail($toEmail, $toName, $subject, $htmlBody) {
    if(!USE_SMTP) {
        // PHP mail() — works on localhost with Mailtrap or local SMTP
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM_EMAIL . ">\r\n";
        $headers .= "Reply-To: " . MAIL_FROM_EMAIL . "\r\n";
        @mail($toEmail, $subject, $htmlBody, $headers);
    }
    // For SMTP: integrate PHPMailer here
    // require_once '../vendor/autoload.php';
    // $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    // ... configure SMTP and send
}
?>
