/**
 * ملف JavaScript لإدارة تبديل الوضع الداكن/الفاتح
 * 
 * يحفظ التفضيل في localStorage ليبقى محفوظاً بعد إعادة تحميل الصفحة
 */

/**
 * ملف JavaScript لإدارة تبديل الوضع الداكن/الفاتح
 * 
 * يحفظ التفضيل في localStorage ليبقى محفوظاً بعد إعادة تحميل الصفحة
 */

document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('themeToggle');
    const body = document.body;
    
    /**
     * دالة تحديث أيقونة الزر حسب الوضع الحالي
     * 
     * @param {boolean} isDark - true إذا كان الوضع داكن، false إذا كان فاتح
     * 
     * الشرح: النص يوضح الوضع الذي سيتم التبديل إليه (وليس الوضع الحالي)
     * - إذا كان الوضع الحالي داكن → الزر يكتب "وضع فاتح" (لأنه سيبدل للفاتح عند الضغط)
     * - إذا كان الوضع الحالي فاتح → الزر يكتب "وضع داكن" (لأنه سيبدل للداكن عند الضغط)
     */
    function updateToggleIcon(isDark) {
        if (themeToggle) {
            if (isDark) {
                // إذا كان الوضع الحالي داكن، الزر يكتب "وضع فاتح" (لأنه سيبدل للفاتح)
                themeToggle.innerHTML = '<i class="fas fa-sun"></i> وضع داكن';
            } else {
                // إذا كان الوضع الحالي فاتح، الزر يكتب "وضع داكن" (لأنه سيبدل للداكن)
                themeToggle.innerHTML = '<i class="fas fa-moon"></i> وضع فاتح';
            }
        }
    }
    
    // التحقق من التفضيل المحفوظ في localStorage
    // الوضع الفاتح هو الافتراضي - نفعّل الداكن فقط إذا كان المستخدم اختاره صراحة
    const savedTheme = localStorage.getItem('theme');
    const isDarkMode = savedTheme === 'dark';
    
    if (isDarkMode) {
        body.classList.add('dark-mode');
    } else {
        // الوضع الفاتح هو الافتراضي - إزالة أي dark-mode class موجود
        body.classList.remove('dark-mode');
    }
    
    // تحديث نص الزر حسب الوضع الحالي
    updateToggleIcon(isDarkMode);
    
    // عند النقر على زر التبديل
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            // تبديل class dark-mode على body
            body.classList.toggle('dark-mode');
            
            // التحقق من الوضع الجديد بعد التبديل
            const nowDark = body.classList.contains('dark-mode');
            
            // حفظ التفضيل في localStorage
            if (nowDark) {
                localStorage.setItem('theme', 'dark');
            } else {
                localStorage.setItem('theme', 'light');
            }
            
            // تحديث نص الزر حسب الوضع الجديد
            updateToggleIcon(nowDark);
        });
    }
});
