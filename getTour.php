<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); // Дозволяє доступ з будь-якого джерела
header('Access-Control-Allow-Methods: GET, POST, OPTIONS'); // Дозволяє методи GET, POST, OPTIONS
header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Дозволяє зазначені заголовки

$host = 'localhost';  
$dbname = 'exploreia';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->query("SELECT title FROM tours LIMIT 1");
    $tour = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($tour);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
