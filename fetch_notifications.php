<?php
// Database connection
$mysqli = new mysqli('localhost', 'root', 'Root', 'termin');
if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => $mysqli->connect_error]);
    exit;
}

// Fetch all notifications
$result = $mysqli->query("SELECT message, timestamp FROM notifications ORDER BY timestamp DESC");
if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => $mysqli->error]);
    exit;
}

$notifications = $result->fetch_all(MYSQLI_ASSOC);

// Return notifications as JSON
header('Content-Type: application/json');
echo json_encode($notifications);