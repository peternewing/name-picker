<?php
// db_connection.php
$servername = "db5015625110.hosting-data.io"; // Host address
$username = "dbu1193995"; // Database username
$password = "Barripper1998"; // Database password
$dbname = "dbs12759750"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
