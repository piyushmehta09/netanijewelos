<?php
function renderSidebar($activePage) {
    $pages = [
        'dashboard' => ['title' => 'Dashboard', 'icon' => '📊', 'url' => 'index.php'],
        'gold_rate' => ['title' => 'Gold Rate Manager', 'icon' => '💰', 'url' => 'update-gold-rate.php'],
        'products' => ['title' => 'Product Vault', 'icon' => '💍', 'url' => 'manage-products.php'],
        'orders_dashboard_token' => ['title' => 'Orders Queue', 'icon' => '📦', 'url' => 'orders.php'],
    ];
    
    $adminName = $_SESSION['user_name'] ?? 'Admin';
    
    echo '<div class="w-64 bg-white text-stone-800 min-h-screen flex flex-col justify-between border-r border-stone-200 shadow-sm shrink-0">';
    echo '  <div>';
    echo '    <div class="p-6 border-b border-stone-100">';
    echo '      <div class="text-xl font-serif font-bold tracking-widest text-[#832729] uppercase">Netanis</div>';
    echo '      <div class="text-[9px] tracking-widest text-stone-400 font-sans uppercase font-semibold mt-0.5">Admin Control Panel</div>';
    echo '    </div>';
    echo '    <div class="p-4 border-b border-stone-100 flex items-center space-x-3">';
    echo '      <div class="w-8 h-8 bg-[#832729]/10 text-[#832729] rounded-full flex items-center justify-center font-bold text-xs uppercase">' . substr($adminName, 0, 1) . '</div>';
    echo '      <div>';
    echo '        <div class="text-xs font-semibold text-stone-800">' . htmlspecialchars($adminName) . '</div>';
    echo '        <div class="text-[9px] text-stone-400 uppercase tracking-wider">' . ($_SESSION['user_role'] ?? 'Admin') . '</div>';
    echo '      </div>';
    echo '    </div>';
    echo '    <nav class="p-4 space-y-1">';
    
    foreach ($pages as $key => $data) {
        $activeClass = ($activePage === $key) 
            ? 'bg-[#832729]/5 text-[#832729] border-l-2 border-[#832729] font-semibold' 
            : 'text-stone-500 hover:bg-stone-50 hover:text-stone-900 font-light';
        echo "<a href='{$data['url']}' class='flex items-center space-x-3 px-4 py-2.5 text-xs tracking-wide transition-all rounded-lg {$activeClass}'>";
        echo "<span>{$data['icon']}</span>";
        echo "<span>{$data['title']}</span>";
        echo "</a>";
    }
    
    echo '    </nav>';
    echo '  </div>';
    echo '  <div class="p-4 border-t border-stone-100 space-y-2">';
    echo '    <a href="../client/index.php" class="flex items-center space-x-3 px-4 py-2.5 text-xs text-stone-500 hover:bg-stone-50 rounded-lg transition-colors">';
    echo '      <span>🌐</span><span>View Store</span>';
    echo '    </a>';
    echo '    <a href="../api/logout.php" class="flex items-center space-x-3 px-4 py-2.5 text-xs text-red-500 hover:bg-red-50 rounded-lg transition-colors">';
    echo '      <span>🚪</span><span>Logout</span>';
    echo '    </a>';
    echo '    <div class="text-[9px] text-stone-400 font-mono tracking-widest text-center uppercase pt-2">v2.0 © 2026 Netanis</div>';
    echo '  </div>';
    echo '</div>';
}
?>
