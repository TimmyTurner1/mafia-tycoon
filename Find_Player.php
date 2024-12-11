<?php 
require("Left.php");
session_start(); // Start the session to manage user data, if necessary

// Database connection setup (make sure to update your database credentials)
$mysqli = new mysqli("localhost", "username", "password", "database"); // Replace with your credentials

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

?>

<html>
<head>
</head> 
<body>
<form method="post">
    <?php if (!isset($_POST['Search'])) { ?>
        <table width="90%" border="1">
            <tr>
                <td colspan="2" align="left">Search Player: </td>
            </tr>
            <tr>
                <td width="50" align="left">Name: </td>
                <td width="300" align="center">
                    <input name="player_name" type="text" id="player_name" style="width: 95%;" maxlength="20" />
                </td>
            </tr>
            <tr>
                <td colspan="2" align="right" background="../img/bg/test12.png">
                    <input name="Search" type="submit" id="Search" value="Search." onFocus="if(this.blur)this.blur()" />
                </td>
            </tr>
        </table>
    <?php } // searchbox. ?>

    <?php
    if (isset($_POST['Search'])) {
        $player_name = trim($_POST['player_name']); // Sanitize user input

        if (empty($player_name)) {
            echo "Empty search field.";
        } else {
            if (strlen($player_name) > 20) {
                echo "The username may not consist of more than 20 characters.";
            } elseif (!preg_match('/^[A-Za-z0-9]+$/', $player_name)) {
                echo "Invalid Name: only A-Z, a-z, and 0-9 are allowed.";
            } else {
                // Prepare the SQL query with parameter binding to avoid SQL injection
                $stmt = $mysqli->prepare("SELECT name FROM users WHERE name LIKE ? ORDER BY name ASC");
                $search_term = "%" . $player_name . "%"; // For partial match
                $stmt->bind_param("s", $search_term); // "s" for string parameter
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows == 0) {
                    echo "There is no player with that name.";
                } else {
                    ?>
                    <br />
                    <table width="250" border="1" align="center">
                        <tr>
                            <td align="left">Results:</td>
                        </tr>
                        <tr>
                            <td align="center">Name:</td>
                        </tr>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td align="center">
                                    <label>
                                        <a href="View_Profile.php?name=<?php echo htmlspecialchars($row['name']); ?>" 
                                           onFocus="if(this.blur)this.blur()"><?php echo htmlspecialchars($row['name']); ?></a>
                                    </label>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                    <?php
                }
                $stmt->close(); // Close the statement after executing
            }
        }
    }
    ?>
</form>
</body>
</html>

<?php 
require("Right.php");
$mysqli->close(); // Close the database connection after use
?>
