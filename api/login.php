<?php
/**
 * ملف تسجيل الدخول (API Endpoint)
 * 
 * هذا الملف يتعامل مع طلبات تسجيل الدخول من الواجهة الأمامية
 * يستقبل البيانات عبر POST ويرجع النتيجة بصيغة JSON
 */

// استيراد الملفات المطلوبة
require_once __DIR__ . '/../config/database.php';  // اتصال قاعدة البيانات
require_once __DIR__ . '/../config/session.php';  // بدء الجلسة

// تعيين نوع المحتوى المرسل كـ JSON (لأن هذا ملف API)
header('Content-Type: application/json; charset=utf-8');

// التحقق من أن الطلب هو POST (وليس GET)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // جلب البيانات من النموذج (Form)
    // ?? '' يعني: إذا لم تكن موجودة، استخدم قيمة فارغة
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'البريد الإلكتروني وكلمة المرور مطلوبان']);
        exit;
    }
    
    try {
        // تسجيل الدخول بالبريد فقط (أي نوع حساب مرتبط بهذا البريد)
        $stmt = $pdo->prepare("SELECT * FROM Users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();  // جلب النتيجة (صف واحد)
        
        // التحقق من وجود المستخدم وصحة كلمة المرور
        // password_verify() تقارن كلمة المرور المدخلة مع المشفرة في قاعدة البيانات
        if ($user && password_verify($password, $user['password'])) {
            
            // حفظ معلومات المستخدم في الجلسة (Session)
            // الجلسة تسمح بتذكر المستخدم أثناء تصفحه للموقع
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['email'] = $user['email'];
            
            // إرجاع رسالة نجاح
            echo json_encode([
                'success' => true,
                'message' => 'تم تسجيل الدخول بنجاح',
                'user_type' => $user['user_type']
            ]);
        } else {
            // كلمة المرور أو البريد الإلكتروني غير صحيح
            echo json_encode(['success' => false, 'message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة']);
        }
    } catch (PDOException $e) {
        // في حالة حدوث خطأ في قاعدة البيانات
        echo json_encode(['success' => false, 'message' => 'خطأ في قاعدة البيانات']);
    }
} else {
    // إذا كان الطلب ليس POST
    echo json_encode(['success' => false, 'message' => 'طريقة طلب غير صحيحة']);
}
?>
