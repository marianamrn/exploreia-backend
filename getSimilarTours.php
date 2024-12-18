<?php
// Заголовки для JSON відповіді та дозволу CORS
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Параметри підключення до бази даних
$host = 'localhost';
$dbname = 'exploreia';
$username = 'root';
$password = '';

try {
    // Підключення до бази даних
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Отримання `current_tour_id` із параметрів запиту
    $current_tour_id = isset($_GET['tourId']) ? intval($_GET['tourId']) : null;

    // SQL-запит для отримання даних турів
    $sql = "SELECT 
                t.id, 
                t.title, 
                t.destination_location, 
                t.start_date, 
                t.people_limit, 
                i.image_path
            FROM tours t
            LEFT JOIN images i ON t.id = i.tour_id 
            WHERE i.object_type = 'tour' 
              AND i.category = 'banner' 
              AND (:current_tour_id IS NULL OR t.id != :current_tour_id)
            LIMIT 15"; // Вибірка до 15 турів

    $stmt = $conn->prepare($sql);

    // Прив'язка параметрів
    $stmt->bindParam(':current_tour_id', $current_tour_id, PDO::PARAM_INT);

    // Виконання запиту
    $stmt->execute();
    $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Формування повного шляху до зображень
    foreach ($tours as &$tour) {
        if (isset($tour['image_path']) && !empty($tour['image_path'])) {
            $tour['image_path'] = 'http://' . $_SERVER['HTTP_HOST'] . '/exploreia-backend/' . ltrim($tour['image_path'], '/');
        } else {
            // Встановлення зображення за замовчуванням, якщо воно відсутнє
            $tour['image_path'] = 'http://' . $_SERVER['HTTP_HOST'] . '/exploreia-backend/default-image.jpg';
        }
    }
    unset($tour);

    // Повернення результатів у форматі JSON
    echo json_encode($tours);
} catch (PDOException $e) {
    // Обробка помилок підключення або запиту
    echo json_encode(['error' => $e->getMessage()]);
}
?>
