<?php
/**
 * Fetch Search History from MySQL
 */

header('Content-Type: application/json');
require_once 'db.php';

$result = $conn->query("SELECT DISTINCT city FROM weather_history ORDER BY search_time DESC LIMIT 5");
$history = [];
while ($row = $result->fetch_assoc()) {
    $history[] = $row['city'];
}

echo json_encode($history);
?>
