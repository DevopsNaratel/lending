<?php
// Database setup script
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'florist_db';

try {
    // Create database
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    $pdo->exec("USE $dbname");
    
    // Create tables
    $sql = "
    CREATE TABLE IF NOT EXISTS collections (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        image VARCHAR(255),
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        image VARCHAR(255),
        category ENUM('bestseller', 'regular') DEFAULT 'regular',
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        address TEXT NOT NULL,
        product VARCHAR(255),
        notes TEXT,
        status ENUM('pending', 'processing', 'completed') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS admin_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    ";
    
    $pdo->exec($sql);
    
    // Insert default admin user (admin/admin123)
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->exec("INSERT IGNORE INTO admin_users (username, password) VALUES ('admin', '$adminPassword')");
    
    // Insert sample data
    $sampleCollections = [
        ['Buket Bunga', 'Rangkaian bunga segar untuk berbagai momen spesial', 'https://images.unsplash.com/photo-1563241527-3004b7be0ffd?w=400&h=300&fit=crop'],
        ['Standing Flower', 'Karangan bunga standing untuk acara formal', 'https://images.unsplash.com/photo-1606041008023-472dfb5e530f?w=400&h=300&fit=crop'],
        ['Hampers Premium', 'Paket lengkap bunga + coklat premium', 'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=400&h=300&fit=crop']
    ];
    
    foreach($sampleCollections as $collection) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO collections (name, description, image) VALUES (?, ?, ?)");
        $stmt->execute($collection);
    }
    
    $sampleProducts = [
        ['Rose Elegance', 'Buket mawar merah premium dengan baby breath', 350000, 'https://images.unsplash.com/photo-1563241527-3004b7be0ffd?w=300&h=300&fit=crop', 'bestseller'],
        ['Pastel Dream', 'Mix bunga pastel dengan hydrangea', 425000, 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=300&h=300&fit=crop', 'bestseller'],
        ['Luxury Hampers', 'Paket premium: bunga + coklat + wine', 750000, 'https://images.unsplash.com/photo-1606041008023-472dfb5e530f?w=300&h=300&fit=crop', 'bestseller'],
        ['Sunflower Joy', 'Buket bunga matahari ceria', 285000, 'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=300&h=300&fit=crop', 'bestseller']
    ];
    
    foreach($sampleProducts as $product) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO products (name, description, price, image, category) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute($product);
    }
    
    echo "Database setup completed successfully!<br>";
    echo "Admin login: admin / admin123<br>";
    echo "<a href='admin/login.php'>Go to Admin Panel</a>";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>