<?php require("Left.php"); ?>

<html>
<head>
  <title>Admin Panel</title>
</head>
<body>
<?php
// Ensure the user has admin or mod privileges
if (in_array($name, $admin_array) || in_array($name, $mods_array)) {

    // Process Ban Request
    if (isset($_POST['Ban'])) {
        // Prepare SQL query using prepared statements for security
        $stmt = $conn->prepare("SELECT name, sitestate, userip FROM users WHERE name = ?");
        $stmt->bind_param("s", $_POST['ban_name']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_object();
            $banned_name = htmlspecialchars($row->name);
            $banned_state = htmlspecialchars($row->sitestate);
            $banned_ip = htmlspecialchars($row->userip);

            // Ban all users with the same IP
            if ($_POST['ban_all'] == "1") {
                $stmt = $conn->prepare("UPDATE users SET sitestate = 1 WHERE userip = ?");
                $stmt->bind_param("s", $banned_ip);
                $stmt->execute();
                echo "All users with this IP have been banned.";
            } else {
                // Ensure the user exists and is not already banned
                if (empty($banned_name)) {
                    echo "This person does not seem to exist.";
                } elseif ($banned_state == 1) {
                    echo "This person is already banned.";
                } else {
                    // Prevent banning staff
                    if (in_array($banned_name, $admin_array) || in_array($banned_name, $mods_array)) {
                        echo "<b style=\"font-size:36px;\">Can't ban staff.</b>";
                    } else {
                        // Perform ban
                        $stmt = $conn->prepare("UPDATE users SET sitestate = 1 WHERE name = ?");
                        $stmt->bind_param("s", $banned_name);
                        $stmt->execute();

                        // Record the ban reason
                        $stmt = $conn->prepare("INSERT INTO banned (name, banner, reason) VALUES (?, ?, ?)");
                        $stmt->bind_param("sss", $banned_name, $name, $_POST['reason']);
                        $stmt->execute();

                        echo "$banned_name has been banned.";
                    }
                }
            }
        } else {
            echo "This person does not seem to exist.";
        }
    }

    // Process Remove Ban Request
    if (isset($_POST['Remove_Ban'])) {
        $stmt = $conn->prepare("SELECT name, sitestate FROM users WHERE name = ?");
        $stmt->bind_param("s", $_POST['remove_ban_name']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_object();
            $banned_name = htmlspecialchars($row->name);
            $banned_state = htmlspecialchars($row->sitestate);

            // Ensure the user is banned before removing the ban
            if ($banned_state == 0) {
                echo "This person is not banned.";
            } else {
                $stmt = $conn->prepare("UPDATE users SET sitestate = 0 WHERE name = ?");
                $stmt->bind_param("s", $banned_name);
                $stmt->execute();
                echo "$banned_name has been unbanned.";
            }
        } else {
            echo "This person does not seem to exist.";
        }
    }

    // Process Murder Request
    if (isset($_POST['Murder'])) {
        $stmt = $conn->prepare("SELECT name, sitestate FROM users WHERE name = ?");
        $stmt->bind_param("s", $_POST['target']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_object();
            $target_name = htmlspecialchars($row->name);
            $target_state = htmlspecialchars($row->sitestate);

            if (in_array($target_name, $admin_array) || in_array($target_name, $mods_array)) {
                echo "<b style=\"font-size:36px;\">Can't kill staff.</b>";
            } elseif ($target_state != 0) {
                echo "Your target is already dead or banned.";
            } else {
                $stmt = $conn->prepare("UPDATE users SET sitestate = 2 WHERE name = ?");
                $stmt->bind_param("s", $target_name);
                $stmt->execute();

                echo "You shot an extreme amount of bullets at $target_name. He/She died from the shots.";
            }
        } else {
            echo "Target does not exist.";
        }
    }

?>

<form method="post">
    <table width="350" border="0" cellpadding="0" cellspacing="2" class="table">
        <tr>
            <td colspan="2" align="left" class="header">Ban Member: <?php echo htmlspecialchars($_GET['ban_name']); ?></td>
        </tr>
        <tr>
            <td align="center" class="cell">Username</td>
            <td align="center" class="cell"><input name="ban_name" type="text" id="ban_name" value="<?php echo htmlspecialchars($_GET['ban']); ?>" required /></td>
        </tr>
        <tr>
            <td align="center" valign="top" class="cell">Reason</td>
            <td align="center" valign="top" class="cell"><textarea name="reason" rows="6" id="reason" required>Duping.</textarea></td>
        </tr>
        <tr>
            <td colspan="2" align="left" class="cell"><input type="checkbox" name="ban_all" value="1" id="ban_all" /> <label for="ban_all">Ban all on IP.</label></td>
        </tr>
        <tr>
            <td colspan="2" align="right" class="cell"><input name="Ban" type="submit" id="Ban" value="Ban" /></td>
        </tr>
    </table>
    <br />
    <table width="350" border="0" cellpadding="0" cellspacing="2" class="table">
        <tr>
            <td colspan="2" align="left" class="header">Kill Player:</td>
        </tr>
        <tr>
            <td align="center" class="cell">Username</td>
            <td align="center" class="cell"><input name="target" type="text" id="target" value="<?php echo htmlspecialchars($_GET['kill']); ?>" required /></td>
        </tr>
        <tr>
            <td colspan="2" align="right" class="cell"><input name="Murder" type="submit" id="Murder" value="Murder" /></td>
        </tr>
    </table>
    <br />
    <table width="350" border="0" cellpadding="0" cellspacing="2" class="table">
        <tr>
            <td colspan="2" align="left" class="header">Unban / Revive Member:</td>
        </tr>
        <tr>
            <td align="center" class="cell">Username</td>
            <td align="center" class="cell"><input name="remove_ban_name" type="text" id="remove_ban_name" value="Name" required /></td>
        </tr>
        <tr>
            <td colspan="2" align="right" class="cell"><input name="Remove_Ban" type="submit" id="Remove_Ban" value="Remove Ban" /></td>
        </tr>
    </table>
</form>

<?php } ?>
</body>
</html>

<?php require("Right.php"); ?>
