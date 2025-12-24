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
                $query = "INSERT INTO collections (name, description, image) VALUES (?, ?, ?)";
                $stmt = $db->prepare($query);
                $stmt->execute([$_POST['name'], $_POST['description'], $_POST['image']]);
                $success = "Koleksi berhasil ditambahkan!";
                break;
                
            case 'edit':
                $query = "UPDATE collections SET name=?, description=?, image=? WHERE id=?";
                $stmt = $db->prepare($query);
                $stmt->execute([$_POST['name'], $_POST['description'], $_POST['image'], $_POST['id']]);
                $success = "Koleksi berhasil diupdate!";
                break;
                
            case 'delete':
                $query = "DELETE FROM collections WHERE id=?";
                $stmt = $db->prepare($query);
                $stmt->execute([$_POST['id']]);
                $success = "Koleksi berhasil dihapus!";
                break;
                
            case 'toggle_status':
                $query = "UPDATE collections SET status = CASE WHEN status = 'active' THEN 'inactive' ELSE 'active' END WHERE id=?";
                $stmt = $db->prepare($query);
                $stmt->execute([$_POST['id']]);
                $success = "Status koleksi berhasil diubah!";
                break;
        }
    }
}

// Get collections
$query = "SELECT * FROM collections ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$collections = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get single collection for editing
$editCollection = null;
if (isset($_GET['edit'])) {
    $query = "SELECT * FROM collections WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$_GET['edit']]);
    $editCollection = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola Koleksi - Admin</title>
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
                <li><a href="collections.php" class="active"><i class="fas fa-layer-group"></i> Koleksi</a></li>
                <li><a href="products.php"><i class="fas fa-box"></i> Produk</a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Pesanan</a></li>
                <li><a href="backgrounds.php"><i class="fas fa-image"></i> Background</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
        
        <main class="main-content">
            <div class="page-header">
                <h1>Kelola Koleksi</h1>
                <a href="#" onclick="showAddForm()" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Koleksi
                </a>
            </div>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <!-- Add/Edit Form -->
            <div id="collectionForm" class="form-container" style="display: <?php echo $editCollection ? 'block' : 'none'; ?>;">
                <h2><?php echo $editCollection ? 'Edit Koleksi' : 'Tambah Koleksi Baru'; ?></h2>
                <form method="POST">
                    <input type="hidden" name="action" value="<?php echo $editCollection ? 'edit' : 'add'; ?>">
                    <?php if ($editCollection): ?>
                        <input type="hidden" name="id" value="<?php echo $editCollection['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label>Nama Koleksi:</label>
                        <input type="text" name="name" value="<?php echo $editCollection['name'] ?? ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Deskripsi:</label>
                        <textarea name="description" rows="4" required><?php echo $editCollection['description'] ?? ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>URL Gambar:</label>
                        <input type="url" name="image" value="<?php echo $editCollection['image'] ?? ''; ?>" required>
                    </div>
                    
                    <div class="actions">
                        <button type="submit" class="btn btn-success">
                            <?php echo $editCollection ? 'Update' : 'Simpan'; ?>
                        </button>
                        <button type="button" onclick="hideForm()" class="btn btn-danger">Batal</button>
                    </div>
                </form>
            </div>
            
            <!-- Collections List -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Gambar</th>
                            <th>Nama</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($collections as $collection): ?>
                        <tr>
                            <td>#<?php echo $collection['id']; ?></td>
                            <td>
                                <img src="<?php echo $collection['image']; ?>" alt="<?php echo $collection['name']; ?>" 
                                     style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;">
                            </td>
                            <td><?php echo htmlspecialchars($collection['name']); ?></td>
                            <td><?php echo htmlspecialchars(substr($collection['description'], 0, 100)) . '...'; ?></td>
                            <td>
                                <span class="status <?php echo $collection['status']; ?>">
                                    <?php echo ucfirst($collection['status']); ?>
                                </span>
                            </td>
                            <td class="actions">
                                <a href="?edit=<?php echo $collection['id']; ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin mengubah status?')">
                                    <input type="hidden" name="action" value="toggle_status">
                                    <input type="hidden" name="id" value="<?php echo $collection['id']; ?>">
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fas fa-toggle-<?php echo $collection['status'] == 'active' ? 'on' : 'off'; ?>"></i>
                                    </button>
                                </form>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $collection['id']; ?>">
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
            document.getElementById('collectionForm').style.display = 'block';
        }
        
        function hideForm() {
            document.getElementById('collectionForm').style.display = 'none';
            window.location.href = 'collections.php';
        }
    </script>
</body>
</html>