<?php
session_start(); // Start the session

// Include the database connection
include('connect.php');

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    // Fetch user details to confirm session validity
    $stmt = $mysqli->prepare("SELECT account_type FROM users WHERE id = ?");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Redirect based on account type
        if ($user['account_type'] === 'admin') {
            header("Location: Usersonline.php");
            exit(); // Prevent further execution
        } else {
            header("Location: GameDashboard.php");
            exit(); // Prevent further execution
        }
    } else {
        // Invalid session; clear it
        session_unset();
        session_destroy();
        $error = "Invalid session. Please log in again.";
    }
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Login'])) {
    // Get and sanitize input
    $username = trim($_POST['Username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        // Prepare the SQL query to check user credentials
        $stmt = $mysqli->prepare("SELECT * FROM users WHERE name = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['account_type'] = $user['account_type'];

                // Regenerate session ID to avoid session fixation
                session_regenerate_id(true);

                // Redirect based on account type
                if ($user['account_type'] === 'admin') {
                    header("Location: Usersonline.php");
                } else {
                    header("Location: GameDashboard.php");
                }
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            $error = "User not found.";
        }
    } else {
        $error = "Both fields are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <form id="form1" name="form1" method="post" action="">
        <center>
            <h2>GAME LOGIN</h2>
            <?php if (isset($error)): ?>
                <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <label for="Username">Username:</label>
            <input type="text" name="Username" id="Username" required />
            <br /><br />
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required />
            <br /><br />
            <input type="submit" name="Login" id="Login" value="Login" />
            <br><br>
            <a href="Register.php">Register</a> | <a href="Lost_Pass.php">Forgot Password?</a>
        </center>
    </form>
</body>
</html>
