/**
 * ملف JavaScript لإدارة قائمة الجوال (Mobile Menu)
 * 
 * يتحكم في إظهار وإخفاء القائمة على الشاشات الصغيرة
 */

/**
 * ملف JavaScript لإدارة قائمة الجوال (Mobile Menu)
 * 
 * يتحكم في إظهار وإخفاء القائمة على الشاشات الصغيرة
 */

document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menuToggle');
    const nav = document.getElementById('mainNav') || document.querySelector('nav');
    
    if (!menuToggle || !nav) return;
    
    // منع الانتشار عند النقر على الزر
    menuToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        e.preventDefault();
        
        // تبديل class mobile-active على nav
        nav.classList.toggle('mobile-active');
        
        // تغيير أيقونة الزر
        const icon = menuToggle.querySelector('i');
        if (icon) {
            if (nav.classList.contains('mobile-active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        }
    });
    
    // إغلاق القائمة عند النقر على رابط (وليس زر التبديل)
    const navLinks = nav.querySelectorAll('a');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                nav.classList.remove('mobile-active');
                const icon = menuToggle.querySelector('i');
                if (icon) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
        });
    });
    
    // إغلاق القائمة عند النقر خارجها
    document.addEventListener('click', function(event) {
        if (window.innerWidth <= 768 && nav.classList.contains('mobile-active')) {
            if (!nav.contains(event.target) && !menuToggle.contains(event.target)) {
                nav.classList.remove('mobile-active');
                const icon = menuToggle.querySelector('i');
                if (icon) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
        }
    });
    
    // إغلاق القائمة عند تغيير حجم النافذة
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (window.innerWidth > 768) {
                nav.classList.remove('mobile-active');
                const icon = menuToggle.querySelector('i');
                if (icon) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
        }, 100);
    });
    
    // منع التمرير عند فتح القائمة على الجوالات
    nav.addEventListener('touchmove', function(e) {
        if (nav.classList.contains('mobile-active') && window.innerWidth <= 768) {
            e.stopPropagation();
        }
    }, { passive: false });
});
