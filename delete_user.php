<?php

// Start the session
session_start();

// If the user is not logged in or is not 'root', redirect to the login page
if (!isset($_SESSION['username']) || $_SESSION['username'] != "root") {
    header("Location: login.php");
    exit();
}

// If the request method is POST, handle the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the username to delete from the form submission
    $usernameToDelete = $_POST["username"];

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

    // Prepare a SQL statement to delete the user
    $stmt = $mysqli->prepare("DELETE FROM users WHERE username = ?");
    $stmt->bind_param("s", $usernameToDelete);
    $stmt->execute();

    // Redirect to the dashboard
    header("Location: dashboard.php");
    exit();
}