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

    $bannerImage = $banner ? 'http://' . $_SERVER['HTTP_HOST'] . '/exploreia-backend/' . ltrim($banner['image_path'], '/') : null;

    echo json_encode([
        'banner' => $bannerImage
    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
