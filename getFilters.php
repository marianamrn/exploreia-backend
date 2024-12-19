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

    // Отримання областей
    $stmtRegion = $conn->prepare("SELECT DISTINCT region FROM places");
    $stmtRegion->execute();
    $regions = $stmtRegion->fetchAll(PDO::FETCH_ASSOC);

    // Отримання категорій
    $stmtCategory = $conn->prepare("SELECT DISTINCT category FROM places");
    $stmtCategory->execute();
    $categories = $stmtCategory->fetchAll(PDO::FETCH_ASSOC);

    // Отримання сезонів
    $stmtSeason = $conn->prepare("SELECT DISTINCT seasonality FROM places");
    $stmtSeason->execute();
    $seasons = $stmtSeason->fetchAll(PDO::FETCH_ASSOC);

    // Виведення результатів як JSON
    echo json_encode([
        'regions' => $regions,
        'categories' => $categories,
        'seasons' => $seasons
    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
