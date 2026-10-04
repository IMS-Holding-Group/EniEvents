<?php
/**
 * ملف اتصال قاعدة البيانات
 * 
 * هذا الملف يحتوي على إعدادات الاتصال بقاعدة البيانات MySQL
 * يتم استخدام PDO (PHP Data Objects) للاتصال بقاعدة البيانات بشكل آمن
 */

// إعدادات الاتصال بقاعدة البيانات
$host = 'localhost';        // عنوان الخادم (عادة localhost للتطوير المحلي)
$dbname = 'eni_events';     // اسم قاعدة البيانات
$username = 'root';         // اسم المستخدم لقاعدة البيانات
$password = '';             // كلمة مرور قاعدة البيانات (فارغة افتراضياً في XAMPP)

try {
    // إنشاء اتصال جديد بقاعدة البيانات باستخدام PDO
    // PDO يوفر طريقة آمنة للتعامل مع قاعدة البيانات
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // تعيين وضع الأخطاء: عند حدوث خطأ، يتم رمي استثناء (Exception)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // تعيين طريقة جلب البيانات: يتم جلب البيانات كمصفوفة ترابطية (Associative Array)
    // مثال: $row['name'] بدلاً من $row[0]
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    // في حالة فشل الاتصال، يتم إيقاف البرنامج وعرض رسالة الخطأ
    die("خطأ في الاتصال: " . $e->getMessage());
}
?>
