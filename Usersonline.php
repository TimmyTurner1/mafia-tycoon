<?php 
session_start(); // Start the session at the beginning

require("Left.php"); 
require("connect.php"); // Include your database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if no active session
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Online</title>
	    <link rel="stylesheet" href="style.css">

</head>
<body>
    <table width="90%" height="94" border="0" cellpadding="0" cellspacing="2" class="table">
        <tr>
            <td height="23" align="center" class="header">Users online</td>
        </tr>
        <tr>
            <td class="cell">
                <?php
                // Check if the database connection exists
                if ($mysqli->connect_error) {
                    die("Database connection failed: " . $mysqli->connect_error);
                }

                // Query to get users active within the last 5 minutes
                $sql = "SELECT name FROM users WHERE DATE_SUB(NOW(), INTERVAL 5 MINUTE) <= lastactive ORDER BY id ASC";
                $result = $mysqli->query($sql);

                if ($result && $result->num_rows > 0) {
                    $count = $result->num_rows;
                    $i = 1;

                    // Loop through each user and display their name
                    while ($row = $result->fetch_object()) {
                        $online_name = htmlspecialchars($row->name);
                        echo "<a href=\"View_Profile.php?name=" . $online_name . "\" onFocus=\"if(this.blur)this.blur()\">" . $online_name . "</a>";

                        if ($i != $count) {
                            echo " - ";
                        }
                        $i++;
                    }
                    echo "<p><center>Total Online: " . $count . "</center></p>";
                } else {
                    echo "No users are online.";
                }

                // Note: Connection closure should not be required here if using persistent connections.
                ?>
            </td>
        </tr>
    </table>
</body>
</html>

<?php require("Right.php"); ?>
