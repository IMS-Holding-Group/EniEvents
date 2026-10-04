<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/auth.php';

if (!isLoggedIn()) {
    header('Location: ../index.php');
    exit;
}

$user = getCurrentUser();
if (!$user || !in_array($user['user_type'], ['student', 'organizer', 'admin'], true)) {
    header('Location: ../index.php');
    exit;
}

$typeLabels = [
    'student' => 'طالب',
    'organizer' => 'منظم',
    'admin' => 'مدير',
];
$typeLabel = $typeLabels[$user['user_type']] ?? $user['user_type'];

if ($user['user_type'] === 'organizer') {
    $pageTitle = 'الملف الشخصي - نظام إدارة الفعاليات';
    $profileHeading = 'الملف الشخصي';
} elseif ($user['user_type'] === 'student') {
    $pageTitle = 'ملفي الشخصي - نظام إدارة الفعاليات';
    $profileHeading = 'ملفي الشخصي';
} else {
    $pageTitle = 'الملف الشخصي - نظام إدارة الفعاليات';
    $profileHeading = 'الملف الشخصي';
}

$fullName = isset($user['full_name']) ? $user['full_name'] : '';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body data-user-type="<?php echo htmlspecialchars($user['user_type']); ?>">
<?php $APP_NAV_FROM_ROOT = false;
require __DIR__ . '/../includes/app_nav.php'; ?>

    <div class="container">
        <h2 style="margin-bottom: 24px;"><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($profileHeading); ?></h2>

        <div class="card" style="margin-bottom: 24px;">
            <h3 class="card-title"><i class="fas fa-id-badge"></i> بيانات الحساب</h3>
            <form id="profileNameForm" class="profile-name-form" style="margin-bottom: 16px;">
                <div class="form-group">
                    <label for="full_name"><i class="fas fa-signature"></i> الاسم</label>
                    <input type="text" id="full_name" name="full_name" maxlength="255"
                           value="<?php echo htmlspecialchars((string) $fullName); ?>"
                           placeholder="اكتب اسمك الظاهر في النظام">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ الاسم</button>
            </form>
            <p id="profileNameMessage" class="card-text" style="display:none;"></p>
            <p class="card-text"><i class="fas fa-envelope"></i> <strong>البريد الإلكتروني:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p class="card-text"><i class="fas fa-user-tag"></i> <strong>نوع الحساب:</strong> <?php echo htmlspecialchars($typeLabel); ?></p>
            <p class="card-text"><i class="fas fa-clock"></i> <strong>تاريخ إنشاء الحساب:</strong> <?php echo htmlspecialchars($user['created_at'] ?? '—'); ?></p>
        </div>

        <?php if ($user['user_type'] === 'student'): ?>
        <h3 style="margin-bottom: 16px; color: var(--primary-color);"><i class="fas fa-bookmark"></i> الفعاليات / الدورات المسجّل بها</h3>
        <div id="registrationsContainer">
            <p class="card-text"><i class="fas fa-spinner fa-spin"></i> جاري التحميل...</p>
        </div>
        <?php elseif ($user['user_type'] === 'organizer'): ?>
        <div class="card">
            <p class="card-text"><i class="fas fa-calendar-plus"></i> لإدارة فعالياتك وإنشاء فعالية جديدة، استخدم <strong>الفعاليات</strong> و<strong>إنشاء فعالية</strong> من الشريط العلوي.</p>
        </div>
        <?php endif; ?>

        <div style="margin-top: 28px;">
            <a href="../api/logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a>
            <a href="../browse_events.php" class="btn btn-secondary" style="margin-right: 12px;"><i class="fas fa-calendar-alt"></i> تصفح الفعاليات</a>
        </div>
    </div>

    <footer>
        <div class="container">
            <p>&copy; 2026 نظام إدارة الفعاليات الجامعية</p>
        </div>
    </footer>

    <script src="../js/theme.js"></script>
    <script src="../js/mobile-menu.js"></script>
    <script src="../js/profile.js"></script>
</body>
</html>
