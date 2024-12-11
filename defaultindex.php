<?php
include_once("connect.php");

if(isset($_SESSION['user_id'])) {
    // If already logged in, destroy session
    session_unset();
    session_destroy();
    echo "<label>You have been logged out.</label>";
}

if(isset($_POST['Login'])) {
    $username = $_POST['Username'];
    $password = $_POST['password'];

    // Validate username
    if (preg_match('/[^A-Za-z0-9]/', $username)) {
        echo "Invalid Username.";
    } else {
        // Using prepared statements for secure query execution
        $stmt = $conn->prepare("SELECT * FROM users WHERE name = ?");
        $stmt->bind_param('s', $username); // 's' denotes a string parameter
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (empty($row['id'])) {
            echo "Account doesn't exist.";
        } else {
            // Verify password using password_verify
            if (!password_verify($password, $row['password'])) {
                echo "Your password is incorrect.";
            } else {
                // Handling login IP
                $login_ip = !empty($row['login_ip']) ? $row['login_ip'] : $_SERVER['REMOTE_ADDR'];
                $ip_information = explode("-", $login_ip);

                if (!in_array($_SERVER['REMOTE_ADDR'], $ip_information)) {
                    $login_ip .= "-" . $_SERVER['REMOTE_ADDR'];
                }

                // Update user IP information
                $update_stmt = $conn->prepare("UPDATE users SET userip = ?, login_ip = ? WHERE id = ?");
                $update_stmt->bind_param('ssi', $_SERVER['REMOTE_ADDR'], $login_ip, $row['id']);
                $update_stmt->execute();

                // Start session and redirect based on account type
                $_SESSION['user_id'] = $row['id'];
                session_regenerate_id(true); // Regenerate session ID to avoid session fixation

                if ($row['account_type'] == 1) {
                    header("Location: Usersonline.php");
                } else {
                    echo "You Were Killed";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Login</title>
</head>
<body>
    <form id="form1" name="form1" method="post" action="">
        <center>
            <h2>GAME LOGIN</h2>
            <label for="Username">Username:</label>
            <input type="text" name="Username" id="Username" required />
            <br /><br />
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required />
            <br /><br />
            <input type="submit" name="Login" id="Login" value="Login" />
        </center>
    </form>
</body>
</html>
