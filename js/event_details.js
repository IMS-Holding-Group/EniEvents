// تفاصيل الفعالية
document.addEventListener('DOMContentLoaded', function() {
    const registerBtn = document.getElementById('registerBtn');
    const unregisterBtn = document.getElementById('unregisterBtn');
    const reviewForm = document.getElementById('reviewForm');
    const messageDiv = document.getElementById('message');
    
    loadReviews();
    
    if (registerBtn) {
        registerBtn.addEventListener('click', function() {
            const eventId = this.getAttribute('data-event-id');
            registerForEvent(eventId);
        });
    }
    
    if (unregisterBtn) {
        unregisterBtn.addEventListener('click', function() {
            const eventId = this.getAttribute('data-event-id');
            unregisterFromEvent(eventId);
        });
    }
    
    if (reviewForm) {
        reviewForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitReview();
        });
    }
    
    function registerForEvent(eventId) {
        const formData = new FormData();
        formData.append('event_id', eventId);
        
        fetch('../api/register_event.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            showMessage(data.message, data.success ? 'success' : 'error');
            if (data.success) {
                setTimeout(() => {
                    location.reload();
                }, 1500);
            }
        })
        .catch(error => {
            showMessage('حدث خطأ أثناء التسجيل', 'error');
        });
    }
    
    function unregisterFromEvent(eventId) {
        if (!confirm('هل أنت متأكد من إلغاء التسجيل؟')) {
            return;
        }
        
        const formData = new FormData();
        formData.append('event_id', eventId);
        
        fetch('../api/unregister_event.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            showMessage(data.message, data.success ? 'success' : 'error');
            if (data.success) {
                setTimeout(() => {
                    location.reload();
                }, 1500);
            }
        })
        .catch(error => {
            showMessage('حدث خطأ أثناء إلغاء التسجيل', 'error');
        });
    }
    
    function loadReviews() {
        const urlParams = new URLSearchParams(window.location.search);
        const eventId = urlParams.get('id');
        
        if (!eventId) return;
        
        fetch('../api/review.php?event_id=' + eventId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayReviews(data.reviews, data.avg_rating, data.total_reviews);
                }
            })
            .catch(error => {
                console.error('Error loading reviews:', error);
            });
    }
    
    function displayReviews(reviews, avgRating, totalReviews) {
        const container = document.getElementById('reviewsContainer');
        
        if (totalReviews === 0) {
            container.innerHTML = '<p>لا توجد تقييمات بعد</p>';
            return;
        }
        
        let html = `<div style="margin-bottom: 20px;">
            <p><i class="fas fa-star"></i> <strong>متوسط التقييم:</strong> ${avgRating.toFixed(1)} / 5.0 (${totalReviews} تقييم)</p>
        </div>`;
        
        reviews.forEach(review => {
            html += `
                <div style="padding: 15px; margin-bottom: 15px; background-color: var(--bg-color); border-radius: 4px; border: 1px solid var(--border-color);">
                    <p><i class="fas fa-user"></i> <strong>${escapeHtml(review.email)}</strong></p>
                    <p><i class="fas fa-star" style="color: #FFD700;"></i> ${'★'.repeat(review.rating)}${'☆'.repeat(5 - review.rating)}</p>
                    ${review.comment ? '<p><i class="fas fa-comment"></i> ' + escapeHtml(review.comment) + '</p>' : ''}
                    <p style="font-size: 12px; color: #999;"><i class="fas fa-clock"></i> ${formatDate(review.created_at)}</p>
                </div>
            `;
        });
        
        container.innerHTML = html;
    }
    
    function submitReview() {
        const formData = new FormData(reviewForm);
        
        fetch('../api/review.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            showMessage(data.message, data.success ? 'success' : 'error');
            if (data.success) {
                reviewForm.reset();
                loadReviews();
            }
        })
        .catch(error => {
            showMessage('حدث خطأ أثناء إرسال التقييم', 'error');
        });
    }
    
    function showMessage(text, type) {
        messageDiv.innerHTML = '<div class="alert alert-' + type + '">' + text + '</div>';
        setTimeout(() => {
            messageDiv.innerHTML = '';
        }, 5000);
    }
    
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString('ar-SA');
    }
});
