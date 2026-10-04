<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json; charset=utf-8');

if (!isLoggedIn() || (!isOrganizer() && !isAdmin())) {
    echo json_encode(['success' => false, 'message' => 'غير مصرح']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $event_id = intval($_GET['event_id'] ?? 0);
    
    if ($event_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'معرف فعالية غير صحيح']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT r.id, r.student_id, u.email, r.registered_at,
                               COALESCE(a.attended, 0) as attended, a.attendance_time
                               FROM Registrations r
                               JOIN Users u ON r.student_id = u.id
                               LEFT JOIN Attendance a ON r.id = a.registration_id
                               WHERE r.event_id = ?
                               ORDER BY r.registered_at DESC");
        $stmt->execute([$event_id]);
        $registrations = $stmt->fetchAll();
        
        echo json_encode(['success' => true, 'registrations' => $registrations]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'خطأ في قاعدة البيانات']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $registration_id = intval($_POST['registration_id'] ?? 0);
    $attended = intval($_POST['attended'] ?? 0);
    
    if ($registration_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'معرف تسجيل غير صحيح']);
        exit;
    }
    
    try {
        // الحصول على معلومات التسجيل
        $stmt = $pdo->prepare("SELECT r.id, r.event_id, r.student_id FROM Registrations r WHERE r.id = ?");
        $stmt->execute([$registration_id]);
        $registration = $stmt->fetch();
        
        if (!$registration) {
            echo json_encode(['success' => false, 'message' => 'تسجيل غير موجود']);
            exit;
        }
        
        // التحقق من وجود سجل حضور
        $stmt = $pdo->prepare("SELECT id FROM Attendance WHERE registration_id = ?");
        $stmt->execute([$registration_id]);
        $attendance = $stmt->fetch();
        
        if ($attendance) {
            // تحديث سجل الحضور
            $stmt = $pdo->prepare("UPDATE Attendance SET attended = ?, attendance_time = ? WHERE id = ?");
            $stmt->execute([
                $attended ? 1 : 0,
                $attended ? date('Y-m-d H:i:s') : null,
                $attendance['id']
            ]);
        } else {
            // إنشاء سجل حضور جديد
            $stmt = $pdo->prepare("INSERT INTO Attendance (registration_id, event_id, student_id, attended, attendance_time) 
                                   VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $registration_id,
                $registration['event_id'],
                $registration['student_id'],
                $attended ? 1 : 0,
                $attended ? date('Y-m-d H:i:s') : null
            ]);
        }
        
        echo json_encode(['success' => true, 'message' => 'تم تحديث الحضور بنجاح']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'خطأ في قاعدة البيانات']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'طريقة طلب غير صحيحة']);
}
?>
