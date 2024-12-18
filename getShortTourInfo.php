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
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed: ' . $e->getMessage()
    ]);
    exit;
}

if (isset($_GET['id'])) {
    $tourId = $_GET['id'];

    $query = "
        SELECT 
            t.duration_days,
            t.price,
            t.people_limit,
            t.discount,
            t.short_info_time,
            t.short_info_price,
            t.short_info_people,
            t.short_info_discount
        FROM tours AS t
        WHERE t.id = :tourId
    ";

    try {
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':tourId', $tourId, PDO::PARAM_INT);
        $stmt->execute();
        $tourInfo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($tourInfo) {
            echo json_encode([
                'status' => 'success',
                'data' => $tourInfo
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Tour not found.'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Query execution failed: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Tour ID is required.'
    ]);
}
?>
