<?php 
require("Left.php");
session_start(); // Start session to access session variables like user_id

// Ensure database connection
$mysqli = new mysqli("localhost", "username", "password", "database"); // Update with your credentials

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

if (isset($_POST['Quote'])) {
    // Ensure the user is logged in
    if (isset($_SESSION['user_id'])) {
        $quote = trim($_POST['quote_box']); // Sanitize the input
        if (!empty($quote)) {
            // Using prepared statements to update the quote securely
            $stmt = $mysqli->prepare("UPDATE users SET profile = ? WHERE id = ?");
            $stmt->bind_param("si", $quote, $_SESSION['user_id']); // "si" means string and integer
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo "Your quote has been updated.";
            } else {
                echo "Error: Quote update failed.";
            }
            $stmt->close();
        } else {
            echo "Error: Quote cannot be empty.";
        }
    } else {
        echo "Error: User is not logged in.";
    }
}

// Fetch the current quote for display (ensure proper security)
$stmt = $mysqli->prepare("SELECT profile FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($profile);
$stmt->fetch();
$stmt->close();
?>
<html>
<head>
</head>
<body>
<form id="form1" name="form1" method="post" action="">
    <table width="90%" border="1" align="center">
        <tr>
            <td align="left">Quote:</td>
        </tr>
        <tr>
            <td align="center">
                <textarea name="quote_box" cols="50" rows="10" id="quote_box"><?php echo htmlspecialchars(stripslashes($profile)); ?></textarea>
            </td>
        </tr>
        <tr>
            <td height="29" align="right">
                <input name="Quote" type="submit" id="Quote" value="Update Quote." onFocus="if(this.blur)this.blur()" />
            </td>
        </tr>
    </table>
</form>
</body>
</html>

<?php 
require("Right.php");
$mysqli->close(); // Close the database connection
?>
