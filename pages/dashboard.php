<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../index.php');
    exit;
}

try {
    // إحصائيات
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM Users");
    $total_users = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM Events");
    $total_events = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM Registrations");
    $total_registrations = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM Attendance WHERE attended = 1");
    $total_attendance = $stmt->fetch()['total'];
    
    // المستخدمون
    $stmt = $pdo->query("SELECT id, email, user_type, created_at FROM Users ORDER BY created_at DESC LIMIT 50");
    $users = $stmt->fetchAll();
    
    // الفعاليات
    $stmt = $pdo->query("SELECT e.*, u.email as organizer_email,
                        (SELECT COUNT(*) FROM Registrations WHERE event_id = e.id) as registered_count
                        FROM Events e
                        JOIN Users u ON e.organizer_id = u.id
                        ORDER BY e.created_at DESC LIMIT 50");
    $events = $stmt->fetchAll();
} catch (PDOException $e) {
    $total_users = $total_events = $total_registrations = $total_attendance = 0;
    $users = $events = [];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - نظام إدارة الفعاليات</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php $APP_NAV_FROM_ROOT = false;
require __DIR__ . '/../includes/app_nav.php'; ?>

    <div class="container">
        <h2 style="margin-bottom: 30px;"><i class="fas fa-tachometer-alt"></i> لوحة التحكم - الأدمن</h2>
        
        <div class="events-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 30px;">
            <div class="card">
                <h3 class="card-title"><i class="fas fa-users"></i> إجمالي المستخدمين</h3>
                <p style="font-size: 32px; margin: 10px 0;"><?php echo $total_users; ?></p>
            </div>
            <div class="card">
                <h3 class="card-title"><i class="fas fa-calendar-alt"></i> إجمالي الفعاليات</h3>
                <p style="font-size: 32px; margin: 10px 0;"><?php echo $total_events; ?></p>
            </div>
            <div class="card">
                <h3 class="card-title"><i class="fas fa-user-plus"></i> إجمالي التسجيلات</h3>
                <p style="font-size: 32px; margin: 10px 0;"><?php echo $total_registrations; ?></p>
            </div>
            <div class="card">
                <h3 class="card-title"><i class="fas fa-check-circle"></i> إجمالي الحضور</h3>
                <p style="font-size: 32px; margin: 10px 0;"><?php echo $total_attendance; ?></p>
            </div>
        </div>
        
        <div class="card" style="margin-bottom: 30px;">
            <h3 class="card-title"><i class="fas fa-user-cog"></i> إدارة المستخدمين</h3>
            <table>
                <thead>
                    <tr>
                        <th><i class="fas fa-envelope"></i> البريد الإلكتروني</th>
                        <th><i class="fas fa-user-tag"></i> نوع المستخدم</th>
                        <th><i class="fas fa-calendar"></i> تاريخ الإنشاء</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <?php
                                $types = ['student' => 'طالب', 'organizer' => 'منظم', 'admin' => 'أدمن'];
                                echo $types[$user['user_type']] ?? $user['user_type'];
                                ?>
                            </td>
                            <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="card">
            <h3 class="card-title"><i class="fas fa-calendar-check"></i> إدارة الفعاليات</h3>
            <table>
                <thead>
                    <tr>
                        <th><i class="fas fa-heading"></i> اسم الفعالية</th>
                        <th><i class="fas fa-user-tie"></i> المنظم</th>
                        <th><i class="fas fa-calendar"></i> التاريخ</th>
                        <th><i class="fas fa-users"></i> المسجلين</th>
                        <th><i class="fas fa-cog"></i> الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($event['name']); ?></td>
                            <td><?php echo htmlspecialchars($event['organizer_email']); ?></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($event['event_date'])); ?></td>
                            <td><?php echo $event['registered_count']; ?></td>
                            <td>
                                <a href="event_details.php?id=<?php echo $event['id']; ?>" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;"><i class="fas fa-eye"></i> عرض</a>
                                <a href="attendance.php?id=<?php echo $event['id']; ?>" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px;"><i class="fas fa-clipboard-check"></i> الحضور</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer>
        <div class="container">
            <p>&copy; 2026 نظام إدارة الفعاليات الجامعية</p>
        </div>
    </footer>

    <script src="../js/theme.js"></script>
    <script src="../js/mobile-menu.js"></script>
</body>
</html>
