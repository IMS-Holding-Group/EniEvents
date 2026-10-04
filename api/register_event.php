<?php
/**
 * ملف التسجيل في فعالية (API Endpoint)
 * 
 * هذا الملف يتعامل مع طلبات تسجيل الطلاب في الفعاليات
 * يستخدم Transactions لضمان تحديث البيانات بشكل صحيح
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json; charset=utf-8');

// التحقق من تسجيل الدخول وأن المستخدم طالب
// فقط الطلاب يمكنهم التسجيل في الفعاليات
if (!isLoggedIn() || !isStudent()) {
    echo json_encode(['success' => false, 'message' => 'غير مصرح']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // جلب معرف الفعالية وتحويله لرقم صحيح
    // intval() تحول النص لرقم صحيح
    $event_id = intval($_POST['event_id'] ?? 0);
    
    // التحقق من صحة المعرف
    if ($event_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'معرف فعالية غير صحيح']);
        exit;
    }
    
    try {
        // التحقق من وجود الفعالية والمقاعد المتاحة
        $stmt = $pdo->prepare("SELECT available_seats FROM Events WHERE id = ?");
        $stmt->execute([$event_id]);
        $event = $stmt->fetch();
        
        if (!$event) {
            echo json_encode(['success' => false, 'message' => 'الفعالية غير موجودة']);
            exit;
        }
        
        // التحقق من وجود مقاعد متاحة
        if ($event['available_seats'] <= 0) {
            echo json_encode(['success' => false, 'message' => 'لا توجد مقاعد متاحة']);
            exit;
        }
        
        // التحقق من عدم التسجيل مسبقاً
        // هذا يمنع التسجيل المكرر
        $stmt = $pdo->prepare("SELECT id FROM Registrations WHERE student_id = ? AND event_id = ?");
        $stmt->execute([$_SESSION['user_id'], $event_id]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'أنت مسجل بالفعل في هذه الفعالية']);
            exit;
        }
        
        /**
         * استخدام Transactions (المعاملات)
         * 
         * Transaction تضمن تنفيذ عدة عمليات معاً أو لا شيء
         * إذا فشلت عملية واحدة، يتم التراجع عن كل شيء
         * 
         * لماذا مهم هنا؟
         * - نضيف تسجيل جديد
         * - ننقص المقاعد المتاحة
         * - إذا فشلت إحداهما، يجب التراجع عن الأخرى
         */
        $pdo->beginTransaction();  // بدء المعاملة
        
        // 1. إضافة تسجيل جديد
        $stmt = $pdo->prepare("INSERT INTO Registrations (student_id, event_id) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $event_id]);
        
        // 2. تقليل المقاعد المتاحة
        $stmt = $pdo->prepare("UPDATE Events SET available_seats = available_seats - 1 WHERE id = ?");
        $stmt->execute([$event_id]);
        
        // إذا وصلنا هنا، كل شيء نجح
        $pdo->commit();  // تأكيد التغييرات
        
        echo json_encode(['success' => true, 'message' => 'تم التسجيل في الفعالية بنجاح']);
    } catch (PDOException $e) {
        // في حالة حدوث خطأ، التراجع عن كل التغييرات
        $pdo->rollBack();  // إلغاء التغييرات
        echo json_encode(['success' => false, 'message' => 'خطأ في قاعدة البيانات']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'طريقة طلب غير صحيحة']);
}
?>
