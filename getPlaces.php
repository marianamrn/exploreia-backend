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

    // Отримання обкладинки сторінки Places
    $bannerQuery = $conn->prepare("SELECT image_path FROM images WHERE object_type = 'location' AND category = 'places-page-banner' LIMIT 1");
    $bannerQuery->execute();
    $banner = $bannerQuery->fetch(PDO::FETCH_ASSOC);

    if (!$banner) {
        // Лог для перевірки: якщо банер не знайдено
        echo json_encode(['error' => 'No banner found']);
        exit;
    }

    $bannerImage = 'http://' . $_SERVER['HTTP_HOST'] . '/exploreia-backend/' . ltrim($banner['image_path'], '/');

    // Запит для отримання місцин
    $placesQuery = $conn->prepare("SELECT 
            l.id, l.location_name, l.category, i.image_path, l.name
        FROM places l
        LEFT JOIN images i ON l.id = i.place_id
        WHERE i.object_type = 'location' AND i.category = 'banner'");
    $placesQuery->execute();
    $places = $placesQuery->fetchAll(PDO::FETCH_ASSOC);

    if (empty($places)) {
        // Лог для перевірки: якщо місцини не знайдено
        echo json_encode(['error' => 'No places found']);
        exit;
    }

    // Шлях до зображення місцин
    foreach ($places as &$place) {
        if (isset($place['image_path'])) {
            $place['image_path'] = 'http://' . $_SERVER['HTTP_HOST'] . '/exploreia-backend/' . ltrim($place['image_path'], '/');
        }
    }
    unset($place);

    // Відправка відповідей: банер та місцини
    echo json_encode([
        'banner' => $bannerImage,
        'places' => $places
    ]);
} catch (PDOException $e) {
    // Лог для помилки підключення або виконання запиту
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
