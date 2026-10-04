<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// الزوار يمكنهم عرض التفاصيل بدون تسجيل دخول، لكن التسجيل في الفعالية يتطلب تسجيل الدخول

$event_id = intval($_GET['id'] ?? 0);

if ($event_id <= 0) {
    header('Location: ../browse_events.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT e.*, e.organizer_id, u.email as organizer_email,
                           (SELECT COUNT(*) FROM Registrations WHERE event_id = e.id) as registered_count
                           FROM Events e
                           JOIN Users u ON e.organizer_id = u.id
                           WHERE e.id = ?");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch();
    
    if (!$event) {
        header('Location: ../browse_events.php');
        exit;
    }
    
    // التحقق من التسجيل
    $is_registered = false;
    if (isStudent()) {
        $stmt = $pdo->prepare("SELECT id FROM Registrations WHERE student_id = ? AND event_id = ?");
        $stmt->execute([$_SESSION['user_id'], $event_id]);
        $is_registered = $stmt->fetch() !== false;
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
    <title>تفاصيل الفعالية - نظام إدارة الفعاليات</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php $APP_NAV_FROM_ROOT = false;
require __DIR__ . '/../includes/app_nav.php'; ?>

    <div class="container">
        <div class="card" style="max-width: 800px; margin: 0 auto;">
            <h2 class="card-title"><?php echo htmlspecialchars($event['name']); ?></h2>
            
            <div class="card-text">
                <p><strong>الوصف:</strong></p>
                <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
            </div>
            
            <div class="card-text">
                <p><i class="fas fa-calendar"></i> <strong>التاريخ والوقت:</strong> <?php echo date('Y-m-d H:i', strtotime($event['event_date'])); ?></p>
                <p><i class="fas fa-map-marker-alt"></i> <strong>الموقع:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
                <p><i class="fas fa-tag"></i> <strong>النوع:</strong> <?php echo htmlspecialchars($event['event_type'] ?? 'غير محدد'); ?></p>
                <p><i class="fas fa-user-tie"></i> <strong>المنظم:</strong> <?php echo htmlspecialchars($event['organizer_email']); ?></p>
                <p><i class="fas fa-chair"></i> <strong>المقاعد المتاحة:</strong> <?php echo $event['available_seats']; ?> / <?php echo $event['total_seats']; ?></p>
                <p><i class="fas fa-users"></i> <strong>عدد المسجلين:</strong> <?php echo $event['registered_count']; ?></p>
            </div>
            
            <div class="card-footer">
                <?php if (isStudent()): ?>
                    <?php if ($is_registered): ?>
                        <button class="btn btn-danger" id="unregisterBtn" data-event-id="<?php echo $event_id; ?>"><i class="fas fa-times-circle"></i> إلغاء التسجيل</button>
                    <?php else: ?>
                        <?php if ($event['available_seats'] > 0): ?>
                            <button class="btn btn-primary" id="registerBtn" data-event-id="<?php echo $event_id; ?>"><i class="fas fa-user-plus"></i> التسجيل في الفعالية</button>
                        <?php else: ?>
                            <p style="color: #DC2626;"><i class="fas fa-exclamation-circle"></i> الفعالية ممتلئة</p>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php elseif (!isLoggedIn()): ?>
                    <p style="color: var(--primary-color); margin-bottom: 10px;"><i class="fas fa-info-circle"></i> أنشئ حساباً طالباً مجاناً أو سجّل الدخول للاشتراك في الفعالية</p>
                    <a href="../index.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> إنشاء حساب</a>
                    <a href="login.php?redirect=event_details.php?id=<?php echo $event_id; ?>" class="btn btn-secondary" style="margin-right: 8px;"><i class="fas fa-sign-in-alt"></i> تسجيل الدخول</a>
                <?php endif; ?>
                
                <?php if (isOrganizer() && $event['organizer_id'] == $_SESSION['user_id']): ?>
                    <a href="attendance.php?id=<?php echo $event_id; ?>" class="btn btn-secondary"><i class="fas fa-clipboard-check"></i> إدارة الحضور</a>
                <?php endif; ?>
                
                <a href="../browse_events.php" class="btn btn-secondary"><i class="fas fa-arrow-right"></i> العودة للقائمة</a>
            </div>
        </div>
        
        <div class="card reviews-section" style="max-width: 800px; margin: 20px auto;">
            <h3 class="card-title"><i class="fas fa-star" style="color: #F59E0B;"></i> التقييمات والتعليقات</h3>
            <div id="reviewsContainer"></div>
            
            <?php if (isStudent() && $is_registered && strtotime($event['event_date']) < time()): ?>
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-color);">
                    <h4>أضف تقييمك</h4>
                    <form id="reviewForm">
                        <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
                        <div class="form-group">
                            <label>التقييم:</label>
                            <div class="rating">
                                <input type="radio" id="star5" name="rating" value="5">
                                <label for="star5">★</label>
                                <input type="radio" id="star4" name="rating" value="4">
                                <label for="star4">★</label>
                                <input type="radio" id="star3" name="rating" value="3">
                                <label for="star3">★</label>
                                <input type="radio" id="star2" name="rating" value="2">
                                <label for="star2">★</label>
                                <input type="radio" id="star1" name="rating" value="1">
                                <label for="star1">★</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="comment">تعليق:</label>
                            <textarea id="comment" name="comment" rows="4"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">إرسال التقييم</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <div class="container">
            <p>&copy; 2026 نظام إدارة الفعاليات الجامعية</p>
        </div>
    </footer>

    <div id="message"></div>
    <script src="../js/theme.js"></script>
    <script src="../js/mobile-menu.js"></script>
    <script src="../js/event_details.js"></script>
</body>
</html>
