-- Database: florist_db
-- Struktur Database untuk Landing Page Florist

CREATE DATABASE IF NOT EXISTS florist_db;
USE florist_db;

-- Tabel untuk menyimpan koleksi produk
CREATE TABLE collections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel untuk menyimpan produk
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    category ENUM('bestseller', 'regular') DEFAULT 'regular',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel untuk menyimpan pesanan
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    product VARCHAR(255),
    notes TEXT,
    status ENUM('pending', 'processing', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel untuk admin users
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    role ENUM('admin', 'staff') DEFAULT 'admin',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user
INSERT INTO admin_users (username, password, email) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@bloombliss.com');
-- Password: admin123

-- Sample data untuk collections
INSERT INTO collections (name, description, image) VALUES 
('Buket Bunga', 'Rangkaian bunga segar untuk berbagai momen spesial. Tersedia berbagai pilihan warna dan ukuran.', 'https://images.unsplash.com/photo-1563241527-3004b7be0ffd?w=400&h=300&fit=crop'),
('Standing Flower', 'Karangan bunga standing untuk acara formal, grand opening, atau ucapan selamat.', 'https://images.unsplash.com/photo-1606041008023-472dfb5e530f?w=400&h=300&fit=crop'),
('Bunga Papan', 'Papan bunga elegan untuk duka cita, pernikahan, atau acara penting lainnya.', 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=300&fit=crop'),
('Hampers Premium', 'Paket lengkap bunga + coklat premium + wine untuk hadiah istimewa yang berkesan.', 'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=400&h=300&fit=crop'),
('Paket Anniversary', 'Paket spesial untuk ulang tahun dan anniversary dengan bunga + kue + balon.', 'https://images.unsplash.com/photo-1606041008023-472dfb5e530f?w=400&h=300&fit=crop');

-- Sample data untuk products
INSERT INTO products (name, description, price, image, category) VALUES 
('Rose Elegance', 'Buket mawar merah premium dengan baby breath dan eucalyptus', 350000, 'https://images.unsplash.com/photo-1563241527-3004b7be0ffd?w=300&h=300&fit=crop', 'bestseller'),
('Pastel Dream', 'Mix bunga pastel dengan hydrangea dan lisianthus yang lembut', 425000, 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=300&h=300&fit=crop', 'bestseller'),
('Luxury Hampers', 'Paket premium: bunga + coklat Ferrero + wine + kartu ucapan', 750000, 'https://images.unsplash.com/photo-1606041008023-472dfb5e530f?w=300&h=300&fit=crop', 'bestseller'),
('Sunflower Joy', 'Buket bunga matahari ceria dengan chrysanthemum kuning', 285000, 'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=300&h=300&fit=crop', 'bestseller'),
('White Elegance', 'Buket bunga putih elegan dengan mawar dan lily', 395000, 'https://images.unsplash.com/photo-1563241527-3004b7be0ffd?w=300&h=300&fit=crop', 'regular'),
('Tropical Paradise', 'Rangkaian bunga tropis eksotis dengan bird of paradise', 485000, 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=300&h=300&fit=crop', 'regular');

-- Sample data untuk orders (contoh pesanan)
INSERT INTO orders (name, phone, address, product, notes, status) VALUES 
('Sarah Putri', '081234567890', 'Jl. Kemang Raya No. 123, Jakarta Selatan', 'Rose Elegance', 'Tolong kirim pagi hari ya', 'pending'),
('Budi Santoso', '081234567891', 'Jl. Sudirman No. 456, Jakarta Pusat', 'Luxury Hampers', 'Untuk anniversary, mohon packaging yang bagus', 'processing'),
('Maya Sari', '081234567892', 'Jl. Gatot Subroto No. 789, Jakarta Selatan', 'Pastel Dream', 'Warna pink dominan ya', 'completed');

-- Indexes untuk performa yang lebih baik
CREATE INDEX idx_collections_status ON collections(status);
CREATE INDEX idx_products_status ON products(status);
CREATE INDEX idx_products_category ON products(category);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_orders_created_at ON orders(created_at);
CREATE INDEX idx_admin_users_username ON admin_users(username);