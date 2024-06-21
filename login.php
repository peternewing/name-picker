<?php
require 'db_connection.php'; // Include your DB connection script

session_start();

// Initialize an error message variable
$_SESSION["error_message"] = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    
    // Validate input
    if (empty($username) || empty($password)) {
        $_SESSION["error_message"] = "Please fill all the fields.";
        header("Location: login.php"); // Redirect back to the login page
        exit;
    }

    // Authenticate user
    $sql = "SELECT id, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($id, $hashed_password);
    if ($stmt->fetch()) {
        if (password_verify($password, $hashed_password)) {
            // Password is correct, start a new session
            $_SESSION["loggedin"] = true;
            $_SESSION["id"] = $id;
            $_SESSION["user_id"] = $id;
            $_SESSION["username"] = $username;
            
            // Redirect to index.php
            header("Location: index.php");
            exit;
        } else {
            $_SESSION["error_message"] = "Invalid username or password.";
            header("Location: login.php"); // Redirect back to the login page
            exit;
        }
    } else {
        $_SESSION["error_message"] = "Invalid username or password.";
        header("Location: login.php"); // Redirect back to the login page
        exit;
    }
    $stmt->close();
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
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
    <!-- Centered login form with modern styling -->
    <div class="auth-form">
        <h2>Login</h2>
        <form method="post" action="login.php">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
        <p><a href="signup.php">Sign up for an account</a></p>
        <p><a href="index.php">Return to Homepage</a></p>
    </div>
</body>
</html>


