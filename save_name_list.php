<?php
// Start session to access session variables
session_start();

// Include the database connection script
require_once 'db_connection.php';

// Set the Content-Type to application/json for proper JSON response
header('Content-Type: application/json');

// Check if the user is logged in and the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['message' => 'Only POST requests are allowed and user must be logged in.']);
    exit;
}

// Read and decode the JSON content from the request
$content = trim(file_get_contents("php://input"));
$decoded = json_decode($content, true);

// Validate the decoded JSON and required fields
if (!is_array($decoded) || !isset($decoded['list_name']) || !isset($decoded['list_data']) || !isset($_SESSION["user_id"])) {
    http_response_code(400); // Bad Request
    echo json_encode(['message' => 'Invalid or incomplete JSON data or user not logged in.']);
    exit;
}

// Extract the necessary information
$listName = $decoded['list_name'];
$listData = json_encode($decoded['list_data']); // Re-encode list data to ensure it's in JSON format
$userId = $_SESSION["user_id"]; // Extract user ID from session

// Prepare the SQL statement for inserting data
$sql = "INSERT INTO saved_lists (user_id, list_name, list_data) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

// Check if statement preparation was successful
if ($stmt === false) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['message' => 'Failed to prepare the statement: ' . $conn->error]);
    exit;
}

// Bind parameters and execute the prepared statement
$stmt->bind_param("iss", $userId, $listName, $listData);
if ($stmt->execute()) {
    echo json_encode(['message' => 'List saved successfully.']);
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(['message' => 'Failed to execute the statement: ' . $stmt->error]);
}

// Clean up statement
$stmt->close();
// Optionally, close the database connection if not used further
$conn->close();
?>
