// إدارة الحضور
document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.getElementById('attendanceTableBody');
    const messageDiv = document.getElementById('message');
    
    loadAttendance();
    
    function loadAttendance() {
        fetch('../api/attendance.php?event_id=' + eventId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayAttendance(data.registrations);
                    updateStats(data.registrations);
                } else {
                    tableBody.innerHTML = '<tr><td colspan="5">' + data.message + '</td></tr>';
                }
            })
            .catch(error => {
                tableBody.innerHTML = '<tr><td colspan="5">حدث خطأ أثناء تحميل البيانات</td></tr>';
            });
    }
    
    function displayAttendance(registrations) {
        if (registrations.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="5" style="text-align: center;">لا يوجد طلاب مسجلين</td></tr>';
            return;
        }
        
        tableBody.innerHTML = registrations.map(reg => `
            <tr>
                <td><i class="fas fa-envelope"></i> ${escapeHtml(reg.email)}</td>
                <td><i class="fas fa-calendar"></i> ${formatDate(reg.registered_at)}</td>
                <td>${reg.attended ? '<i class="fas fa-check-circle" style="color: #10B981;"></i> حاضر' : '<i class="fas fa-times-circle" style="color: #EF4444;"></i> غائب'}</td>
                <td><i class="fas fa-clock"></i> ${reg.attendance_time ? formatDate(reg.attendance_time) : '-'}</td>
                <td>
                    <button class="btn btn-primary" onclick="toggleAttendance(${reg.id}, ${reg.attended ? 0 : 1})" 
                            style="padding: 6px 12px; font-size: 12px;">
                        <i class="fas fa-${reg.attended ? 'times' : 'check'}"></i> ${reg.attended ? 'تغيير للغائب' : 'تغيير للحاضر'}
                    </button>
                </td>
            </tr>
        `).join('');
    }
    
    function updateStats(registrations) {
        const totalRegistered = registrations.length;
        const totalAttended = registrations.filter(r => r.attended).length;
        
        document.getElementById('totalRegistered').textContent = totalRegistered;
        document.getElementById('totalAttended').textContent = totalAttended;
    }
    
    window.toggleAttendance = function(registrationId, attended) {
        const formData = new FormData();
        formData.append('registration_id', registrationId);
        formData.append('attended', attended);
        
        fetch('../api/attendance.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            showMessage(data.message, data.success ? 'success' : 'error');
            if (data.success) {
                loadAttendance();
            }
        })
        .catch(error => {
            showMessage('حدث خطأ أثناء تحديث الحضور', 'error');
        });
    };
    
    function showMessage(text, type) {
        messageDiv.innerHTML = '<div class="alert alert-' + type + '">' + text + '</div>';
        setTimeout(() => {
            messageDiv.innerHTML = '';
        }, 3000);
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function formatDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleString('ar-SA', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
});
