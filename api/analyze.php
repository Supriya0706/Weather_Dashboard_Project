<?php
/**
 * PHP Bridge for Python Analytics Engine
 */

header('Content-Type: application/json');

$temp = $_GET['temp'] ?? 20;
$humidity = $_GET['humidity'] ?? 50;
$weather = $_GET['weather'] ?? 'Clear';

// Sanitize inputs
$temp = escapeshellarg($temp);
$humidity = escapeshellarg($humidity);
$weather = escapeshellarg($weather);

// Path to Python
$python_path = "python"; // Assumes python is in PATH
$script_path = __DIR__ . '/../scripts/analytics.py';

$command = "$python_path \"$script_path\" $temp $humidity \"$weather\" 2>&1";
$output = shell_exec($command);

echo $output;
?>
