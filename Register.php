<?php
include_once("connect.php"); // Include the connection file
require 'functions.php';      // Assuming this file contains the checkEmail function

if (isset($_POST['Register'])) {

    // Validate username length
    if(strlen($_POST['Username']) < 3 || strlen($_POST['Username']) > 32) {
        echo 'Your name must be between 3 and 32 characters!';
    } else {

        // Validate password field
        if (empty($_POST['Password'])) {
            echo 'You need to select a password!';
        } else {

            // Validate username for invalid characters
            if (preg_match('/[^a-z0-9\-\_\.]+/i', $_POST['Username'])) {
                echo 'Your name contains invalid characters!';
            } else {

                // Validate email format using a function
                if (!checkEmail($_POST['Email'])) {
                    echo 'Your email is not valid!';
                } else {

                    // Check if the terms checkbox is checked
                    if (empty($_POST['Agree'])) {
                        echo 'You need to accept the Terms & Conditions in order to sign up!';
                    } else {

                        // Sanitize email input to prevent SQL injection
                        $email = filter_var($_POST['Email'], FILTER_SANITIZE_EMAIL);

                        // Check if email already exists
                        $stmt = $mysqli->prepare("SELECT id FROM users WHERE mail = ?");
                        $stmt->bind_param('s', $email);
                        $stmt->execute();
                        $stmt->store_result();
                        $m_count = $stmt->num_rows;

                        if ($m_count >= 1) {
                            echo 'This email has already been used!';
                        } else {

                            // Sanitize and check if username already exists
                            $username = htmlspecialchars(trim($_POST['Username']));
                            $stmt = $mysqli->prepare("SELECT id FROM users WHERE name = ?");
                            $stmt->bind_param('s', $username);
                            $stmt->execute();
                            $stmt->store_result();
                            $m_count = $stmt->num_rows;

                            if ($m_count >= 1) {
                                echo 'This name has already been used!';
                            } else {

                                // Hash the password using a more secure method
                                $password = password_hash($_POST['Password'], PASSWORD_DEFAULT);

                                // Insert the new user into the database
                                $stmt = $mysqli->prepare("INSERT INTO users (name, password, mail) VALUES (?, ?, ?)");
                                $stmt->bind_param('sss', $username, $password, $email);
                                if ($stmt->execute()) {

                                    // Send a registration confirmation email
                                    $to = $email;
                                    $from = "no-reply@Game.co.uk";
                                    $subject = "Registration - Your Registration Details";

                                    $message = "<html>
                                        <body background=\"#4B4B4B\">
                                            <h1>Game Registration Details</h1>
                                            Dear ".$username.", <br>
                                            <center>
                                            Your Username: ".$username."<p>
                                            Your Password: (hashed and stored securely)<p>
                                            <p><font size=3> You received this mail because someone used this email to sign up to a game.</font>
                                        </body>
                                    </html>";

                                    $headers  = "From: Game Registration Details <no-reply@Game.co.uk>\r\n";
                                    $headers .= "Content-type: text/html\r\n";

                                    mail($to, $subject, $message, $headers);

                                    echo $username.", Welcome to the game.";
                                } else {
                                    echo "There was an error during registration. Please try again later.";
                                }
                            }
                        }
                    }
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
    <title>Register - Game</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to your CSS file -->
</head>
<body>
    <form method="post">
        <center>
            <h1><strong>Register</strong></h1>
            <p>UserName: 
                <input type="text" name="Username" id="Username" required>
            </p>
            <p>Password:
                <input type="password" name="Password" id="Password" required>
            </p>
            <p>Email: 
                <input type="email" name="Email" id="Email" required>
            </p>
            <p>
                <input type="checkbox" name="Agree" id="Agree" required>
                Agree to Terms of Services
                <br>
                <input type="submit" name="Register" id="Register" value="Register">
            </p>
        </center>
    </form>
</body>
</html>
