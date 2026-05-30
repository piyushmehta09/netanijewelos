<?php require_once 'components/auth-guard.php'; ?>
<?php
require_once '../config/db.php';
require_once 'components/sidebar.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $imagePath = "";
        
        // Premium File Handling Matrix
        if (isset($_FILES['primary_image_file']) && $_FILES['primary_image_file']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['primary_image_file']['tmp_name'];
            $fileName = $_FILES['primary_image_file']['name'];
            
            // Clean extension extraction
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $newFileName = 'tanishq_' . time() . '_' . rand(1000, 9999) . '.' . $fileExtension;
            
            // Upload target location mapping
            $uploadFileDir = '../client/assets/uploads/';
            
            // Auto-create directory structure if missing
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true);
            }
            
            $dest_path = $uploadFileDir . $newFileName;
            
            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                // Client relative directory trajectory string to store in DB
                $imagePath = 'assets/uploads/' . $newFileName;
            } else {
                throw new Exception("File movement cluster allocation failure.");
            }
        }

        $stmt = $pdo->prepare("INSERT INTO products (product_name, sku, short_description, full_description, category, subcategory, collection_tag, metal_type, purity, weight_grams, making_charges_per_gram, discount_percent, primary_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $_POST['product_name'],
            $_POST['sku'],
            $_POST['short_description'],
            $_POST['full_description'],
            $_POST['category'],
            $_POST['subcategory'],
            $_POST['collection_tag'],
            $_POST['metal_type'],
            $_POST['purity'],
            $_POST['weight_grams'],
            $_POST['making_charges_per_gram'],
            $_POST['discount_percent'],
            $imagePath // Storing local file relative location
        ]);
        
        $message = "<div class='bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg mb-6 text-xs flex justify-between items-center shadow-sm font-sans'>
                        <span>✨ Success! New jewellery masterpiece has been registered into the secure vault with native media assets.</span>
                        <a href='manage-products.php' class='bg-[#832729] text-white px-3 py-1.5 rounded text-[11px] font-medium tracking-wider hover:bg-[#6b2022] transition-colors'>View Vault Inventory</a>
                    </div>";
    } catch (Exception $e) {
        $message = "<div class='bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg mb-6 text-xs shadow-sm font-sans'>🚨 Upload Engine Interrupted - " . $e->getMessage() . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forge New Product - Tanishq Control Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-serif { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="bg-[#fcfbfa] text-stone-800 flex min-h-screen">

    <?php renderSidebar('products'); ?>

    <div class="flex-1 p-12 overflow-y-auto">
        <div class="max-w-4xl mx-auto">
            
            <div class="flex items-center space-x-2 text-[11px] uppercase tracking-widest text-stone-400 mb-3 font-mono">
                <a href="manage-products.php" class="hover:text-[#832729] transition-colors">Vault Inventory</a>
                <span>/</span>
                <span class="text-stone-600 font-medium">Forge New Product</span>
            </div>

            <h1 class="text-3xl font-serif tracking-wide text-[#832729] font-semibold mb-2">Forge New Masterpiece</h1>
            <p class="text-stone-500 text-sm mb-10 font-light leading-relaxed">Input product definitions and metallurgical specs to instantly update the customer-side dynamic pricing catalogs.</p>

            <?php echo $message; ?>

            <div class="bg-white border border-stone-200/60 rounded-xl p-8 shadow-[0_4px_20px_rgba(0,0,0,0.02)]">
                <form action="add-product.php" method="POST" enctype="multipart/form-data" class="space-y-8 text-xs">
                    
                    <div>
                        <h3 class="text-[#832729] font-serif text-sm font-semibold mb-4 tracking-wide border-b border-stone-100 pb-2">1. Visual Catalog Identity</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-stone-500 font-medium mb-1.5 uppercase tracking-wider text-[10px]">Product Name</label>
                                <input type="text" name="product_name" placeholder="e.g., Bloom Bud Gold Ring" required class="w-full bg-stone-50/50 border border-stone-200 focus:border-[#832729] rounded-lg px-4 py-3 text-stone-800 focus:outline-none text-sm font-light transition-all placeholder-stone-300">
                            </div>
                            <div>
                                <label class="block text-stone-500 font-medium mb-1.5 uppercase tracking-wider text-[10px]">Unique SKU Code</label>
                                <input type="text" name="sku" placeholder="e.g., T-RNG-2026-001" required class="w-full bg-stone-50/50 border border-stone-200 focus:border-[#832729] rounded-lg px-4 py-3 text-stone-800 focus:outline-none text-sm font-mono tracking-wider transition-all placeholder-stone-300">
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-[#832729] font-serif text-sm font-semibold mb-4 tracking-wide border-b border-stone-100 pb-2">2. Metallurgical Specifications</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div>
                                <label class="block text-stone-500 font-medium mb-1.5 uppercase tracking-wider text-[10px]">Metal Type</label>
                                <select name="metal_type" class="w-full bg-stone-50/50 border border-stone-200 focus:border-[#832729] rounded-lg px-4 py-3 text-stone-800 focus:outline-none text-sm transition-all">
                                    <option value="Gold">Gold</option>
                                    <option value="Platinum">Platinum</option>
                                    <option value="Silver">Silver</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-stone-500 font-medium mb-1.5 uppercase tracking-wider text-[10px]">Purity Configuration</label>
                                <select name="purity" class="w-full bg-stone-50/50 border border-stone-200 focus:border-[#832729] rounded-lg px-4 py-3 text-stone-800 focus:outline-none text-sm font-medium transition-all">
                                    <option value="22Kt">22Kt (91.6% Pure)</option>
                                    <option value="18Kt">18Kt (75.0% Pure)</option>
                                    <option value="24Kt">24Kt (99.9% Pure Investment)</option>
                                    <option value="14Kt">14Kt (58.5% Pure Sturdy)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-stone-500 font-medium mb-1.5 uppercase tracking-wider text-[10px]">Net Weight (Grams)</label>
                                <input type="number" step="0.001" name="weight_grams" placeholder="e.g., 5.250" required class="w-full bg-stone-50/50 border border-stone-200 focus:border-[#832729] rounded-lg px-4 py-3 text-stone-800 focus:outline-none text-sm font-mono transition-all placeholder-stone-300">
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-[#832729] font-serif text-sm font-semibold mb-4 tracking-wide border-b border-stone-100 pb-2">3. Commercial Parameters</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-stone-500 font-medium mb-1.5 uppercase tracking-wider text-[10px]">Making Charges (Per Gram in INR)</label>
                                <input type="number" step="0.01" name="making_charges_per_gram" placeholder="e.g., 499" required class="w-full bg-stone-50/50 border border-stone-200 focus:border-[#832729] rounded-lg px-4 py-3 text-stone-800 focus:outline-none text-sm font-mono transition-all placeholder-stone-300">
                            </div>
                            <div>
                                <label class="block text-stone-500 font-medium mb-1.5 uppercase tracking-wider text-[10px]">Promotional Discount % (On Making Charges)</label>
                                <input type="number" name="discount_percent" value="0" min="0" max="100" class="w-full bg-stone-50/50 border border-stone-200 focus:border-[#832729] rounded-lg px-4 py-3 text-stone-800 focus:outline-none text-sm font-mono transition-all">
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-[#832729] font-serif text-sm font-semibold mb-4 tracking-wide border-b border-stone-100 pb-2">4. Navigation Taxonomy & Asset Links</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
                            <div>
                                <label class="block text-stone-500 font-medium mb-1.5 uppercase tracking-wider text-[10px]">Primary Category</label>
                                <input type="text" name="category" placeholder="e.g., Rings" required class="w-full bg-stone-50/50 border border-stone-200 focus:border-[#832729] rounded-lg px-4 py-3 text-stone-800 focus:outline-none text-sm transition-all">
                            </div>
                            <div>
                                <label class="block text-stone-500 font-medium mb-1.5 uppercase tracking-wider text-[10px]">Sub-Category</label>
                                <input type="text" name="subcategory" placeholder="e.g., Dailywear Rings" class="w-full bg-stone-50/50 border border-stone-200 focus:border-[#832729] rounded-lg px-4 py-3 text-stone-800 focus:outline-none text-sm transition-all">
                            </div>
                            <div>
                                <label class="block text-stone-500 font-medium mb-1.5 uppercase tracking-wider text-[10px]">Collection Tag</label>
                                <input type="text" name="collection_tag" placeholder="e.g., Elevated Summer Stack" class="w-full bg-stone-50/50 border border-stone-200 focus:border-[#832729] rounded-lg px-4 py-3 text-stone-800 focus:outline-none text-sm transition-all">
                            </div>
                        </div>
                        <div>
                            <label class="block text-stone-500 font-medium mb-1.5 uppercase tracking-wider text-[10px]">Primary Showcase Image (Upload File)</label>
                            <input type="file" name="primary_image_file" accept="image/*" required class="w-full bg-stone-50/50 border border-stone-200 focus:border-[#832729] rounded-lg px-4 py-2.5 text-stone-700 focus:outline-none file:mr-4 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-[11px] file:font-semibold file:bg-[#832729]/10 file:text-[#832729] hover:file:bg-[#832729]/20 file:cursor-pointer transition-all">
                        </div>
                    </div>

                    <div>
                        <h3 class="text-[#832729] font-serif text-sm font-semibold mb-4 tracking-wide border-b border-stone-100 pb-2">5. Editorial Content</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-stone-500 font-medium mb-1.5 uppercase tracking-wider text-[10px]">Short Description (Card hover snapshot)</label>
                                <textarea name="short_description" rows="2" required placeholder="A crisp summary for listing cards..." class="w-full bg-stone-50/50 border border-stone-200 focus:border-[#832729] rounded-lg px-4 py-3 text-stone-800 focus:outline-none text-sm font-light transition-all"></textarea>
                            </div>
                            <div>
                                <label class="block text-stone-500 font-medium mb-1.5 uppercase tracking-wider text-[10px]">Full Technical Story Specification Sheet</label>
                                <textarea name="full_description" rows="3" placeholder="Detailed product craftsmanship narrative..." class="w-full bg-stone-50/50 border border-stone-200 focus:border-[#832729] rounded-lg px-4 py-3 text-stone-800 focus:outline-none text-sm font-light transition-all"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4 pt-4 border-t border-stone-100">
                        <button type="submit" class="flex-1 bg-[#832729] hover:bg-[#6b2022] text-white font-medium tracking-widest py-3.5 rounded-lg transition-colors uppercase text-xs shadow-sm font-semibold">Commit Artifact to Vault</button>
                        <a href="manage-products.php" class="bg-stone-100 hover:bg-stone-200 text-stone-600 font-medium tracking-wide px-6 py-3.5 rounded-lg transition-colors uppercase text-xs border border-stone-200 text-center">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>