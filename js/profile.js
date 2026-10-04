document.addEventListener('DOMContentLoaded', function () {
    var body = document.body;
    var userType = body.getAttribute('data-user-type') || '';

    var nameForm = document.getElementById('profileNameForm');
    var nameMsg = document.getElementById('profileNameMessage');

    if (nameForm) {
        nameForm.addEventListener('submit', function (e) {
            e.preventDefault();
            if (nameMsg) {
                nameMsg.style.display = 'none';
            }
            var fd = new FormData(nameForm);
            fetch('../api/update_profile.php', { method: 'POST', body: fd })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (!nameMsg) return;
                    nameMsg.style.display = 'block';
                    nameMsg.textContent = data.message || '';
                    nameMsg.style.color = data.success ? 'var(--primary-color)' : '#dc2626';
                })
                .catch(function () {
                    if (!nameMsg) return;
                    nameMsg.style.display = 'block';
                    nameMsg.textContent = 'تعذر الاتصال بالخادم';
                    nameMsg.style.color = '#dc2626';
                });
        });
    }

    if (userType !== 'student') {
        return;
    }

    var container = document.getElementById('registrationsContainer');
    if (!container) return;

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatDate(dateString) {
        var date = new Date(dateString);
        return date.toLocaleString('ar-SA', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    fetch('../api/my_registrations.php')
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (!data.success) {
                container.innerHTML = '<p class="card-text">تعذر تحميل التسجيلات</p>';
                return;
            }
            var list = data.registrations || [];
            if (list.length === 0) {
                container.innerHTML = '<div class="card"><p class="card-text">لم تسجّل في أي فعالية بعد. تصفح <a href="../browse_events.php">قائمة الفعاليات</a> للاشتراك.</p></div>';
                return;
            }
            container.innerHTML = list.map(function (row) {
                return (
                    '<div class="card">' +
                    '<h4 class="card-title"><i class="fas fa-calendar-check"></i> ' + escapeHtml(row.name) + '</h4>' +
                    '<p class="card-text"><i class="fas fa-calendar"></i> <strong>التاريخ:</strong> ' + formatDate(row.event_date) + '</p>' +
                    '<p class="card-text"><i class="fas fa-map-marker-alt"></i> <strong>الموقع:</strong> ' + escapeHtml(row.location || '') + '</p>' +
                    '<p class="card-text"><i class="fas fa-chair"></i> <strong>المقاعد:</strong> ' + row.available_seats + ' / ' + row.total_seats + '</p>' +
                    '<p class="card-text"><i class="fas fa-user-check"></i> <strong>تاريخ تسجيلك:</strong> ' + formatDate(row.registered_at) + '</p>' +
                    '<div class="card-footer">' +
                    '<a href="event_details.php?id=' + encodeURIComponent(row.id) + '" class="btn btn-primary"><i class="fas fa-eye"></i> التفاصيل</a>' +
                    '</div>' +
                    '</div>'
                );
            }).join('');
        })
        .catch(function () {
            container.innerHTML = '<p class="card-text">حدث خطأ في الاتصال</p>';
        });
});
