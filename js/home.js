/**
 * ملف JavaScript للصفحة الرئيسية
 * 
 * هذا الملف يعرض الفعاليات القادمة ويسمح بالبحث والفلترة
 */

// انتظار تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    // الحصول على العناصر من الصفحة
    const searchForm = document.getElementById('searchForm');      // نموذج البحث
    const eventsContainer = document.getElementById('eventsContainer'); // مكان عرض الفعاليات
    
    // تحميل الفعاليات عند فتح الصفحة
    loadEvents();
    
    // عند إرسال نموذج البحث، تحميل الفعاليات مرة أخرى
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();  // منع إرسال النموذج بالطريقة العادية
        loadEvents();        // تحميل الفعاليات مع معايير البحث
    });
    
    /**
     * دالة تحميل الفعاليات من الخادم
     * 
     * الشرح: ترسل معايير البحث للخادم وتحصل على الفعاليات المناسبة
     */
    function loadEvents() {
        // جمع بيانات النموذج
        const formData = new FormData(searchForm);
        
        // URLSearchParams تحول البيانات إلى صيغة URL
        // مثال: ?search=ورشة&event_type=workshop
        const params = new URLSearchParams(formData);
        
        // إرسال طلب GET للخادم
        // ? + params.toString() يضيف معايير البحث للرابط
        fetch('../api/events.php?' + params.toString())
            .then(response => response.json())  // تحويل الاستجابة لـ JSON
            .then(data => {
                if (data.success) {
                    // filter() ترشح الفعاليات - نعرض فقط القادمة
                    const upcomingEvents = data.events.filter(event => {
                        const eventDate = new Date(event.event_date);  // تحويل التاريخ
                        return eventDate >= new Date();  // فقط الفعاليات القادمة
                    });
                    displayEvents(upcomingEvents);  // عرض الفعاليات
                } else {
                    eventsContainer.innerHTML = '<p>حدث خطأ أثناء تحميل الفعاليات</p>';
                }
            })
            .catch(error => {
                eventsContainer.innerHTML = '<p>حدث خطأ أثناء تحميل الفعاليات</p>';
            });
    }
    
    /**
     * دالة عرض الفعاليات في الصفحة
     * 
     * @param {Array} events - مصفوفة الفعاليات
     * 
     * الشرح: تأخذ مصفوفة الفعاليات وتعرضها ككروت (Cards) في الصفحة
     */
    function displayEvents(events) {
        // إذا لم توجد فعاليات
        if (events.length === 0) {
            eventsContainer.innerHTML = '<p>لا توجد فعاليات قادمة</p>';
            return;
        }
        
        // map() تمر على كل فعالية وتحولها لـ HTML
        // join('') يجمع كل الكروت في نص واحد
        eventsContainer.innerHTML = events.map(event => `
            <div class="card">
                <h3 class="card-title"><i class="fas fa-calendar-check"></i> ${escapeHtml(event.name)}</h3>
                <p class="card-text"><i class="fas fa-calendar"></i> <strong>التاريخ:</strong> ${formatDate(event.event_date)}</p>
                <p class="card-text"><i class="fas fa-map-marker-alt"></i> <strong>الموقع:</strong> ${escapeHtml(event.location)}</p>
                <p class="card-text"><i class="fas fa-chair"></i> <strong>المقاعد المتاحة:</strong> ${event.available_seats} / ${event.total_seats}</p>
                <div class="card-footer">
                    <a href="event_details.php?id=${event.id}" class="btn btn-primary"><i class="fas fa-eye"></i> عرض التفاصيل</a>
                </div>
            </div>
        `).join('');
    }
    
    /**
     * دالة حماية من XSS (Cross-Site Scripting)
     * 
     * @param {string} text - النص المراد تنظيفه
     * @returns {string} - النص الآمن
     * 
     * الشرح: تمنع إدراج كود JavaScript خبيث في الصفحة
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;  // textContent يضيف النص كـ نص عادي (ليس HTML)
        return div.innerHTML;     // جلب النص الآمن
    }
    
    /**
     * دالة تنسيق التاريخ
     * 
     * @param {string} dateString - التاريخ بصيغة نصية
     * @returns {string} - التاريخ المنسق
     * 
     * الشرح: تحول التاريخ من صيغة قاعدة البيانات إلى صيغة مقروءة
     */
    function formatDate(dateString) {
        const date = new Date(dateString);
        // toLocaleString() تنسق التاريخ حسب اللغة المحددة
        return date.toLocaleString('ar-SA', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
});
