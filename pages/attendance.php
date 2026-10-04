<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

if (!isLoggedIn() || (!isOrganizer() && !isAdmin())) {
    header('Location: ../index.php');
    exit;
}

$event_id = intval($_GET['id'] ?? 0);

if ($event_id <= 0) {
    header('Location: ../browse_events.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM Events WHERE id = ?");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch();
    
    if (!$event) {
        header('Location: ../browse_events.php');
        exit;
    }
    
    // التحقق من ملكية الفعالية (للمنظم)
    if (isOrganizer() && $event['organizer_id'] != $_SESSION['user_id']) {
        header('Location: ../browse_events.php');
        exit;
    }
} catch (PDOException $e) {
    header('Location: ../browse_events.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الحضور - نظام إدارة الفعاليات</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php $APP_NAV_FROM_ROOT = false;
require __DIR__ . '/../includes/app_nav.php'; ?>

    <div class="container">
        <h2 style="margin-bottom: 20px;"><i class="fas fa-clipboard-check"></i> إدارة الحضور - <?php echo htmlspecialchars($event['name']); ?></h2>
        
        <div class="card">
            <p><i class="fas fa-calendar"></i> <strong>التاريخ:</strong> <?php echo date('Y-m-d H:i', strtotime($event['event_date'])); ?></p>
            <p><i class="fas fa-map-marker-alt"></i> <strong>الموقع:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
            <p><i class="fas fa-users"></i> <strong>عدد المسجلين:</strong> <span id="totalRegistered">0</span></p>
            <p><i class="fas fa-check-circle"></i> <strong>عدد الحاضرين:</strong> <span id="totalAttended">0</span></p>
        </div>
        
        <div id="message"></div>
        
        <div class="card">
            <h3 class="card-title"><i class="fas fa-list"></i> قائمة الطلاب المسجلين</h3>
            <table id="attendanceTable">
                <thead>
                    <tr>
                        <th>البريد الإلكتروني</th>
                        <th>تاريخ التسجيل</th>
                        <th>الحضور</th>
                        <th>وقت الحضور</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody id="attendanceTableBody">
                    <tr>
                        <td colspan="5" style="text-align: center;">جاري التحميل...</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <a href="event_details.php?id=<?php echo $event_id; ?>" class="btn btn-secondary"><i class="fas fa-arrow-right"></i> العودة لتفاصيل الفعالية</a>
    </div>

    <footer>
        <div class="container">
            <p>&copy; 2026 نظام إدارة الفعاليات الجامعية</p>
        </div>
    </footer>

    <script>
        const eventId = <?php echo $event_id; ?>;
    </script>
    <script src="../js/theme.js"></script>
    <script src="../js/mobile-menu.js"></script>
    <script src="../js/attendance.js"></script>
</body>
</html>
