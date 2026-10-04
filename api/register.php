<?php
/**
 * ملف إنشاء حساب جديد (API Endpoint)
 * 
 * هذا الملف يتعامل مع طلبات إنشاء حسابات جديدة
 * يتحقق من عدم وجود البريد الإلكتروني مسبقاً ويشفر كلمة المرور
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // جلب البيانات من النموذج — نوع الحساب: طالب أو منظم فقط (لا يُسمح بإنشاء أدمن من الواجهة)
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $requested = trim($_POST['user_type'] ?? 'student');
    $user_type = in_array($requested, ['student', 'organizer'], true) ? $requested : 'student';
    
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'البريد الإلكتروني وكلمة المرور مطلوبان']);
        exit;
    }
    
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل']);
        exit;
    }
    
    try {
        // التحقق من عدم وجود البريد الإلكتروني مسبقاً
        // هذا مهم لمنع إنشاء حسابات مكررة
        $stmt = $pdo->prepare("SELECT id FROM Users WHERE email = ?");
        $stmt->execute([$email]);
        
        // إذا وجدنا نتيجة، البريد مستخدم بالفعل
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'البريد الإلكتروني مستخدم بالفعل']);
            exit;
        }
        
        // تشفير كلمة المرور قبل حفظها
        // password_hash() تستخدم خوارزمية آمنة (bcrypt)
        // PASSWORD_DEFAULT تستخدم أقوى خوارزمية متاحة
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // إدراج المستخدم الجديد في قاعدة البيانات
        $stmt = $pdo->prepare("INSERT INTO Users (email, password, user_type) VALUES (?, ?, ?)");
        $stmt->execute([$email, $hashed_password, $user_type]);
        
        // حفظ معلومات المستخدم في الجلسة
        // lastInsertId() يعيد معرف الصف الذي تم إدراجه للتو
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['user_type'] = $user_type;
        $_SESSION['email'] = $email;
        
        // إرجاع رسالة نجاح
        echo json_encode([
            'success' => true,
            'message' => 'تم إنشاء الحساب بنجاح',
            'user_type' => $user_type
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'خطأ في قاعدة البيانات']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'طريقة طلب غير صحيحة']);
}
?>
