<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json; charset=utf-8');

if (!isLoggedIn() || !isStudent()) {
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
        // التحقق من التسجيل
        $stmt = $pdo->prepare("SELECT id FROM Registrations WHERE student_id = ? AND event_id = ?");
        $stmt->execute([$_SESSION['user_id'], $event_id]);
        $registration = $stmt->fetch();
        
        if (!$registration) {
            echo json_encode(['success' => false, 'message' => 'أنت غير مسجل في هذه الفعالية']);
            exit;
        }
        
        // إلغاء التسجيل
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("DELETE FROM Registrations WHERE id = ?");
        $stmt->execute([$registration['id']]);
        
        $stmt = $pdo->prepare("UPDATE Events SET available_seats = available_seats + 1 WHERE id = ?");
        $stmt->execute([$event_id]);
        
        $pdo->commit();
        
        echo json_encode(['success' => true, 'message' => 'تم إلغاء التسجيل بنجاح']);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'خطأ في قاعدة البيانات']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'طريقة طلب غير صحيحة']);
}
?>
