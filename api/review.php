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
    $rating = intval($_POST['rating'] ?? 0);
    $comment = $_POST['comment'] ?? '';
    
    if ($event_id <= 0 || $rating < 1 || $rating > 5) {
        echo json_encode(['success' => false, 'message' => 'بيانات غير صحيحة']);
        exit;
    }
    
    try {
        // التحقق من انتهاء الفعالية
        $stmt = $pdo->prepare("SELECT event_date FROM Events WHERE id = ?");
        $stmt->execute([$event_id]);
        $event = $stmt->fetch();
        
        if (!$event) {
            echo json_encode(['success' => false, 'message' => 'الفعالية غير موجودة']);
            exit;
        }
        
        if (strtotime($event['event_date']) > time()) {
            echo json_encode(['success' => false, 'message' => 'يمكن التقييم بعد انتهاء الفعالية فقط']);
            exit;
        }
        
        // التحقق من التسجيل في الفعالية
        $stmt = $pdo->prepare("SELECT id FROM Registrations WHERE student_id = ? AND event_id = ?");
        $stmt->execute([$_SESSION['user_id'], $event_id]);
        if (!$stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'يجب التسجيل في الفعالية أولاً']);
            exit;
        }
        
        // التحقق من وجود تقييم سابق
        $stmt = $pdo->prepare("SELECT id FROM Reviews WHERE student_id = ? AND event_id = ?");
        $stmt->execute([$_SESSION['user_id'], $event_id]);
        $existing_review = $stmt->fetch();
        
        if ($existing_review) {
            // تحديث التقييم
            $stmt = $pdo->prepare("UPDATE Reviews SET rating = ?, comment = ? WHERE id = ?");
            $stmt->execute([$rating, $comment, $existing_review['id']]);
        } else {
            // إنشاء تقييم جديد
            $stmt = $pdo->prepare("INSERT INTO Reviews (student_id, event_id, rating, comment) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $event_id, $rating, $comment]);
        }
        
        echo json_encode(['success' => true, 'message' => 'تم حفظ التقييم بنجاح']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'خطأ في قاعدة البيانات']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $event_id = intval($_GET['event_id'] ?? 0);
    
    if ($event_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'معرف فعالية غير صحيح']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT r.*, u.email FROM Reviews r
                               JOIN Users u ON r.student_id = u.id
                               WHERE r.event_id = ?
                               ORDER BY r.created_at DESC");
        $stmt->execute([$event_id]);
        $reviews = $stmt->fetchAll();
        
        // حساب متوسط التقييم
        $stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews FROM Reviews WHERE event_id = ?");
        $stmt->execute([$event_id]);
        $stats = $stmt->fetch();
        
        echo json_encode([
            'success' => true,
            'reviews' => $reviews,
            'avg_rating' => round($stats['avg_rating'] ?? 0, 2),
            'total_reviews' => $stats['total_reviews'] ?? 0
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'خطأ في قاعدة البيانات']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'طريقة طلب غير صحيحة']);
}
?>
