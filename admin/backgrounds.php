<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Create backgrounds table if not exists
$createTable = "
CREATE TABLE IF NOT EXISTS backgrounds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section VARCHAR(50) NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$db->exec($createTable);

// Handle form submissions
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update':
                foreach ($_POST['backgrounds'] as $section => $imageUrl) {
                    if (!empty($imageUrl)) {
                        // Delete old background for this section
                        $deleteQuery = "DELETE FROM backgrounds WHERE section = ?";
                        $stmt = $db->prepare($deleteQuery);
                        $stmt->execute([$section]);
                        
                        // Insert new background
                        $insertQuery = "INSERT INTO backgrounds (section, image_url) VALUES (?, ?)";
                        $stmt = $db->prepare($insertQuery);
                        $stmt->execute([$section, $imageUrl]);
                    }
                }
                $success = "Background berhasil diupdate!";
                break;
        }
    }
}

// Get current backgrounds
$query = "SELECT * FROM backgrounds WHERE is_active = 1";
$stmt = $db->prepare($query);
$stmt->execute();
$backgrounds = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $backgrounds[$row['section']] = $row['image_url'];
}

// Default backgrounds
$defaultBackgrounds = [
    'hero' => 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=1920&h=1080&fit=crop',
    'features' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1920&h=600&fit=crop',
    'categories' => 'https://images.unsplash.com/photo-1490750967868-88aa4486c946?w=1920&h=800&fit=crop',
    'bestsellers' => 'https://images.unsplash.com/photo-1487070183336-b863922373d4?w=1920&h=600&fit=crop',
    'promo' => 'https://images.unsplash.com/photo-1518709268805-4e9042af2176?w=1920&h=600&fit=crop',
    'testimonials' => 'https://images.unsplash.com/photo-1464207687429-7505649dae38?w=1920&h=600&fit=crop',
    'gallery' => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=1920&h=600&fit=crop',
    'order' => 'https://images.unsplash.com/photo-1426604966848-d7adac402bff?w=1920&h=800&fit=crop'
];

// Merge with current backgrounds
foreach ($defaultBackgrounds as $section => $defaultUrl) {
    if (!isset($backgrounds[$section])) {
        $backgrounds[$section] = $defaultUrl;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola Background - Admin</title>
    <link rel="stylesheet" href="admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <nav class="sidebar">
            <div class="logo">
                <h3>Bloom & Bliss Admin</h3>
            </div>
            <ul class="nav-menu">
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="collections.php"><i class="fas fa-layer-group"></i> Koleksi</a></li>
                <li><a href="products.php"><i class="fas fa-box"></i> Produk</a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Pesanan</a></li>
                <li><a href="backgrounds.php" class="active"><i class="fas fa-image"></i> Background</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
        
        <main class="main-content">
            <div class="page-header">
                <h1>Kelola Background Website</h1>
                <p>Ubah background setiap section untuk tampilan yang lebih menarik</p>
            </div>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="background-manager">
                <form method="POST">
                    <input type="hidden" name="action" value="update">
                    
                    <div class="background-sections">
                        <div class="bg-section">
                            <h3><i class="fas fa-home"></i> Hero Section</h3>
                            <div class="bg-preview">
                                <img src="<?php echo $backgrounds['hero']; ?>" alt="Hero Background">
                            </div>
                            <div class="bg-input">
                                <label>URL Gambar:</label>
                                <input type="url" name="backgrounds[hero]" value="<?php echo $backgrounds['hero']; ?>" placeholder="https://images.unsplash.com/...">
                                <small>Rekomendasi: Taman bunga, landscape alam, atau bunga field</small>
                            </div>
                        </div>
                        
                        <div class="bg-section">
                            <h3><i class="fas fa-star"></i> Features Section</h3>
                            <div class="bg-preview">
                                <img src="<?php echo $backgrounds['features']; ?>" alt="Features Background">
                            </div>
                            <div class="bg-input">
                                <label>URL Gambar:</label>
                                <input type="url" name="backgrounds[features]" value="<?php echo $backgrounds['features']; ?>">
                                <small>Rekomendasi: Soft flower background, botanical garden</small>
                            </div>
                        </div>
                        
                        <div class="bg-section">
                            <h3><i class="fas fa-th-large"></i> Categories Section</h3>
                            <div class="bg-preview">
                                <img src="<?php echo $backgrounds['categories']; ?>" alt="Categories Background">
                            </div>
                            <div class="bg-input">
                                <label>URL Gambar:</label>
                                <input type="url" name="backgrounds[categories]" value="<?php echo $backgrounds['categories']; ?>">
                                <small>Rekomendasi: Flower shop, colorful flowers, garden view</small>
                            </div>
                        </div>
                        
                        <div class="bg-section">
                            <h3><i class="fas fa-fire"></i> Bestsellers Section</h3>
                            <div class="bg-preview">
                                <img src="<?php echo $backgrounds['bestsellers']; ?>" alt="Bestsellers Background">
                            </div>
                            <div class="bg-input">
                                <label>URL Gambar:</label>
                                <input type="url" name="backgrounds[bestsellers]" value="<?php echo $backgrounds['bestsellers']; ?>">
                                <small>Rekomendasi: Premium flowers, elegant bouquet background</small>
                            </div>
                        </div>
                        
                        <div class="bg-section">
                            <h3><i class="fas fa-gift"></i> Promo Section</h3>
                            <div class="bg-preview">
                                <img src="<?php echo $backgrounds['promo']; ?>" alt="Promo Background">
                            </div>
                            <div class="bg-input">
                                <label>URL Gambar:</label>
                                <input type="url" name="backgrounds[promo]" value="<?php echo $backgrounds['promo']; ?>">
                                <small>Rekomendasi: Vibrant flowers, celebration theme</small>
                            </div>
                        </div>
                        
                        <div class="bg-section">
                            <h3><i class="fas fa-comments"></i> Testimonials Section</h3>
                            <div class="bg-preview">
                                <img src="<?php echo $backgrounds['testimonials']; ?>" alt="Testimonials Background">
                            </div>
                            <div class="bg-input">
                                <label>URL Gambar:</label>
                                <input type="url" name="backgrounds[testimonials]" value="<?php echo $backgrounds['testimonials']; ?>">
                                <small>Rekomendasi: Peaceful garden, soft nature background</small>
                            </div>
                        </div>
                        
                        <div class="bg-section">
                            <h3><i class="fas fa-images"></i> Gallery Section</h3>
                            <div class="bg-preview">
                                <img src="<?php echo $backgrounds['gallery']; ?>" alt="Gallery Background">
                            </div>
                            <div class="bg-input">
                                <label>URL Gambar:</label>
                                <input type="url" name="backgrounds[gallery]" value="<?php echo $backgrounds['gallery']; ?>">
                                <small>Rekomendasi: Forest, nature landscape, green background</small>
                            </div>
                        </div>
                        
                        <div class="bg-section">
                            <h3><i class="fas fa-shopping-cart"></i> Order Section</h3>
                            <div class="bg-preview">
                                <img src="<?php echo $backgrounds['order']; ?>" alt="Order Background">
                            </div>
                            <div class="bg-input">
                                <label>URL Gambar:</label>
                                <input type="url" name="backgrounds[order]" value="<?php echo $backgrounds['order']; ?>">
                                <small>Rekomendasi: Mountain landscape, serene nature view</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Semua Background
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="previewChanges()">
                            <i class="fas fa-eye"></i> Preview Website
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="bg-suggestions">
                <h3>🌸 Saran Background Cantik:</h3>
                <div class="suggestion-grid">
                    <div class="suggestion-item" onclick="useBackground('https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=1920&h=1080&fit=crop')">
                        <img src="https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=200&h=120&fit=crop" alt="Flower Field">
                        <span>Flower Field</span>
                    </div>
                    <div class="suggestion-item" onclick="useBackground('https://images.unsplash.com/photo-1490750967868-88aa4486c946?w=1920&h=800&fit=crop')">
                        <img src="https://images.unsplash.com/photo-1490750967868-88aa4486c946?w=200&h=120&fit=crop" alt="Garden Path">
                        <span>Garden Path</span>
                    </div>
                    <div class="suggestion-item" onclick="useBackground('https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1920&h=600&fit=crop')">
                        <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=200&h=120&fit=crop" alt="Botanical Garden">
                        <span>Botanical Garden</span>
                    </div>
                    <div class="suggestion-item" onclick="useBackground('https://images.unsplash.com/photo-1518709268805-4e9042af2176?w=1920&h=600&fit=crop')">
                        <img src="https://images.unsplash.com/photo-1518709268805-4e9042af2176?w=200&h=120&fit=crop" alt="Spring Flowers">
                        <span>Spring Flowers</span>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <style>
        .background-manager { margin-top: 2rem; }
        .background-sections { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 2rem; margin-bottom: 2rem; }
        .bg-section { background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .bg-section h3 { color: #2d3748; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
        .bg-preview { margin-bottom: 1rem; }
        .bg-preview img { width: 100%; height: 120px; object-fit: cover; border-radius: 8px; border: 2px solid #e2e8f0; }
        .bg-input label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: #2d3748; }
        .bg-input input { width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 5px; margin-bottom: 0.5rem; }
        .bg-input small { color: #666; font-style: italic; }
        .form-actions { text-align: center; padding: 2rem 0; }
        .form-actions .btn { margin: 0 0.5rem; }
        .bg-suggestions { margin-top: 3rem; background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .suggestion-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-top: 1rem; }
        .suggestion-item { text-align: center; cursor: pointer; padding: 1rem; border-radius: 8px; transition: all 0.3s ease; }
        .suggestion-item:hover { background: #f7fafc; transform: translateY(-2px); }
        .suggestion-item img { width: 100%; height: 80px; object-fit: cover; border-radius: 5px; margin-bottom: 0.5rem; }
        .suggestion-item span { font-size: 0.9rem; color: #666; }
    </style>
    
    <script>
        let currentInput = null;
        
        function useBackground(url) {
            if (currentInput) {
                currentInput.value = url;
                currentInput.parentElement.parentElement.querySelector('.bg-preview img').src = url;
                currentInput = null;
            } else {
                alert('Klik pada input URL terlebih dahulu, lalu pilih background yang diinginkan.');
            }
        }
        
        // Track current input
        document.querySelectorAll('input[type="url"]').forEach(input => {
            input.addEventListener('focus', function() {
                currentInput = this;
                document.querySelectorAll('input[type="url"]').forEach(i => i.style.borderColor = '#e2e8f0');
                this.style.borderColor = '#f093fb';
            });
            
            input.addEventListener('input', function() {
                const preview = this.parentElement.parentElement.querySelector('.bg-preview img');
                if (this.value) {
                    preview.src = this.value;
                }
            });
        });
        
        function previewChanges() {
            window.open('../index-elora.html', '_blank');
        }
    </script>
</body>
</html>