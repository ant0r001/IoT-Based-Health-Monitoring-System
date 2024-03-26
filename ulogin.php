<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "health_monitor";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//login check
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Implement proper validation and sanitation
    if (empty($username) || empty($password)) {
        echo "All fields are required";
    } else {
        // Hash the password (use password_hash() in a real-world application)
        $hashed_password = md5($password);

        // Insert user into the database
        $sql = "SELECT * FROM users WHERE email='$username' AND password='$password '";

        $result = $conn->query($sql);

        //login error check
        if ($result->num_rows > 0) {
            echo "Login successful";

            // Redirect to the login page
            header("Location: ../model/dashbord.php");

        } else {
            echo "Error: wrong username or password";
        }


    }
}


?>
