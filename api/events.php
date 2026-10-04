<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $search = $_GET['search'] ?? '';
    $event_type = $_GET['event_type'] ?? '';
    $date_filter = $_GET['date_filter'] ?? '';
    
    $query = "SELECT e.*, u.email as organizer_email, 
              (SELECT COUNT(*) FROM Registrations WHERE event_id = e.id) as registered_count
              FROM Events e
              JOIN Users u ON e.organizer_id = u.id
              WHERE 1=1";
    
    $params = [];
    
    if (!empty($search)) {
        $query .= " AND (e.name LIKE ? OR e.description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    if (!empty($event_type)) {
        $query .= " AND e.event_type = ?";
        $params[] = $event_type;
    }
    
    if ($date_filter === 'upcoming') {
        $query .= " AND e.event_date >= NOW()";
    } elseif ($date_filter === 'past') {
        $query .= " AND e.event_date < NOW()";
    }
    
    $query .= " ORDER BY e.event_date ASC";
    
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $events = $stmt->fetchAll();
        
        echo json_encode(['success' => true, 'events' => $events]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'خطأ في قاعدة البيانات']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'طريقة طلب غير صحيحة']);
}
?>
