<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/auth.php';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نبذة عن المشروع - نظام إدارة الفعاليات</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php $APP_NAV_FROM_ROOT = false;
require __DIR__ . '/../includes/app_nav.php'; ?>

    <div class="container">
        <div class="card" style="max-width: 900px; margin: 0 auto;">
            <h2 style="margin-bottom: 25px; color: var(--primary-color);"><i class="fas fa-info-circle"></i> نبذة عن المشروع</h2>
            
            <p style="margin-bottom: 20px; font-size: 18px; line-height: 1.8;">
                نظام إدارة الفعاليات الجامعية هو نظام ويب متكامل يهدف إلى تسهيل تنظيم وإدارة الفعاليات داخل الجامعة.
            </p>
            
            <h3 style="margin: 25px 0 15px; color: var(--secondary-color);"><i class="fas fa-bullseye"></i> أهداف المشروع</h3>
            <ul style="margin-right: 25px; margin-bottom: 20px; line-height: 2;">
                <li>توفير منصة مركزية للتعريف بالفعاليات</li>
                <li>تمكين الطلاب من التسجيل في الفعاليات بسهولة</li>
                <li>تمكين المنظمين من إنشاء وإدارة الفعاليات</li>
                <li>تتبع الحضور وإدارة القوائم</li>
                <li>التقييمات والتعليقات بعد انتهاء الفعاليات</li>
            </ul>
            
            <h3 style="margin: 25px 0 15px; color: var(--secondary-color);"><i class="fas fa-users"></i> أنواع المستخدمين</h3>
            <ul style="margin-right: 25px; margin-bottom: 20px; line-height: 2;">
                <li><strong>طالب:</strong> تصفح الفعاليات والتسجيل فيها وإضافة التقييمات</li>
                <li><strong>منظم:</strong> إنشاء فعاليات وإدارة الحضور</li>
                <li><strong>أدمن:</strong> لوحة تحكم شاملة ومراقبة النظام</li>
            </ul>
            
            <h3 style="margin: 25px 0 15px; color: var(--secondary-color);"><i class="fas fa-cogs"></i> المميزات</h3>
            <ul style="margin-right: 25px; margin-bottom: 20px; line-height: 2;">
                <li>واجهة عربية كاملة</li>
                <li>دعم الوضع الفاتح والداكن</li>
                <li>تصميم متجاوب للجوال</li>
                <li>بحث وفلترة الفعاليات</li>
                <li>نظام تسجيل دخول وإنشاء حسابات</li>
            </ul>
            
            <div class="card-footer" style="margin-top: 30px;">
                <a href="../browse_events.php" class="btn btn-primary"><i class="fas fa-calendar-alt"></i> عرض الفعاليات</a>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <p>&copy; 2026 نظام إدارة الفعاليات الجامعية</p>
        </div>
    </footer>

    <script src="../js/theme.js"></script>
    <script src="../js/mobile-menu.js"></script>
</body>
</html>
