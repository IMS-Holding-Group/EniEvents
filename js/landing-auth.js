document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('landingAuthForm');
    const tabs = document.querySelectorAll('.landing-tab');
    const modeInput = document.getElementById('formMode');
    const submitBtn = document.getElementById('submitBtn');
    const messageDiv = document.getElementById('message');
    const userTypeGroup = document.getElementById('landingUserTypeGroup');

    function showMessage(text, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
        messageDiv.innerHTML = `<div class="alert ${alertClass}">${text}</div>`;
        setTimeout(function () { messageDiv.innerHTML = ''; }, 6000);
    }

    function redirectAfterLogin(userType) {
        if (userType === 'admin') {
            window.location.href = 'pages/dashboard.php';
        } else if (userType === 'organizer') {
            window.location.href = 'pages/home.php';
        } else {
            window.location.href = 'pages/profile.php';
        }
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            const mode = tab.getAttribute('data-mode');
            tabs.forEach(function (t) {
                t.classList.remove('is-active');
                t.setAttribute('aria-selected', 'false');
            });
            tab.classList.add('is-active');
            tab.setAttribute('aria-selected', 'true');
            modeInput.value = mode;
            if (mode === 'register') {
                if (userTypeGroup) userTypeGroup.style.display = '';
                submitBtn.innerHTML = '<i class="fas fa-check"></i> إنشاء حساب';
            } else {
                if (userTypeGroup) userTypeGroup.style.display = 'none';
                submitBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> تسجيل الدخول';
            }
        });
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const mode = modeInput.value;
        const fd = new FormData(form);
        fd.delete('mode');
        const url = mode === 'register' ? 'api/register.php' : 'api/login.php';
        fetch(url, { method: 'POST', body: fd })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.success) {
                    showMessage(data.message, 'success');
                    setTimeout(function () { redirectAfterLogin(data.user_type); }, 800);
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(function () {
                showMessage('تعذر الاتصال بالخادم', 'error');
            });
    });
});
