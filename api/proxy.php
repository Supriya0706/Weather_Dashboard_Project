<?php
/**
 * SkyCast OWM Proxy with MySQL Logging
 */

header('Content-Type: application/json');
require_once 'db.php';

$API_KEY = 'd402e52eb3117d6209fd8c47ab60e12a';
$BASE_URL = 'https://api.openweathermap.org/data/2.5';

$endpoint = $_GET['endpoint'] ?? '/weather';
$params = $_GET;
unset($params['endpoint']);

// Special case: if score is passed, we are logging the search
$score = $_GET['score'] ?? null;
unset($params['score']);

$params['appid'] = $API_KEY;
$params['units'] = 'metric';

$url = $BASE_URL . $endpoint . '?' . http_build_query($params);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Log to MySQL if it's a successful weather search with a score
if ($http_code === 200 && $endpoint === '/weather' && $score !== null) {
    $data = json_decode($output, true);
    $stmt = $conn->prepare("INSERT INTO weather_history (city, temp, humidity, weather_main, feels_like, wind_speed, score) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sdisddi", 
        $data['name'], 
        $data['main']['temp'], 
        $data['main']['humidity'], 
        $data['weather'][0]['main'], 
        $data['main']['feels_like'], 
        $data['wind']['speed'], 
        $score
    );
    $stmt->execute();
    $stmt->close();
}

http_response_code($http_code);
echo $output;
?>
