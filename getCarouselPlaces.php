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

    $placeId = isset($_GET['placeId']) ? (int)$_GET['placeId'] : 0;

    $sql = "
        SELECT 
            p.id, 
            p.name, 
            p.description, 
            p.location_name, 
            i.image_path
        FROM places p
        LEFT JOIN images i ON p.id = i.place_id
        WHERE i.object_type = 'location' AND i.category = 'banner'
    ";

    if ($placeId > 0) {
        $sql .= " AND p.id != :placeId";
    }

    $stmt = $conn->prepare($sql);

    if ($placeId > 0) {
        $stmt->bindParam(':placeId', $placeId, PDO::PARAM_INT);
    }

    $stmt->execute();
    $places = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($places as &$place) {
        if (isset($place['image_path'])) {
            $place['image_path'] = 'http://' . $_SERVER['HTTP_HOST'] . '/exploreia-backend/' . ltrim($place['image_path'], '/');
        }
    }
    unset($place);

    echo json_encode($places);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
