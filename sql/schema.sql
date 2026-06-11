CREATE DATABASE IF NOT EXISTS weather_db;

USE weather_db;

CREATE TABLE IF NOT EXISTS weather_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    city VARCHAR(100) NOT NULL,
    temp FLOAT NOT NULL,
    humidity INT NOT NULL,
    weather_main VARCHAR(50) NOT NULL,
    feels_like FLOAT NOT NULL,
    wind_speed FLOAT NOT NULL,
    score INT NOT NULL,
    search_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
