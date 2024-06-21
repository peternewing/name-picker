<?php
// Start the session
session_start();

// Include your DB connection script
require 'db_connection.php';

$message = ''; // Initialize a variable to store messages

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Trim and assign input to variables
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);

    // Basic validation for empty fields
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $message = "Please fill in all fields.";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    } else {
        // Prepare a select statement to check if username exists
        $sql = "SELECT id FROM users WHERE username = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement
            $stmt->bind_param("s", $username);
            
            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                $stmt->store_result();
                
                // Check if username exists
                if ($stmt->num_rows == 1) {
                    $message = "This username is already taken.";
                } else {
                    // Username doesn't exist, proceed with inserting new record
                    $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
                    
                    if ($stmt = $conn->prepare($sql)) {
                        // Hash the password
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        
                        // Bind variables to the prepared statement
                        $stmt->bind_param("ss", $username, $hashed_password);
                        
                        // Attempt to execute the prepared statement
                        if ($stmt->execute()) {
                            // Redirect to login page with success message
                            $_SESSION["success_message"] = "Registration successful! You can now login.";
                            header("Location: login.php");
                            exit;
                        } else {
                            $message = "Oops! Something went wrong. Please try again later.";
                        }
                    }
                }
            } else {
                $message = "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }

    // Close connection
    $conn->close();
}

// Display any message stored in session or from the current request
if (!empty($_SESSION["success_message"])) {
    $message = $_SESSION["success_message"];
    unset($_SESSION["success_message"]);
}

// Include HTML for displaying messages and the form
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="style2.css"> <!-- Ensure this points to the correct CSS file -->
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-TYLT0T4ZXV">
</script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-TYLT0T4ZXV');
</script>
  </head>
<body>
    <div class="auth-form">
        <?php if ($message): ?>
            <p class="error-message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <h2>Sign Up</h2>
        <form method="post" action="signup.php">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit">Register</button>
        </form>

        <p><a href="login.php">Already have an account? Log in!</a></p>
        <p><a href="index.php">Return to Homepage</a></p>
    </div>
</body>
</html>

