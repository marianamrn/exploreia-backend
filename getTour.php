<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

$host = 'localhost';
$dbname = 'exploreia';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $destination = isset($_GET['destination']) ? $_GET['destination'] : '';
    $departure = isset($_GET['departure']) ? $_GET['departure'] : '';
    $date = isset($_GET['date']) ? $_GET['date'] : '';
    $days = isset($_GET['days']) ? (int)$_GET['days'] : 0;

    $sql = "SELECT 
            t.id, t.title, t.destination_location, t.departure_location, t.start_date, t.people_limit, i.image_path
        FROM tours t
        LEFT JOIN images i ON t.id = i.tour_id 
        WHERE i.object_type = 'tour' AND i.category = 'banner'";

    // Фільтри до запиту
    if (!empty($destination)) {
        $sql .= " AND t.destination_location = :destination";
    }
    if (!empty($departure)) {
        $sql .= " AND LOWER(TRIM(t.departure_location)) = LOWER(TRIM(:departure))";
    }
    if (!empty($date)) {
        $sql .= " AND t.start_date BETWEEN DATE_SUB(:date, INTERVAL 2 DAY) AND DATE_ADD(:date, INTERVAL 2 DAY)";
    }
    if ($days > 0) {
        $sql .= " AND t.duration_days BETWEEN :days - 2 AND :days + 2";
    }

    $stmt = $conn->prepare($sql);

    // Прив'язуємо параметри
    if (!empty($destination)) {
        $stmt->bindParam(':destination', $destination);
    }
    if (!empty($departure)) {
        $stmt->bindParam(':departure', $departure);
    }
    if (!empty($date)) {
        $stmt->bindParam(':date', $date);
    }
    if ($days > 0) {
        $stmt->bindParam(':days', $days, PDO::PARAM_INT);
    }

    $stmt->execute();
    $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Шлях до зображення
    foreach ($tours as &$tour) {
        if (isset($tour['image_path'])) {
            $tour['image_path'] = 'http://' . $_SERVER['HTTP_HOST'] . '/exploreia-backend/' . ltrim($tour['image_path'], '/');
        }
    }
    unset($tour);

    echo json_encode($tours);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
