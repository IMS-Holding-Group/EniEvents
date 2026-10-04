<?php
/**
 * ملف المصادقة والتحقق من المستخدمين
 * 
 * يحتوي على دوال (Functions) للتحقق من حالة تسجيل الدخول ونوع المستخدم
 * هذه الدوال تستخدم في جميع صفحات النظام لحماية الصفحات والتحقق من الصلاحيات
 */

// استيراد الملفات المطلوبة
require_once __DIR__ . '/../config/session.php';    // ملف بدء الجلسة
require_once __DIR__ . '/../config/database.php';  // ملف اتصال قاعدة البيانات

/**
 * التحقق من تسجيل الدخول
 * 
 * @return bool - يرجع true إذا كان المستخدم مسجل دخول، false إذا لم يكن
 * 
 * الشرح: هذه الدالة تتحقق من وجود معرف المستخدم في الجلسة (Session)
 * الجلسة هي طريقة لتخزين معلومات المستخدم أثناء تصفحه للموقع
 */
function isLoggedIn() {
    // isset() تتحقق من وجود المتغير
    // $_SESSION['user_id'] يحتوي على معرف المستخدم المسجل دخوله
    return isset($_SESSION['user_id']);
}

/**
 * الحصول على معلومات المستخدم الحالي
 * 
 * @return array|null - معلومات المستخدم من قاعدة البيانات أو null إذا لم يكن مسجل دخول
 * 
 * الشرح: تجلب هذه الدالة جميع معلومات المستخدم من قاعدة البيانات
 * بناءً على معرفه المخزن في الجلسة
 */
function getCurrentUser() {
    // التحقق أولاً من تسجيل الدخول
    if (!isLoggedIn()) {
        return null;  // إذا لم يكن مسجل دخول، نرجع null
    }
    
    // استخدام المتغير العام $pdo من ملف database.php
    global $pdo;
    
    // إعداد استعلام SQL للبحث عن المستخدم
    // ? هو placeholder (مكان مؤقت) للقيمة - هذا يمنع SQL Injection
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE id = ?");
    
    // تنفيذ الاستعلام مع تمرير معرف المستخدم
    $stmt->execute([$_SESSION['user_id']]);
    
    // جلب النتيجة (صف واحد فقط)
    return $stmt->fetch();
}

/**
 * التحقق من أن المستخدم الحالي هو أدمن
 * 
 * @return bool - true إذا كان أدمن، false إذا لم يكن
 */
function isAdmin() {
    $user = getCurrentUser();  // جلب معلومات المستخدم
    // التحقق من وجود المستخدم وأن نوعه 'admin'
    return $user && $user['user_type'] === 'admin';
}

/**
 * التحقق من أن المستخدم الحالي هو منظم
 * 
 * @return bool - true إذا كان منظم، false إذا لم يكن
 */
function isOrganizer() {
    $user = getCurrentUser();
    return $user && $user['user_type'] === 'organizer';
}

/**
 * التحقق من أن المستخدم الحالي هو طالب
 * 
 * @return bool - true إذا كان طالب، false إذا لم يكن
 */
function isStudent() {
    $user = getCurrentUser();
    return $user && $user['user_type'] === 'student';
}

/**
 * تسجيل الخروج
 * 
 * الشرح: هذه الدالة تقوم بحذف جميع بيانات الجلسة وإعادة توجيه المستخدم لصفحة تسجيل الدخول
 */
function logout() {
    session_destroy();  // حذف جميع بيانات الجلسة
    header('Location: ../index.php');  // إعادة التوجيه لصفحة تسجيل الدخول
    exit;  // إيقاف تنفيذ الكود
}
?>
