<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    $name = $input['name'] ?? '';
    $phone = $input['phone'] ?? '';
    $address = $input['address'] ?? '';
    $product = $input['product'] ?? '';
    $notes = $input['notes'] ?? '';
    
    if (empty($name) || empty($phone) || empty($address) || empty($product)) {
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
        exit();
    }
    
    try {
        $query = "INSERT INTO orders (name, phone, address, product, notes) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->execute([$name, $phone, $address, $product, $notes]);
        
        $orderId = $db->lastInsertId();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Pesanan berhasil disimpan',
            'order_id' => $orderId
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan pesanan: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>