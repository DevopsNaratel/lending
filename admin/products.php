<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Handle form submissions
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $query = "INSERT INTO products (name, description, price, image, category) VALUES (?, ?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                $stmt->execute([$_POST['name'], $_POST['description'], $_POST['price'], $_POST['image'], $_POST['category']]);
                $success = "Produk berhasil ditambahkan!";
                break;
                
            case 'edit':
                $query = "UPDATE products SET name=?, description=?, price=?, image=?, category=? WHERE id=?";
                $stmt = $db->prepare($query);
                $stmt->execute([$_POST['name'], $_POST['description'], $_POST['price'], $_POST['image'], $_POST['category'], $_POST['id']]);
                $success = "Produk berhasil diupdate!";
                break;
                
            case 'delete':
                $query = "DELETE FROM products WHERE id=?";
                $stmt = $db->prepare($query);
                $stmt->execute([$_POST['id']]);
                $success = "Produk berhasil dihapus!";
                break;
                
            case 'toggle_status':
                $query = "UPDATE products SET status = CASE WHEN status = 'active' THEN 'inactive' ELSE 'active' END WHERE id=?";
                $stmt = $db->prepare($query);
                $stmt->execute([$_POST['id']]);
                $success = "Status produk berhasil diubah!";
                break;
        }
    }
}

// Get products
$query = "SELECT * FROM products ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get single product for editing
$editProduct = null;
if (isset($_GET['edit'])) {
    $query = "SELECT * FROM products WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$_GET['edit']]);
    $editProduct = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola Produk - Admin</title>
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
                <li><a href="products.php" class="active"><i class="fas fa-box"></i> Produk</a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Pesanan</a></li>
                <li><a href="backgrounds.php"><i class="fas fa-image"></i> Background</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
        
        <main class="main-content">
            <div class="page-header">
                <h1>Kelola Produk</h1>
                <a href="#" onclick="showAddForm()" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Produk
                </a>
            </div>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <!-- Add/Edit Form -->
            <div id="productForm" class="form-container" style="display: <?php echo $editProduct ? 'block' : 'none'; ?>;">
                <h2><?php echo $editProduct ? 'Edit Produk' : 'Tambah Produk Baru'; ?></h2>
                <form method="POST">
                    <input type="hidden" name="action" value="<?php echo $editProduct ? 'edit' : 'add'; ?>">
                    <?php if ($editProduct): ?>
                        <input type="hidden" name="id" value="<?php echo $editProduct['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label>Nama Produk:</label>
                        <input type="text" name="name" value="<?php echo $editProduct['name'] ?? ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Deskripsi:</label>
                        <textarea name="description" rows="4" required><?php echo $editProduct['description'] ?? ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Harga (Rp):</label>
                        <input type="number" name="price" value="<?php echo $editProduct['price'] ?? ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>URL Gambar:</label>
                        <input type="url" name="image" value="<?php echo $editProduct['image'] ?? ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Kategori:</label>
                        <select name="category" required>
                            <option value="regular" <?php echo ($editProduct['category'] ?? '') == 'regular' ? 'selected' : ''; ?>>Regular</option>
                            <option value="bestseller" <?php echo ($editProduct['category'] ?? '') == 'bestseller' ? 'selected' : ''; ?>>Best Seller</option>
                        </select>
                    </div>
                    
                    <div class="actions">
                        <button type="submit" class="btn btn-success">
                            <?php echo $editProduct ? 'Update' : 'Simpan'; ?>
                        </button>
                        <button type="button" onclick="hideForm()" class="btn btn-danger">Batal</button>
                    </div>
                </form>
            </div>
            
            <!-- Products List -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Gambar</th>
                            <th>Nama</th>
                            <th>Harga</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td>#<?php echo $product['id']; ?></td>
                            <td>
                                <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" 
                                     style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;">
                            </td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td>Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></td>
                            <td>
                                <span class="status <?php echo $product['category']; ?>">
                                    <?php echo $product['category'] == 'bestseller' ? 'Best Seller' : 'Regular'; ?>
                                </span>
                            </td>
                            <td>
                                <span class="status <?php echo $product['status']; ?>">
                                    <?php echo ucfirst($product['status']); ?>
                                </span>
                            </td>
                            <td class="actions">
                                <a href="?edit=<?php echo $product['id']; ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin mengubah status?')">
                                    <input type="hidden" name="action" value="toggle_status">
                                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fas fa-toggle-<?php echo $product['status'] == 'active' ? 'on' : 'off'; ?>"></i>
                                    </button>
                                </form>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    
    <script>
        function showAddForm() {
            document.getElementById('productForm').style.display = 'block';
        }
        
        function hideForm() {
            document.getElementById('productForm').style.display = 'none';
            window.location.href = 'products.php';
        }
    </script>
</body>
</html>