<?php
// Database connection parameters
$host = 'localhost'; 
$db   = 'termin'; 
$user = 'root'; 
$pass = 'Root'; 
$charset = 'utf8mb4';

// Data Source Name (DSN) for the database connection
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// PDO options
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on error
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Fetch results as associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false, // Use real prepared statements
];

// Create a new PDO instance
$pdo = new PDO($dsn, $user, $pass, $opt);

// If the request method is POST, handle the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the username and password from the form submission
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT); // Hash the password

    // Prepare a SQL statement to insert the new user into the database
    $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
    $stmt= $pdo->prepare($sql);
    $stmt->execute([$username, $password]);

    // Redirect to the login page
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration Form</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to the CSS stylesheet -->
</head>
<body>
    <h2>Registration Form</h2>
    <form id="registrationForm" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br> <!-- Input for the username -->

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br> <!-- Input for the password -->

        <button type="submit">Register</button> <!-- Button to submit the form and register -->
        <a href="index.html">cancel</a> <!-- Link to cancel registration and return to the index page -->
    </form>
</body>
</html>