<?php

$conn = new mysqli("localhost", "root", "", "weather_db");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $city = trim($_POST["city"]);

    if (!empty($city)) {
        $city_safe = escapeshellarg($city);


        $python = '"C:\\Users\\hp\\AppData\\Local\\Programs\\Python\\Python314\\python.exe"';
        $script = '"C:\\xampp\\htdocs\\weather_dashboard\\scripts\\fetch_weather.py"';

        // Execute Python script
        $command = "$python $script $city_safe 2>&1";
        $output = shell_exec($command);
        $output = trim($output);
        $data = json_decode($output, true);

        if (!$data) {
            echo "<p style='color:red;'>Python Output (debug):</p>";
            echo "<pre>$output</pre>";
            echo "<p style='color:red;'>JSON decode failed</p>";
        }
        elseif (isset($data["error"])) {
            echo "<p style='color:red;'>Error: {$data['error']}</p>";
        }
        else {

            echo "<h2>Weather for {$city}</h2>";
            echo "<p><b>Temperature:</b> {$data['temp']} °C</p>";
            echo "<p><b>Humidity:</b> {$data['humidity']} %</p>";
            echo "<p><b>Analysis:</b> {$data['analysis']}</p>";

            $stmt = $conn->prepare("INSERT INTO weather_logs (city, temp, humidity) VALUES (?, ?, ?)");
            $stmt->bind_param("sdi", $city, $data["temp"], $data["humidity"]);
            $stmt->execute();
            $stmt->close();

            echo "<p style='color:green;'>Weather data saved successfully.</p>";
        }
    } else {
        echo "<p style='color:red;'>Please enter a city name.</p>";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Real-Time Weather Dashboard</title>
    <style>
        body { font-family: Arial; background: #eef2f3; padding: 40px; }
        h1 { color: #333; }
        form { background: #fff; padding: 20px; width: 300px; border-radius: 5px; }
        input, button { width: 100%; padding: 8px; margin-top: 10px; }
        button { background: #007BFF; color: #fff; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
<h1>🌦️ Real-Time Weather Dashboard</h1>
<form method="post">
    <label>Enter City:</label>
    <input type="text" name="city" placeholder="e.g. London" required>
    <button type="submit">Get Weather</button>
</form>
</body>
</html>
