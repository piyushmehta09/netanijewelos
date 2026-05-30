<?php
require_once '../config/db.php';
require_once 'components/sidebar.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("INSERT INTO gold_rates (rate_24kt, rate_22kt, rate_18kt, rate_14kt, silver_rate) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['rate_24kt'],
            $_POST['rate_22kt'],
            $_POST['rate_18kt'],
            $_POST['rate_14kt'],
            $_POST['silver_rate']
        ]);
        $message = "<div class='bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg mb-6 text-xs shadow-sm'>✨ System Matrix Updated: All consumer pricing modules synchronized instantly.</div>";
    } catch (\PDOException $e) {
        $message = "<div class='bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg mb-6 text-xs shadow-sm'>🚨 Database Parameter Error: " . $e->getMessage() . "</div>";
    }
}
$latestRate = $pdo->query("SELECT * FROM gold_rates ORDER BY id DESC LIMIT 1")->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bullion Settings | Netanis Control Engine</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } .font-serif { font-family: 'Playfair Display', serif; } </style>
</head>
<body class="bg-[#fcfbfa] text-stone-800 flex min-h-screen">

    <?php renderSidebar('gold_rate'); ?>

    <div class="flex-1 p-12 overflow-y-auto">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-serif tracking-wide text-[#832729] font-semibold mb-1">Bullion Matrix Parameters</h1>
            <p class="text-stone-500 text-xs font-light mb-8 uppercase tracking-widest font-sans">Configure current global retail asset evaluations</p>

            <?php echo $message; ?>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white border border-stone-200/60 p-6 rounded-xl shadow-sm md:col-span-2">
                    <h2 class="text-sm font-serif font-semibold text-stone-800 mb-5 border-b border-stone-100 pb-2">Set Daily Values (INR Per Gram)</h2>
                    <form action="update-gold-rate.php" method="POST" class="space-y-4 text-xs">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-stone-500 font-medium mb-1 uppercase tracking-wider text-[10px]">Gold 24Kt</label>
                                <input type="number" step="0.01" name="rate_24kt" value="<?php echo $latestRate['rate_24kt'] ?? ''; ?>" required class="w-full bg-stone-50 border border-stone-200 rounded-lg px-3 py-2.5 text-stone-800 focus:outline-none focus:border-[#832729] font-mono">
                            </div>
                            <div>
                                <label class="block text-stone-500 font-medium mb-1 uppercase tracking-wider text-[10px]">Gold 22Kt</label>
                                <input type="number" step="0.01" name="rate_22kt" value="<?php echo $latestRate['rate_22kt'] ?? ''; ?>" required class="w-full bg-stone-50 border border-stone-200 rounded-lg px-3 py-2.5 text-stone-800 focus:outline-none focus:border-[#832729] font-mono">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-stone-500 font-medium mb-1 uppercase tracking-wider text-[10px]">Gold 18Kt</label>
                                <input type="number" step="0.01" name="rate_18kt" value="<?php echo $latestRate['rate_18kt'] ?? ''; ?>" required class="w-full bg-stone-50 border border-stone-200 rounded-lg px-3 py-2.5 text-stone-800 focus:outline-none focus:border-[#832729] font-mono">
                            </div>
                            <div>
                                <label class="block text-stone-500 font-medium mb-1 uppercase tracking-wider text-[10px]">Gold 14Kt</label>
                                <input type="number" step="0.01" name="rate_14kt" value="<?php echo $latestRate['rate_14kt'] ?? ''; ?>" required class="w-full bg-stone-50 border border-stone-200 rounded-lg px-3 py-2.5 text-stone-800 focus:outline-none focus:border-[#832729] font-mono">
                            </div>
                        </div>
                        <div>
                            <label class="block text-stone-500 font-medium mb-1 uppercase tracking-wider text-[10px]">Pure Silver Rate</label>
                            <input type="number" step="0.01" name="silver_rate" value="<?php echo $latestRate['silver_rate'] ?? ''; ?>" required class="w-full bg-stone-50 border border-stone-200 rounded-lg px-3 py-2.5 text-stone-800 focus:outline-none focus:border-[#832729] font-mono">
                        </div>
                        <button type="submit" class="w-full bg-[#832729] hover:bg-[#6b2022] text-white font-medium uppercase tracking-widest py-3 rounded-lg transition-colors shadow-sm font-semibold">Publish Active Tickers</button>
                    </form>
                </div>

                <div class="bg-white border border-stone-200/60 p-5 rounded-xl shadow-sm h-fit">
                    <h2 class="text-xs uppercase tracking-widest text-stone-400 font-mono border-b border-stone-100 pb-2 mb-3">Live Active Core</h2>
                    <?php if ($latestRate): ?>
                        <div class="space-y-2.5 font-mono text-[11px]">
                            <div class="flex justify-between border-b border-stone-100 pb-1.5"><span class="text-stone-400">Au 24Kt:</span> <span class="text-stone-800 font-bold">₹<?php echo number_format($latestRate['rate_24kt']); ?></span></div>
                            <div class="flex justify-between border-b border-stone-100 pb-1.5"><span class="text-stone-400">Au 22Kt:</span> <span class="text-stone-800 font-bold">₹<?php echo number_format($latestRate['rate_22kt']); ?></span></div>
                            <div class="flex justify-between border-b border-stone-100 pb-1.5"><span class="text-stone-400">Ag Silver:</span> <span class="text-stone-800 font-bold">₹<?php echo number_format($latestRate['silver_rate']); ?></span></div>
                        </div>
                        <div class="text-[9px] text-stone-400 mt-5 text-center leading-relaxed">Last Synchronized Timestamp: <br><?php echo $latestRate['updated_at']; ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</body>
</html>