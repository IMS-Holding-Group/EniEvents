<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/auth.php';

if (!isLoggedIn()) {
    header('Location: ../index.php');
    exit;
}

if (isStudent()) {
    header('Location: profile.php');
    exit;
}

$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جميع الفعاليات - نظام إدارة الفعاليات</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php $APP_NAV_FROM_ROOT = false;
require __DIR__ . '/../includes/app_nav.php'; ?>

    <div class="container">
        <h2 style="margin-bottom: 20px;"><i class="fas fa-user-circle"></i> مرحباً، <?php echo htmlspecialchars($user['email']); ?></h2>
        
        <div class="search-filter">
            <form id="searchForm">
                <input type="text" id="search" name="search" placeholder="🔍 ابحث عن فعالية...">
                <select id="event_type" name="event_type">
                    <option value="">جميع الأنواع</option>
                    <option value="workshop">ورشة عمل</option>
                    <option value="lecture">محاضرة</option>
                    <option value="conference">مؤتمر</option>
                    <option value="seminar">ندوة</option>
                    <option value="other">أخرى</option>
                </select>
                <select id="date_filter" name="date_filter">
                    <option value="">جميع الفعاليات</option>
                    <option value="upcoming">قادمة</option>
                    <option value="past">منتهية</option>
                </select>
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> بحث</button>
            </form>
        </div>

        <h3 style="margin-bottom: 20px; color: var(--primary-color);"><i class="fas fa-calendar-check"></i> الفعاليات القادمة</h3>
        <div id="eventsContainer" class="events-grid"></div>
    </div>

    <footer>
        <div class="container">
            <p>&copy; 2026 نظام إدارة الفعاليات الجامعية</p>
        </div>
    </footer>

    <script src="../js/theme.js"></script>
    <script src="../js/mobile-menu.js"></script>
    <script src="../js/home.js"></script>
</body>
</html>
