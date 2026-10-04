document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('themeToggle');
    const body = document.body;
    
    function updateToggleIcon(isDark) {
        if (themeToggle) {
            if (isDark) {
                themeToggle.innerHTML = '<i class="fas fa-sun"></i> وضع فاتح';
            } else {
                themeToggle.innerHTML = '<i class="fas fa-moon"></i> وضع داكن';
            }
        }
    }
    
    const savedTheme = localStorage.getItem('theme');
    const isDarkMode = savedTheme === 'dark';
    
    if (isDarkMode) {
        body.classList.add('dark-mode');
    } else {
        body.classList.remove('dark-mode');
    }
    
    updateToggleIcon(isDarkMode);
    
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            body.classList.toggle('dark-mode');
            const nowDark = body.classList.contains('dark-mode');
            if (nowDark) {
                localStorage.setItem('theme', 'dark');
            } else {
                localStorage.setItem('theme', 'light');
            }
            updateToggleIcon(nowDark);
        });
    }
});
