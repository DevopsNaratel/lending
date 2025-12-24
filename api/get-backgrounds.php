<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

try {
    $query = "SELECT section, image_url FROM backgrounds WHERE is_active = 1";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $backgrounds = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $backgrounds[$row['section']] = $row['image_url'];
    }
    
    // Default backgrounds if none set
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
    
    // Merge with defaults
    foreach ($defaultBackgrounds as $section => $defaultUrl) {
        if (!isset($backgrounds[$section])) {
            $backgrounds[$section] = $defaultUrl;
        }
    }
    
    echo json_encode(['success' => true, 'backgrounds' => $backgrounds]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>