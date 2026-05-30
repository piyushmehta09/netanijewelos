<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | Netanis Jewelos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } .font-serif { font-family: 'Playfair Display', serif; } </style>
</head>
<body class="bg-[#fcfbfa] text-stone-800 flex flex-col min-h-screen justify-center">

    <div class="max-w-md mx-auto px-6 py-16 w-full">
        <div class="text-center mb-8">
            <a href="index.php" class="text-3xl font-serif tracking-widest text-[#832729] font-bold uppercase block">Netanis Jewelos</a>
            <p class="text-stone-400 text-xs tracking-wider uppercase font-light mt-1">Create your Luxury Account</p>
        </div>

        <?php if(isset($_GET['error'])): ?>
            <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg mb-6 text-xs shadow-sm"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <div class="bg-white border border-stone-200/60 rounded-xl p-8 shadow-[0_4px_25px_rgba(0,0,0,0.02)]">
            <form action="../api/register-handler.php" method="POST" class="space-y-5 text-xs">
                <div>
                    <label class="block text-stone-500 font-medium mb-1.5 uppercase tracking-wider text-[10px]">Full Name</label>
                    <input type="text" name="full_name" required placeholder="e.g., Piyush Mehta" class="w-full bg-stone-50/50 border border-stone-200 focus:border-[#832729] rounded-lg px-4 py-3 text-stone-800 focus:outline-none text-sm font-light">
                </div>
                <div>
                    <label class="block text-stone-500 font-medium mb-1.5 uppercase tracking-wider text-[10px]">Email Address</label>
                    <input type="email" name="email" required placeholder="e.g., mail@example.com" class="w-full bg-stone-50/50 border border-stone-200 focus:border-[#832729] rounded-lg px-4 py-3 text-stone-800 focus:outline-none text-sm font-mono tracking-wide">
                </div>
                <div>
                    <label class="block text-stone-500 font-medium mb-1.5 uppercase tracking-wider text-[10px]">Mobile Number</label>
                    <input type="tel" name="phone" placeholder="e.g., +91 98765 43210" class="w-full bg-stone-50/50 border border-stone-200 focus:border-[#832729] rounded-lg px-4 py-3 text-stone-800 focus:outline-none text-sm font-mono tracking-wide">
                </div>
                <div>
                    <label class="block text-stone-500 font-medium mb-1.5 uppercase tracking-wider text-[10px]">Password</label>
                    <input type="password" name="password" required placeholder="••••••••••••" class="w-full bg-stone-50/50 border border-stone-200 focus:border-[#832729] rounded-lg px-4 py-3 text-stone-800 focus:outline-none text-sm font-mono tracking-widest">
                </div>
                <button type="submit" class="w-full bg-[#832729] hover:bg-[#6b2022] text-white font-medium tracking-widest py-3.5 rounded-lg transition-colors uppercase text-xs shadow-sm mt-2 font-semibold">Create Account</button>
            </form>
            <div class="text-center text-stone-400 mt-6 text-[11px] font-light">
                Already have an account? <a href="login.php" class="text-[#832729] font-medium hover:underline">Log In</a>
            </div>
        </div>
    </div>

</body>
</html>
