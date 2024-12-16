<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$host = 'localhost';
$dbname = 'exploreia';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    // Отримання інформації про тур
    $tourQuery = $conn->prepare("SELECT * FROM tours WHERE id = :id");
    $tourQuery->bindParam(':id', $id, PDO::PARAM_INT);
    $tourQuery->execute();
    $tour = $tourQuery->fetch(PDO::FETCH_ASSOC);

    // Отримання фотографій
    $imagesQuery = $conn->prepare("SELECT image_path, alt_text FROM images WHERE tour_id = :id AND object_type = 'tour'");
    $imagesQuery->bindParam(':id', $id, PDO::PARAM_INT);
    $imagesQuery->execute();
    $images = $imagesQuery->fetchAll(PDO::FETCH_ASSOC);

    // Додавання шляху до фотографій
    foreach ($images as &$image) {
        $image['image_path'] = 'http://' . $_SERVER['HTTP_HOST'] . '/exploreia-backend/' . ltrim($image['image_path'], '/');
    }

    // Отримання обкладинки категорії (category_banner)
    $bannerQuery = $conn->prepare("SELECT image_path FROM images WHERE tour_id = :id AND category = 'banner' LIMIT 1");
    $bannerQuery->bindParam(':id', $id, PDO::PARAM_INT);
    $bannerQuery->execute();
    $banner = $bannerQuery->fetch(PDO::FETCH_ASSOC);

    $bannerImage = $banner ? 'http://' . $_SERVER['HTTP_HOST'] . '/exploreia-backend/' . ltrim($banner['image_path'], '/') : null;

    echo json_encode(['tour' => $tour, 'images' => $images, 'banner' => $bannerImage]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
