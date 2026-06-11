<?php
/**
 * Database Connection Configuration
 */

$host = 'localhost';
$user = 'root';
$pass = ''; // Default XAMPP password is empty
$dbname = 'weather_db';

$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Ensure database exists
$conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
$conn->select_db($dbname);

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
