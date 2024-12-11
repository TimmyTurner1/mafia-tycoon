<?php
include_once("connect.php");

// Function to validate email format
function checkEmail($str) {
    return filter_var($str, FILTER_VALIDATE_EMAIL);
}

if (isset($_POST['Send'])) {
    // Ensure email is provided
    if (empty($_POST['Email'])) {
        echo 'You left the email field empty.';
    } else {
        $email = $_POST['Email'];

        // Check if the email is valid
        if (!checkEmail($email)) {
            echo 'Your email is not valid!';
        } else {
            // Prepare SQL to prevent SQL injection
            $stmt = $conn->prepare("SELECT name, email FROM login WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Fetch user data
                $row = $result->fetch_assoc();
                $name = htmlspecialchars($row['name']);
                $mail = htmlspecialchars($row['email']);

                // Generate a random password
                $newPassword = substr(bin2hex(random_bytes(4)), 0, 6); // More secure than rand()

                // Update the password in the database
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE name = ?");
                $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT); // Store password securely
                $stmt->bind_param("ss", $hashedPassword, $name);
                $stmt->execute();

                // Send the password reset email
                $to = $email;
                $from = "no-reply@Game.co.uk";
                $subject = "Password Reset - Your New Password";
                $message = "<html>
                    <body background=\"#4B4B4B\">
                    <h1>Game Password Reset</h1>
                    Dear $name, <br>
                    <center>
                    Your Username: $name <p>
                    Your New Password: $newPassword <p>
                    </center>
                    <p>If you did not request a password reset, please contact support immediately.</p>
                    </body>
                    </html>";

                $headers = "From: Game Password Reset <no-reply@Game.co.uk>\r\n";
                $headers .= "Content-type: text/html\r\n";

                if (mail($to, $subject, $message, $headers)) {
                    echo 'We sent you an email with your new password!';
                } else {
                    echo 'Error sending email. Please try again later.';
                }
            } else {
                echo 'Invalid information.';
            }
        }
    }
}
?>

<html>
<head>
    <title>Lost Password</title>
</head>
<body>
    <form method="post">
        <center>
            <h1><strong>Lost Password</strong></h1>
            <p>Email: <input type="text" name="Email" id="Email"></p>
            <br>
            <input type="submit" name="Send" id="Send" value="Send">
        </center>
    </form>
</body>
</html>
