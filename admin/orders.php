<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Handle status update
if ($_POST && isset($_POST['action']) && $_POST['action'] == 'update_status') {
    $query = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$_POST['status'], $_POST['id']]);
    $success = "Status pesanan berhasil diupdate!";
}

// Handle delete
if ($_POST && isset($_POST['action']) && $_POST['action'] == 'delete') {
    $query = "DELETE FROM orders WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$_POST['id']]);
    $success = "Pesanan berhasil dihapus!";
}

// Get orders
$query = "SELECT * FROM orders ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola Pesanan - Admin</title>
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
                <li><a href="orders.php" class="active"><i class="fas fa-shopping-cart"></i> Pesanan</a></li>
                <li><a href="backgrounds.php"><i class="fas fa-image"></i> Background</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
        
        <main class="main-content">
            <div class="page-header">
                <h1>Kelola Pesanan</h1>
            </div>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <!-- Orders List -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>No. HP</th>
                            <th>Alamat</th>
                            <th>Produk</th>
                            <th>Catatan</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo htmlspecialchars($order['name']); ?></td>
                            <td>
                                <a href="https://wa.me/<?php echo $order['phone']; ?>" target="_blank" class="btn btn-success btn-sm">
                                    <i class="fab fa-whatsapp"></i> <?php echo $order['phone']; ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars(substr($order['address'], 0, 50)) . '...'; ?></td>
                            <td><?php echo htmlspecialchars($order['product']); ?></td>
                            <td><?php echo htmlspecialchars(substr($order['notes'], 0, 30)) . '...'; ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="id" value="<?php echo $order['id']; ?>">
                                    <select name="status" onchange="this.form.submit()" class="status <?php echo $order['status']; ?>">
                                        <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                        <option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    </select>
                                </form>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                            <td class="actions">
                                <button onclick="showOrderDetail(<?php echo htmlspecialchars(json_encode($order)); ?>)" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus pesanan ini?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $order['id']; ?>">
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
    
    <!-- Order Detail Modal -->
    <div id="orderModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 30px; border-radius: 10px; max-width: 500px; width: 90%;">
            <h2>Detail Pesanan</h2>
            <div id="orderDetails"></div>
            <button onclick="closeModal()" class="btn btn-primary" style="margin-top: 20px;">Tutup</button>
        </div>
    </div>
    
    <script>
        function showOrderDetail(order) {
            const modal = document.getElementById('orderModal');
            const details = document.getElementById('orderDetails');
            
            details.innerHTML = `
                <p><strong>ID:</strong> #${order.id}</p>
                <p><strong>Nama:</strong> ${order.name}</p>
                <p><strong>No. HP:</strong> ${order.phone}</p>
                <p><strong>Alamat:</strong> ${order.address}</p>
                <p><strong>Produk:</strong> ${order.product}</p>
                <p><strong>Catatan:</strong> ${order.notes || 'Tidak ada catatan'}</p>
                <p><strong>Status:</strong> ${order.status}</p>
                <p><strong>Tanggal:</strong> ${new Date(order.created_at).toLocaleString('id-ID')}</p>
            `;
            
            modal.style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('orderModal').style.display = 'none';
        }
        
        // Close modal when clicking outside
        document.getElementById('orderModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>
</html>