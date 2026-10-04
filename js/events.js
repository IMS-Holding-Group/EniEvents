document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('searchForm');
    const eventsContainer = document.getElementById('eventsContainer');
    
    loadEvents();
    
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        loadEvents();
    });
    
    function loadEvents() {
        const formData = new FormData(searchForm);
        const params = new URLSearchParams(formData);
        
        // تعديل المسار ليعمل من الصفحة الرئيسية
        fetch('api/events.php?' + params.toString())
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayEvents(data.events);
                } else {
                    eventsContainer.innerHTML = '<p>لا توجد فعاليات متاحة حالياً</p>';
                }
            })
            .catch(error => {
                eventsContainer.innerHTML = '<p>حدث خطأ أثناء الاتصال بالخادم</p>';
            });
    }
    
    function displayEvents(events) {
        if (events.length === 0) {
            eventsContainer.innerHTML = '<p>لا توجد نتائج تطابق بحثك</p>';
            return;
        }
        
        eventsContainer.innerHTML = events.map(event => `
            <div class="card">
                <h3>${escapeHtml(event.name)}</h3>
                <p><i class="fas fa-calendar"></i> ${formatDate(event.event_date)}</p>
                <p><i class="fas fa-map-marker-alt"></i> ${escapeHtml(event.location)}</p>
                <div class="card-footer">
                    <a href="pages/event_details.php?id=${event.id}" class="btn btn-primary">عرض التفاصيل</a>
                </div>
            </div>
        `).join('');
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('ar-SA');
    }
});