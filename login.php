<?php
// Start the session
session_start();

// Database connection parameters
$host = 'localhost'; 
$db   = 'termin'; 
$user = 'root'; 
$pass = 'Root'; 

// Connect to the database
$mysqli = new mysqli($host, $user, $pass, $db);

// If the database connection failed, end the script
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// If the request method is POST, handle the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the username and password from the form submission
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Prepare a SQL statement to get the password for the username
    $stmt = $mysqli->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // If the user exists and the password is correct, log in the user and redirect to the dashboard
    // Otherwise, display an error message
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $username; 
        header("Location: dashboard.php");
        exit();
    } else {
        $error_message = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Form</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to the CSS stylesheet -->
</head>
<body>
    <h2>Login Form</h2>
    <?php if (isset($error_message)): ?> <!-- If there is an error message, display it -->
        <p><?php echo $error_message; ?></p>
    <?php endif; ?>
    <form id="loginForm" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br> <!-- Input for the username -->

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br> <!-- Input for the password -->

        <button type="submit">Login</button> <!-- Button to submit the form and log in -->
        <a href="index.html">cancel</a> <!-- Link to cancel login and return to the index page -->
    </form>
</body>
</html>