# 🗄️ Database Structure - Florist Landing Page

## Database: `florist_db`

### 📋 **Tabel Utama:**

#### 1. **collections** - Koleksi Produk
```sql
id (INT, PRIMARY KEY, AUTO_INCREMENT)
name (VARCHAR 255) - Nama koleksi
description (TEXT) - Deskripsi koleksi  
image (VARCHAR 255) - URL gambar
status (ENUM: active/inactive) - Status aktif
created_at (TIMESTAMP)
updated_at (TIMESTAMP)
```

#### 2. **products** - Produk
```sql
id (INT, PRIMARY KEY, AUTO_INCREMENT)
name (VARCHAR 255) - Nama produk
description (TEXT) - Deskripsi produk
price (DECIMAL 10,2) - Harga produk
image (VARCHAR 255) - URL gambar
category (ENUM: bestseller/regular) - Kategori
status (ENUM: active/inactive) - Status aktif
created_at (TIMESTAMP)
updated_at (TIMESTAMP)
```

#### 3. **orders** - Pesanan
```sql
id (INT, PRIMARY KEY, AUTO_INCREMENT)
name (VARCHAR 255) - Nama pemesan
phone (VARCHAR 20) - No. telepon
address (TEXT) - Alamat pengiriman
product (VARCHAR 255) - Produk yang dipesan
notes (TEXT) - Catatan pesanan
status (ENUM: pending/processing/completed) - Status pesanan
created_at (TIMESTAMP)
updated_at (TIMESTAMP)
```

#### 4. **admin_users** - Admin Users
```sql
id (INT, PRIMARY KEY, AUTO_INCREMENT)
username (VARCHAR 50, UNIQUE) - Username admin
password (VARCHAR 255) - Password (hashed)
email (VARCHAR 100) - Email admin
role (ENUM: admin/staff) - Role user
status (ENUM: active/inactive) - Status aktif
created_at (TIMESTAMP)
updated_at (TIMESTAMP)
```

## 🚀 **Cara Install Database:**

### **Opsi 1: Otomatis via Web**
```
1. Buka: http://localhost/landing_page_florist/install.php
2. Database akan dibuat otomatis dengan sample data
```

### **Opsi 2: Manual via phpMyAdmin**
```
1. Buka phpMyAdmin
2. Import file: database_structure.sql
3. Database dan sample data akan dibuat
```

### **Opsi 3: Command Line**
```bash
mysql -u root -p < database_structure.sql
```

## 🔐 **Default Admin Login:**
- **Username:** admin
- **Password:** admin123
- **URL:** http://localhost/landing_page_florist/admin/login.php

## 📊 **Sample Data Included:**
- ✅ 5 Koleksi produk
- ✅ 6 Produk (4 bestseller, 2 regular)
- ✅ 3 Sample pesanan
- ✅ 1 Admin user

## 🔍 **Database Indexes:**
- Status indexes untuk performa query
- Category indexes untuk filtering
- Date indexes untuk sorting
- Username index untuk login

## 🛠️ **Konfigurasi Database:**
File: `config/database.php`
```php
Host: localhost
Database: florist_db  
Username: root
Password: (kosong)
```