<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json; charset=utf-8');

// حماية: التأكد من الصلاحيات
if (!isLoggedIn() || !isOrganizer()) {
    echo json_encode(['success' => false, 'message' => 'غير مصرح']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // استلام البيانات
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $event_date = $_POST['event_date'] ?? '';
    $location = trim($_POST['location'] ?? '');
    $total_seats = intval($_POST['total_seats'] ?? 0);
    $event_type = $_POST['event_type'] ?? '';

    // التحقق من الحقول (تأكد أن الأسماء تطابق الـ HTML)
    if (empty($name) || empty($event_date) || empty($location) || $total_seats <= 0) {
        echo json_encode(['success' => false, 'message' => 'جميع الحقول مطلوبة، تأكد من إدخال عدد مقاعد أكبر من صفر']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO Events (organizer_id, name, description, event_date, location, total_seats, available_seats, event_type) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $_SESSION['user_id'],
            $name,
            $description,
            $event_date,
            $location,
            $total_seats,
            $total_seats, // المتاحة = الإجمالية
            $event_type
        ]);

        echo json_encode(['success' => true, 'message' => 'تم إنشاء الفعالية بنجاح ✅']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'خطأ قاعدة بيانات: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'طلب غير صالح']);
}