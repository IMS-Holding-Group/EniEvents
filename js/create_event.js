document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('eventForm');
    const deleteBtn = document.getElementById('deleteBtn');
    const messageDiv = document.getElementById('message');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        saveEvent();
    });
    
    function saveEvent() {
        const formData = new FormData(form);
        const eventId = formData.get('event_id');
        
        // التصحيح: إذا كان الـ ID هو "0" أو فارغ، نرسل لـ create
        // وإذا كان أكبر من 0 نرسل لـ update
        const isUpdate = eventId && parseInt(eventId) > 0;
        const url = isUpdate ? '../api/update_event.php' : '../api/create_event.php';
        
        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messageDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                setTimeout(() => { window.location.href = 'home.php'; }, 1500);
            } else {
                messageDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
            }
        })
        .catch(error => {
            messageDiv.innerHTML = '<div class="alert alert-danger">حدث خطأ في الاتصال بالسيرفر</div>';
        });
    }

    // كود الحذف
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function() {
            if (!confirm('هل أنت متأكد من حذف هذه الفعالية؟')) return;
            
            const eventId = document.getElementById('event_id').value;
            const formData = new FormData();
            formData.append('event_id', eventId);
            
            fetch('../api/delete_event.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'home.php';
                } else {
                    alert(data.message);
                }
            });
        });
    }
});