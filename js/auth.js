document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('authForm');
    const loginLink = document.getElementById('loginLink');
    const registerLink = document.getElementById('registerLink');
    const messageDiv = document.getElementById('message');

    function showMessage(text, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
        messageDiv.innerHTML = `<div class="alert ${alertClass}">${text}</div>`;
        setTimeout(() => { messageDiv.innerHTML = ''; }, 5000);
    }

    function getRedirectUrl(userType) {
        const params = new URLSearchParams(window.location.search);
        const redirect = params.get('redirect');
        if (redirect) {
            return redirect;
        }
        if (userType === 'admin') {
            return 'dashboard.php';
        }
        if (userType === 'organizer') {
            return 'home.php';
        }
        return 'profile.php';
    }

    // عمل رابط تسجيل الدخول
    loginLink.addEventListener('click', function() {
        const formData = new FormData(form);
        fetch('../api/login.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                showMessage(data.message, 'success');
                setTimeout(() => { window.location.href = getRedirectUrl(data.user_type); }, 1000);
            } else {
                showMessage(data.message, 'error');
            }
        });
    });

    // عمل رابط إنشاء الحساب (نوع الحساب من حقل user_type في النموذج)
    registerLink.addEventListener('click', function() {
        const formData = new FormData(form);
        fetch('../api/register.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                showMessage(data.message, 'success');
                setTimeout(() => { window.location.href = getRedirectUrl(data.user_type); }, 1000);
            } else {
                showMessage(data.message, 'error');
            }
        });
    });
});