<?php
require_once 'config/session.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    if (isAdmin()) {
        header('Location: pages/dashboard.php');
        exit;
    }
    if (isOrganizer()) {
        header('Location: pages/home.php');
        exit;
    }
    header('Location: pages/profile.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام إدارة الفعاليات — الرئيسية</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="landing-body">
    <div class="landing-bg" aria-hidden="true">
        <div class="landing-orb landing-orb-1"></div>
        <div class="landing-orb landing-orb-2"></div>
        <div class="landing-orb landing-orb-3"></div>
        <div class="landing-grid"></div>
    </div>

    <div class="landing-topbar">
        <a href="pages/about.php" class="landing-link"><i class="fas fa-info-circle"></i> نبذة عن المشروع</a>
        <a href="browse_events.php" class="landing-link landing-link-accent"><i class="fas fa-calendar-alt"></i> تصفح الفعاليات</a>
        <button type="button" id="themeToggle" class="theme-toggle landing-theme"><i class="fas fa-moon"></i> وضع داكن</button>
    </div>

    <main class="landing-main">
        <div class="landing-brand landing-reveal">
            <div class="landing-logo-wrap">
                <img src="assets/logo.png" alt="" class="landing-logo-img">
            </div>
            <h1 class="landing-title">نظام إدارة الفعاليات</h1>
            <p class="landing-tagline">أنشئ حساباً كطالب أو كمنظم، أو سجّل الدخول ببريدك وكلمة المرور</p>
        </div>

        <div class="landing-card landing-reveal landing-reveal-delay">
            <div class="landing-tabs" role="tablist">
                <button type="button" class="landing-tab is-active" data-mode="register" role="tab" aria-selected="true">
                    <i class="fas fa-user-plus"></i> إنشاء حساب
                </button>
                <button type="button" class="landing-tab" data-mode="login" role="tab" aria-selected="false">
                    <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
                </button>
            </div>
            <div id="message"></div>
            <form id="landingAuthForm" autocomplete="on">
                <input type="hidden" name="mode" id="formMode" value="register">
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> البريد الإلكتروني</label>
                    <input type="email" id="email" name="email" required placeholder="أي بريد صالح (Gmail، Outlook، جامعي...)">
                </div>
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> كلمة المرور</label>
                    <input type="password" id="password" name="password" required minlength="6" placeholder="ست خانات على الأقل">
                </div>
                <div class="form-group" id="landingUserTypeGroup">
                    <label for="user_type"><i class="fas fa-users"></i> نوع الحساب</label>
                    <select id="user_type" name="user_type">
                        <option value="student">طالب</option>
                        <option value="organizer">منظم</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-block landing-submit" id="submitBtn">
                    <i class="fas fa-check"></i> إنشاء حساب
                </button>
            </form>
            <p class="landing-hint">
                <i class="fas fa-shield-alt"></i> عند إنشاء حساب جديد اختر <strong>طالباً</strong> أو <strong>منظماً</strong>. حساب المسؤول (أدمن) لا يُنشأ من هنا.
            </p>
        </div>
    </main>

    <footer class="landing-footer">
        <p>&copy; 2026 نظام إدارة الفعاليات الجامعية</p>
    </footer>

    <script src="js/theme.js"></script>
    <script src="js/landing-auth.js"></script>
</body>
</html>
