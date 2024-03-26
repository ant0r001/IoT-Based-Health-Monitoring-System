<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "health_monitor";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
$phone = $_POST['phone'];
$address = $_POST['address'];

// SQL query to insert data into the database
$sql = "INSERT INTO users (username, email, password, phone, address) VALUES ('$username', '$email', '$password', '$phone', '$address')";

if ($conn->query($sql) === TRUE) {
    echo "Signup successful";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();


//signup successfull GO TO LOGIN PAGE

header("Location: ../view/login.php");




?>
