<?php
// Initialize session to access session variables
session_start();

// Set content type to application/json for AJAX requests
header('Content-Type: application/json');

// Include the database connection file
require_once 'db_connection.php';

// Check if the user is authenticated
if (!isset($_SESSION['user_id'])) {
    http_response_code(403); // Forbidden
    echo json_encode(['error' => 'User not authenticated']);
    exit;
}

// Extract the user ID from the session
$userId = $_SESSION['user_id'];

// Prepare the SQL query to select the user's saved lists
$sql = "SELECT list_id, list_name, list_data FROM saved_lists WHERE user_id = ? ORDER BY list_id DESC";

// Prepare the SQL statement
if ($stmt = $conn->prepare($sql)) {
    // Bind the user ID to the prepared statement
    $stmt->bind_param("i", $userId);
    
    // Execute the query
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $lists = [];

        // Fetch each row from the query results
        while ($row = $result->fetch_assoc()) {
            $lists[] = [
                'list_id' => $row['list_id'],
                'list_name' => $row['list_name'],
                // Decode the list data from JSON format
                'list_data' => json_decode($row['list_data'], true)
            ];
        }

        // Send the lists as a JSON response
        echo json_encode($lists);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => "Failed to execute query: " . $stmt->error]);
    }

    // Clean up the statement
    $stmt->close();
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(["error" => "Unable to prepare query: " . $conn->error]);
}

// Close the database connection
$conn->close();
?>
