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

    $stmt = $conn->query("SELECT 
        t.id, t.title, t.destination_location, t.start_date, t.people_limit, i.image_path
    FROM tours t
    LEFT JOIN images i ON t.id = i.tour_id 
    WHERE i.object_type = 'tour' AND i.category = 'banner'");

    $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Додаємо повний шлях до зображення
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
