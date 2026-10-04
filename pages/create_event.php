<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/auth.php';

if (!isLoggedIn() || !isOrganizer()) {
    header('Location: ../index.php');
    exit;
}

$event_id = intval($_GET['id'] ?? 0);
$event = null;

if ($event_id > 0) {
    require_once __DIR__ . '/../config/database.php';
    try {
        $stmt = $pdo->prepare("SELECT * FROM Events WHERE id = ? AND organizer_id = ?");
        $stmt->execute([$event_id, $_SESSION['user_id']]);
        $event = $stmt->fetch();
        
        if (!$event) {
            header('Location: create_event.php');
            exit;
        }
    } catch (PDOException $e) {
        header('Location: create_event.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $event ? 'تعديل فعالية' : 'إنشاء فعالية'; ?> - نظام إدارة الفعاليات</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php $APP_NAV_FROM_ROOT = false;
require __DIR__ . '/../includes/app_nav.php'; ?>

    <div class="container">
        <div class="form-container form-container--wide">
            <h2 class="event-form-title">
                <i class="fas fa-<?php echo $event ? 'edit' : 'plus-circle'; ?>"></i> <?php echo $event ? 'تعديل فعالية' : 'إنشاء فعالية جديدة'; ?>
            </h2>
            
            <div id="message"></div>
            
            <form id="eventForm" class="event-form">
                <input type="hidden" id="event_id" name="event_id" value="<?php echo $event_id; ?>">
                
                <div class="event-form-grid">
                    <div class="form-group form-group--full">
                        <label for="name"><i class="fas fa-heading"></i> اسم الفعالية</label>
                        <input type="text" id="name" name="name" required placeholder="مثال: ورشة تطوير الويب"
                               value="<?php echo $event ? htmlspecialchars($event['name']) : ''; ?>">
                    </div>
                    
                    <div class="form-group form-group--full">
                        <label for="description"><i class="fas fa-align-right"></i> الوصف</label>
                        <textarea id="description" name="description" rows="8" required placeholder="وصف مختصر للفعالية والمحتوى المتوقع"><?php echo $event ? htmlspecialchars($event['description']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="event_date"><i class="fas fa-calendar"></i> التاريخ والوقت</label>
                        <input type="datetime-local" id="event_date" name="event_date" required
                               value="<?php echo $event ? date('Y-m-d\TH:i', strtotime($event['event_date'])) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="event_type"><i class="fas fa-tag"></i> نوع الفعالية</label>
                        <select id="event_type" name="event_type">
                            <option value="">اختر النوع</option>
                            <option value="workshop" <?php echo ($event && $event['event_type'] === 'workshop') ? 'selected' : ''; ?>>ورشة عمل</option>
                            <option value="lecture" <?php echo ($event && $event['event_type'] === 'lecture') ? 'selected' : ''; ?>>محاضرة</option>
                            <option value="conference" <?php echo ($event && $event['event_type'] === 'conference') ? 'selected' : ''; ?>>مؤتمر</option>
                            <option value="seminar" <?php echo ($event && $event['event_type'] === 'seminar') ? 'selected' : ''; ?>>ندوة</option>
                            <option value="other" <?php echo ($event && $event['event_type'] === 'other') ? 'selected' : ''; ?>>أخرى</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="location"><i class="fas fa-map-marker-alt"></i> الموقع</label>
                        <input type="text" id="location" name="location" required placeholder="القاعة أو المبنى"
                               value="<?php echo $event ? htmlspecialchars($event['location']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="total_seats"><i class="fas fa-chair"></i> عدد المقاعد</label>
                        <input type="number" id="total_seats" name="total_seats" min="1" required placeholder="مثال: 50"
                               value="<?php echo $event ? $event['total_seats'] : ''; ?>">
                    </div>
                </div>
                
                <div class="event-form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-<?php echo $event ? 'save' : 'plus'; ?>"></i> <?php echo $event ? 'تحديث الفعالية' : 'إنشاء الفعالية'; ?>
                    </button>
                    <?php if ($event): ?>
                        <button type="button" class="btn btn-danger" id="deleteBtn">
                            <i class="fas fa-trash"></i> حذف الفعالية
                        </button>
                    <?php endif; ?>
                    <a href="home.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>

    <footer>
        <div class="container">
            <p>&copy; 2026 نظام إدارة الفعاليات الجامعية</p>
        </div>
    </footer>

    <script src="../js/theme.js"></script>
    <script src="../js/mobile-menu.js"></script>
    <script src="../js/create_event.js"></script>
</body>
</html>
