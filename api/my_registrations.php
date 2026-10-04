<?php
/**
 * قائمة الفعاليات المسجّل بها الطالب الحالي
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json; charset=utf-8');

if (!isLoggedIn() || !isStudent()) {
    echo json_encode(['success' => false, 'message' => 'غير مصرح']);
    exit;
}

try {
    $stmt = $pdo->prepare(
        "SELECT e.id, e.name, e.event_date, e.location, e.event_type, e.available_seats, e.total_seats,
                r.registered_at
         FROM Registrations r
         INNER JOIN Events e ON e.id = r.event_id
         WHERE r.student_id = ?
         ORDER BY e.event_date DESC"
    );
    $stmt->execute([$_SESSION['user_id']]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'registrations' => $rows]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'خطأ في قاعدة البيانات']);
}
