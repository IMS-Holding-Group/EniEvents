<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json; charset=utf-8');

if (!isLoggedIn() || !isOrganizer()) {
    echo json_encode(['success' => false, 'message' => 'غير مصرح']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = intval($_POST['event_id'] ?? 0);
    
    if ($event_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'معرف فعالية غير صحيح']);
        exit;
    }
    
    try {
        // التحقق من ملكية الفعالية
        $stmt = $pdo->prepare("SELECT organizer_id FROM Events WHERE id = ?");
        $stmt->execute([$event_id]);
        $event = $stmt->fetch();
        
        if (!$event || $event['organizer_id'] != $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'غير مصرح']);
            exit;
        }
        
        $stmt = $pdo->prepare("DELETE FROM Events WHERE id = ?");
        $stmt->execute([$event_id]);
        
        echo json_encode(['success' => true, 'message' => 'تم حذف الفعالية بنجاح']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'خطأ في قاعدة البيانات']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'طريقة طلب غير صحيحة']);
}
?>
