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

    // Отримання ID туру
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    // Отримання інформації про тур
    $tourQuery = $conn->prepare("SELECT * FROM tours WHERE id = :id");
    $tourQuery->bindParam(':id', $id, PDO::PARAM_INT);
    $tourQuery->execute();
    $tour = $tourQuery->fetch(PDO::FETCH_ASSOC);

    // Фотографії для туру
    $imagesQuery = $conn->prepare("SELECT image_path, alt_text FROM images WHERE tour_id = :id AND object_type = 'tour'");
    $imagesQuery->bindParam(':id', $id, PDO::PARAM_INT);
    $imagesQuery->execute();
    $images = $imagesQuery->fetchAll(PDO::FETCH_ASSOC);

    // Додавання шляху до фотографій
    foreach ($images as &$image) {
        $image['image_path'] = 'http://' . $_SERVER['HTTP_HOST'] . '/exploreia-backend/' . ltrim($image['image_path'], '/');
    }

    // обкладинка
    $bannerQuery = $conn->prepare("SELECT image_path FROM images WHERE tour_id = :id AND category = 'banner' LIMIT 1");
    $bannerQuery->bindParam(':id', $id, PDO::PARAM_INT);
    $bannerQuery->execute();
    $banner = $bannerQuery->fetch(PDO::FETCH_ASSOC);

    $bannerImage = $banner ? 'http://' . $_SERVER['HTTP_HOST'] . '/exploreia-backend/' . ltrim($banner['image_path'], '/') : null;

    // зображення "welcome-tour-photo"
    $welcomeQuery = $conn->prepare("SELECT image_path, alt_text FROM images WHERE tour_id = :id AND category = 'welcome-tour-photo' LIMIT 1");
    $welcomeQuery->bindParam(':id', $id, PDO::PARAM_INT);
    $welcomeQuery->execute();
    $welcomeImage = $welcomeQuery->fetch(PDO::FETCH_ASSOC);

    $welcomeTourPhoto = $welcomeImage ? [
        'image_path' => 'http://' . $_SERVER['HTTP_HOST'] . '/exploreia-backend/' . ltrim($welcomeImage['image_path'], '/'),
        'alt_text' => $welcomeImage['alt_text']
    ] : null;

    echo json_encode([
        'tour' => $tour,
        'images' => $images,
        'banner' => $bannerImage,
        'welcomeTourPhoto' => $welcomeTourPhoto,
        'where_to' => $tour['where_to'],
        'for_whom' => $tour['for_whom'],
        'why_go' => $tour['why_go'],
        'description' => $tour['description'],
        'includes' => $tour['includes'],
        'facts' => $tour['facts']
    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
