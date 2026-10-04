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
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $event_date = $_POST['event_date'] ?? '';
    $location = $_POST['location'] ?? '';
    $total_seats = intval($_POST['total_seats'] ?? 0);
    $event_type = $_POST['event_type'] ?? '';
    
    if ($event_id <= 0 || empty($name) || empty($event_date) || empty($location)) {
        echo json_encode(['success' => false, 'message' => 'جميع الحقول مطلوبة']);
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
        
        // حساب المقاعد المتاحة
        $stmt = $pdo->prepare("SELECT COUNT(*) as registered FROM Registrations WHERE event_id = ?");
        $stmt->execute([$event_id]);
        $registered = $stmt->fetch()['registered'];
        $available_seats = max(0, $total_seats - $registered);
        
        $stmt = $pdo->prepare("UPDATE Events SET name = ?, description = ?, event_date = ?, location = ?, total_seats = ?, available_seats = ?, event_type = ? WHERE id = ?");
        $stmt->execute([
            $name,
            $description,
            $event_date,
            $location,
            $total_seats,
            $available_seats,
            $event_type,
            $event_id
        ]);
        
        echo json_encode(['success' => true, 'message' => 'تم تحديث الفعالية بنجاح']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'خطأ في قاعدة البيانات']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'طريقة طلب غير صحيحة']);
}
?>
