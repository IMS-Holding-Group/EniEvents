<?php
/**
 * شريط تنقل موحّد — يُضمَّن بعد تحميل auth.php
 * يعرّف قبل الضمّ: $APP_NAV_FROM_ROOT = true من جذر المشروع، وإلا من مجلد pages/
 */
if (!function_exists('isLoggedIn')) {
    require_once __DIR__ . '/auth.php';
}

if (!function_exists('app_nav_url')) {
    function app_nav_url(string $pathFromRoot): string
    {
        $fromRoot = !empty($GLOBALS['APP_NAV_FROM_ROOT']);
        if ($fromRoot) {
            return $pathFromRoot;
        }
        if (strncmp($pathFromRoot, 'pages/', 6) === 0) {
            return substr($pathFromRoot, 6);
        }
        return '../' . $pathFromRoot;
    }
}

$h = app_nav_url('index.php');
$about = app_nav_url('pages/about.php');
$browse = app_nav_url('browse_events.php');
$profile = app_nav_url('pages/profile.php');
$create = app_nav_url('pages/create_event.php');
$dash = app_nav_url('pages/dashboard.php');
$logout = app_nav_url('api/logout.php');
$login = app_nav_url('pages/login.php');
$logo = app_nav_url('assets/logo.png');
?>
<header>
    <div class="container header-bar">
        <div class="logo">
            <a href="<?php echo htmlspecialchars($h); ?>" style="display:flex;align-items:center;gap:10px;text-decoration:none;color:inherit;" aria-label="الرئيسية">
                <img src="<?php echo htmlspecialchars($logo); ?>" alt="">
            </a>
        </div>
        <button id="menuToggle" class="menu-toggle" aria-label="قائمة" type="button">
            <i class="fas fa-bars"></i>
        </button>
        <nav id="mainNav" class="main-nav-wrap" aria-label="القائمة الرئيسية">
            <ul class="main-nav-list">
                <li><a href="<?php echo htmlspecialchars($about); ?>"><i class="fas fa-info-circle"></i> نبذة عن المشروع</a></li>
                <li><a href="<?php echo htmlspecialchars($browse); ?>"><i class="fas fa-calendar-alt"></i> الفعاليات</a></li>
                <?php if (!isLoggedIn()): ?>
                    <li><a href="<?php echo htmlspecialchars($h); ?>"><i class="fas fa-home"></i> الرئيسية</a></li>
                    <li><a href="<?php echo htmlspecialchars($login); ?>"><i class="fas fa-sign-in-alt"></i> تسجيل الدخول</a></li>
                <?php elseif (isOrganizer()): ?>
                    <li><a href="<?php echo htmlspecialchars($profile); ?>"><i class="fas fa-id-card"></i> الملف الشخصي</a></li>
                    <li><a href="<?php echo htmlspecialchars($create); ?>"><i class="fas fa-plus-circle"></i> إنشاء فعالية</a></li>
                    <li><a href="<?php echo htmlspecialchars($logout); ?>"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a></li>
                <?php elseif (isStudent()): ?>
                    <li><a href="<?php echo htmlspecialchars($h); ?>"><i class="fas fa-home"></i> الرئيسية</a></li>
                    <li><a href="<?php echo htmlspecialchars($profile); ?>"><i class="fas fa-id-card"></i> ملفي الشخصي</a></li>
                    <li><a href="<?php echo htmlspecialchars($logout); ?>"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a></li>
                <?php elseif (isAdmin()): ?>
                    <li><a href="<?php echo htmlspecialchars($dash); ?>"><i class="fas fa-tachometer-alt"></i> لوحة التحكم</a></li>
                    <li><a href="<?php echo htmlspecialchars($logout); ?>"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a></li>
                <?php else: ?>
                    <li><a href="<?php echo htmlspecialchars($h); ?>"><i class="fas fa-home"></i> الرئيسية</a></li>
                    <li><a href="<?php echo htmlspecialchars($logout); ?>"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a></li>
                <?php endif; ?>
                <li class="nav-theme-item"><button id="themeToggle" type="button" class="theme-toggle"><i class="fas fa-moon"></i> وضع داكن</button></li>
            </ul>
        </nav>
    </div>
</header>
