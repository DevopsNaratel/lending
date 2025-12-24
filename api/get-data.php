<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$type = $_GET['type'] ?? '';

switch ($type) {
    case 'collections':
        $query = "SELECT * FROM collections WHERE status = 'active' ORDER BY created_at DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        break;
        
    case 'products':
        $category = $_GET['category'] ?? 'all';
        if ($category == 'bestseller') {
            $query = "SELECT * FROM products WHERE status = 'active' AND category = 'bestseller' ORDER BY created_at DESC";
        } else {
            $query = "SELECT * FROM products WHERE status = 'active' ORDER BY created_at DESC";
        }
        $stmt = $db->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        break;
        
    default:
        $data = ['error' => 'Invalid type parameter'];
}

echo json_encode($data);
?>