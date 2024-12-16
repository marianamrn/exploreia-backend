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

    // список унікальних міст для destination_location
    $stmtDestination = $conn->prepare("SELECT DISTINCT destination_location FROM tours");
    $stmtDestination->execute();
    $destinationCities = $stmtDestination->fetchAll(PDO::FETCH_COLUMN);

    // список унікальних міст для departure_location
    $stmtDeparture = $conn->prepare("SELECT DISTINCT departure_location FROM tours");
    $stmtDeparture->execute();
    $departureCities = $stmtDeparture->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode([
        'destinationCities' => $destinationCities,
        'departureCities' => $departureCities,
    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
