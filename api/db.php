<?php
/**
 * Database Connection Configuration
 */

$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'weather_db';
$port = getenv('DB_PORT') ?: 3306;

// Handle host:port format if provided in DB_HOST
if (strpos($host, ':') !== false) {
    list($host, $p) = explode(':', $host);
    $port = $p;
}

$conn = new mysqli($host, $user, $pass, $dbname, $port);

if ($conn->connect_error) {
    // If connection with dbname fails, try without it to see if we need to create it (local setup)
    $conn = new mysqli($host, $user, $pass);
    if (!$conn->connect_error) {
        $conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
        $conn->select_db($dbname);
    } else {
        die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
    }
}

// Ensure table exists
$table_sql = "CREATE TABLE IF NOT EXISTS weather_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    city VARCHAR(100) NOT NULL,
    temp FLOAT NOT NULL,
    humidity INT NOT NULL,
    weather_main VARCHAR(50) NOT NULL,
    feels_like FLOAT NOT NULL,
    wind_speed FLOAT NOT NULL,
    score INT NOT NULL,
    search_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($table_sql);
?>
