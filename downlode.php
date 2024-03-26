<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "health_monitor";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all sensor data
$sql = "SELECT * FROM sensor_data";
$result = $conn->query($sql);

// Check if there is data in the result set
if ($result->num_rows > 0) {
    $sensorData = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $sensorData = [];
}

// Close connection
$conn->close();

// Create CSV file
$csvFile = 'sensor_data_all.csv';
$fp = fopen('php://output', 'w');

// Set headers for CSV file download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $csvFile . '"');

// Write CSV headers
fputcsv($fp, array('Humidity', 'Temperature', 'ECG Value', 'DS18B20 Temperature'));

// Write CSV data
foreach ($sensorData as $data) {
    fputcsv($fp, array($data['humidity'], $data['temperature'], $data['ecgValue'], $data['ds18b20Temperature']));
}

fclose($fp);
?>
