<?php
/**
 * تحديث الاسم الظاهر للمستخدم الحالي (طالب أو منظم)
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'طريقة طلب غير صحيحة']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'يجب تسجيل الدخول']);
    exit;
}

$full_name = trim($_POST['full_name'] ?? '');
if (strlen($full_name) > 255) {
    echo json_encode(['success' => false, 'message' => 'الاسم طويل جداً']);
    exit;
}

try {
    $stmt = $pdo->prepare('UPDATE Users SET full_name = ? WHERE id = ?');
    $stmt->execute([$full_name === '' ? null : $full_name, $_SESSION['user_id']]);
    echo json_encode(['success' => true, 'message' => 'تم حفظ الاسم', 'full_name' => $full_name]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'خطأ في قاعدة البيانات']);
}
