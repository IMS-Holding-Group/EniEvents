<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/auth.php';

if (isLoggedIn()) {
    if (isAdmin()) {
        header('Location: dashboard.php');
        exit;
    }
    if (isOrganizer()) {
        header('Location: home.php');
        exit;
    }
    header('Location: profile.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - نظام إدارة الفعاليات</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body style="display: flex; flex-direction: column; min-height: 100vh;">
    <div style="position: fixed; top: 20px; left: 20px; z-index: 1000;">
        <a href="../index.php" class="btn btn-secondary" style="text-decoration: none;"><i class="fas fa-home"></i> الرئيسية</a>
    </div>
    <div style="position: fixed; top: 20px; right: 20px; z-index: 1000;">
        <button id="themeToggle" class="theme-toggle">
            <i class="fas fa-moon"></i> وضع داكن
        </button>
    </div>
    
    <div class="form-container" style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center;">
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="../assets/logo.png" alt="Logo" style="height: 80px; width: auto; margin-bottom: 20px;">
            
            <h2 style="color: var(--primary-color); cursor: default;">
                <span id="loginLink" style="cursor: pointer;"><i class="fas fa-sign-in-alt"></i> تسجيل الدخول</span> 
                <span style="color: #ccc;"> / </span>
                <span id="registerLink" style="cursor: pointer;"><i class="fas fa-user-plus"></i> إنشاء حساب</span>
            </h2>
        </div>
        
        <div id="message"></div>
        
        <form id="authForm">
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> البريد الإلكتروني:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> كلمة المرور:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="user_type"><i class="fas fa-users"></i> نوع الحساب (عند إنشاء حساب جديد):</label>
                <select id="user_type" name="user_type">
                    <option value="student">طالب</option>
                    <option value="organizer">منظم</option>
                </select>
            </div>
            
            <p style="text-align: center; color: #888; margin-top: 15px; font-size: 0.85rem;">
                تسجيل الدخول بالبريد وكلمة المرور. عند <strong>إنشاء حساب</strong> اختر طالباً أو منظماً من القائمة أعلاه.
            </p>
        </form>
    </div>

    <footer>
        <div class="container">
            <p>&copy; 2026 نظام إدارة الفعاليات الجامعية</p>
        </div>
    </footer>

    <script src="../js/theme.js"></script>
    <script src="../js/auth.js"></script>
</body>
</html>
