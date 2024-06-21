<?php
// Start the session to access session variables
session_start();

// Set content type to application/json for proper JSON response handling
header('Content-Type: application/json');

// Include the database connection script
require_once 'db_connection.php';

// Check for user authentication and the presence of 'list_id' in the query string
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_GET["list_id"])) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Missing authentication or list ID."]);
    exit;
}

// Extract the user ID and list ID, ensuring list_id is properly validated
$user_id = $_SESSION["user_id"];
$list_id = filter_input(INPUT_GET, 'list_id', FILTER_VALIDATE_INT);

// Validate list_id input
if (false === $list_id) {
    http_response_code(422); // Unprocessable Entity
    echo json_encode(["error" => "Invalid list ID."]);
    exit;
}

// Prepare the SQL query to fetch the list names based on list_id and user_id
$sql = "SELECT list_data FROM saved_lists WHERE list_id = ? AND user_id = ? LIMIT 1";

if ($stmt = $conn->prepare($sql)) {
    // Bind parameters and execute
    $stmt->bind_param("ii", $list_id, $user_id);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            // Directly output the list data, assuming it's stored in JSON format
            echo $row["list_data"];
        } else {
            // If no data found, return an empty array as JSON
            echo json_encode([]);
        }
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => "Query execution failed: " . $stmt->error]);
    }
    
    // Clean up statement
    $stmt->close();
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(["error" => "Failed to prepare the query: " . $conn->error]);
}

// Close the database connection
$conn->close();
?>
