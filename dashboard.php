<?php

// Start the session
session_start();

// If the user is not logged in, redirect to the login page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Get the username from the session
$username = $_SESSION['username'];

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

// If the user is 'root', fetch all users from the database
$users = [];
if ($username == "root") {
    $result = $mysqli->query("SELECT username, password FROM users");
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// If the request method is POST and the user is 'root', handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $username == 'root') {
    // If a message was posted, insert it into the notifications table
    if (isset($_POST['message'])) {
        $message = $_POST['message'];
        $stmt = $mysqli->prepare("INSERT INTO notifications (message) VALUES (?)");
        $stmt->bind_param("s", $message);
        $stmt->execute();
    } 
    // If a delete_id was posted, delete the corresponding notification
    elseif (isset($_POST['delete_id'])) {
        $id = $_POST['delete_id'];
        $stmt = $mysqli->prepare("DELETE FROM notifications WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
}

// Fetch all notifications from the database
$result = $mysqli->query("SELECT id, message, timestamp FROM notifications ORDER BY timestamp DESC");
$notifications = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to the CSS stylesheet -->
</head>
<body class="dashboard-body">
    <h2 class="welcome-message">
        <?php 
        // If the username is not empty, display a personalized welcome message
        // If the username is 'root', indicate that the user is an admin
        // Otherwise, display a generic welcome message
        if (!empty($username)) {
            echo "Hello, " . htmlspecialchars($username);
            if ($username == "root") {
                echo " (admin)";
            }
        } else {
            echo "Welcome!";
        }
        ?>!
    </h2>
    <?php if ($username == "root"): ?> <!-- If the user is 'root', display the user menu -->
        <button onclick="toggleNav()" class="open-button">Open User Menu</button>
        <div id="mySidenav" class="sidenav">
            <?php foreach ($users as $user): ?> <!-- For each user, display their username and a delete button -->
                <div class="user-entry">
                    <span><?php echo htmlspecialchars($user['username']); ?></span>
                    <form action="delete_user.php" method="post" class="delete-form">
                        <input type="hidden" name="username" value="<?php echo htmlspecialchars($user['username']); ?>">
                        <button type="submit" class="delete-button">Delete</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <a href="logout.php" class="logout-link">Logout</a> <!-- Logout link -->
</body>
</html>

<script>
    // Function to toggle the navigation menu
    function toggleNav() {
        var nav = document.getElementById("mySidenav");
        // If the navigation menu is open, close it
        // If it's closed, open it
        if (nav.style.width === "250px") {
            nav.style.width = "0";
        } else {
            nav.style.width = "250px";
        }
    }

    // Get the username from the PHP session
    var username = "<?php echo $username; ?>"; 

    // If the user is not 'root', fetch notifications every second
    if (username !== 'root') {
        setInterval(function() {
            fetch('fetch_notifications.php')
                .then(response => response.json())
                .then(notifications => {
                    // Clear the dropdown menu
                    const dropdownContent = document.querySelector('.dropdown-content');
                    dropdownContent.innerHTML = '';

                    // Add each notification to the dropdown menu
                    notifications.forEach(notification => {
                        const a = document.createElement('a');
                        a.innerHTML = `
                            <p>${notification.message}</p>
                            <p><small>${notification.timestamp}</small></p>
                        `;
                        dropdownContent.appendChild(a);
                    });
                });
        }, 1000);
    }
</script>

<?php if ($username == 'root'): ?> <!-- If the user is 'root', display the notification creation form -->
<form action="dashboard.php" method="post" class="notification-form">
    <textarea name="message" required></textarea> <!-- Textarea for the notification message -->
    <button type="submit">Create Notification</button> <!-- Button to submit the form and create the notification -->
</form>
<?php endif; ?>

<div class="dropdown"> <!-- Dropdown menu for notifications -->
    <div class="dropdown-content">
        <?php foreach ($notifications as $notification): ?> <!-- For each notification, display its message and timestamp -->
        <a href="#">
            <p><?php echo htmlspecialchars($notification['message']); ?></p> <!-- Notification message -->
            <p><small><?php echo htmlspecialchars($notification['timestamp']); ?></small></p> <!-- Notification timestamp -->
            <?php if ($username == 'root'): ?> <!-- If the user is 'root', display a delete button for the notification -->
            <form action="dashboard.php" method="post">
                <input type="hidden" name="delete_id" value="<?php echo $notification['id']; ?>"> <!-- Hidden input with the notification ID -->
                <button type="submit">Delete</button> <!-- Button to submit the form and delete the notification -->
            </form>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>