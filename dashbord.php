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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sensor Data Dashboard</title>
    <!-- Bootstrap CSS link -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .download-btn {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Sensor Data Dashboard</h1>

    <div class="download-btn">
        <a href="../model/downlode.php" class="btn btn-primary">Download All Sensor Data CSV</a>
    </div>

    <table class="table table-bordered table-striped mt-4">
        <thead class="thead-dark">
            <tr>
                <th>Humidity</th>
                <th>Temperature</th>
                <th>ECG Value</th>
                <th>DS18B20 Temperature</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sensorData as $data): ?>
                <tr>
                    <td><?php echo $data['humidity']; ?></td>
                    <td><?php echo $data['temperature']; ?></td>
                    <td><?php echo $data['ecgValue']; ?></td>
                    <td><?php echo $data['ds18b20Temperature']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="download-btn">
        <a href="download.php" class="btn btn-primary">Download All Sensor Data CSV</a>
    </div>
</div>

<!-- Bootstrap JS and Popper.js script links -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
