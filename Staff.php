<?php require("Left.php"); ?>

<html>
<head>
</head> 
<body>
<?php
// Handle adding admin
if (isset($_POST['Add_admin'])) {
    if (empty($_POST['admin'])) {
        echo "You didn't enter a name.";
    } else {
        if (in_array($_POST['admin'], $admin_array)) {
            echo "This person is already an admin.";
        } else {
            // Prepare SQL query to check if the user exists
            $stmt = $mysqli->prepare("SELECT name FROM users WHERE name=?");
            $stmt->bind_param("s", $_POST['admin']);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($admin_name);
            $stmt->fetch();
            $stmt->close();

            if (empty($admin_name)) {
                echo "This user doesn't exist.";
            } else {
                // Update sitestats and admins list
                if (empty($admins)) {
                    $stmt = $mysqli->prepare("UPDATE sitestats SET admins=?, admins_ip=? WHERE id='1'");
                    $stmt->bind_param("ss", $_POST['admin'], $_SERVER['REMOTE_ADDR']);
                    $stmt->execute();
                    $stmt->close();

                    $admins = $_POST['admin'];
                } else {
                    $new_admin = $admins . "-" . $_POST['admin'];
                    $new_ip = $admins_ip . "-" . $_SERVER['REMOTE_ADDR'];

                    $stmt = $mysqli->prepare("UPDATE sitestats SET admins=?, admins_ip=? WHERE id='1'");
                    $stmt->bind_param("ss", $new_admin, $new_ip);
                    $stmt->execute();
                    $stmt->close();

                    $admins = $new_admin;
                }

                echo "You added " . htmlspecialchars($admin_name) . " as new admin.";
            }
        }
    }
}

// Handle removing admin
if (isset($_POST['Remove_admin'])) {
    if (empty($_POST['admin_name'])) {
        echo "You didn't select a person to demote from admin.";
    } else {
        if (!in_array($_POST['admin_name'], $admin_array)) {
            echo "This person isn't an admin.";
        } else {
            // Remove selected admin from admins list
            $new_admin = "";
            foreach ($admin_array as $key => $value) {
                if ($value != $_POST['admin_name']) {
                    $new_admin .= (empty($new_admin) ? "" : "-") . $value;
                }
            }

            $new_ip = "";
            foreach ($admin_ip_array as $key => $value) {
                if ($value != $_POST['admin_name']) {
                    $new_ip .= (empty($new_ip) ? "" : "-") . $value;
                }
            }

            // Update sitestats and users table
            $stmt = $mysqli->prepare("UPDATE sitestats SET admins=?, admins_ip=? WHERE id='1'");
            $stmt->bind_param("ss", $new_admin, $new_ip);
            $stmt->execute();
            $stmt->close();

            $stmt = $mysqli->prepare("UPDATE users SET state='0' WHERE id=?");
            $stmt->bind_param("s", $_POST['admin_name']);
            $stmt->execute();
            $stmt->close();

            $admins = $new_admin;

            echo "You removed " . $_POST['admin_name'] . " from their admin position.";
        }
    }
}
?>

<?php if (in_array($name, $admin_array)) { ?>
<form name="form1" method="post" action="">
  <table width="90%" border="1">
    <tr>
      <td colspan="4" align="left">Admins</td>
    </tr>
    <tr>
      <td colspan="4" align="center">
        <?php
        if (empty($admins)) {
            echo "There are no admins.";
        } else {
            $admins_list = explode("-", $admins);
            foreach ($admins_list as $key => $value) {
                echo "<input name=\"admin_name\" type=\"radio\" value=\"" . htmlspecialchars($value) . "\" onFocus=\"if(this.blur)this.blur()\">" . htmlspecialchars($value) . "<br>";
            }
        }
        ?>
      </td>
    </tr>
    <tr>
      <td width="50" align="left"><b>Name:</b></td>
      <td align="center"><input name="admin" type="text" id="admin" style='width: 95%;' maxlength="20" /></td>
      <td width="100" align="right"><input name="Add_admin" type="submit" id="Add_admin" value="Add." onFocus="if(this.blur)this.blur()" /></td>
      <td width="100" align="right"><input name="Remove_admin" type="submit" id="Remove_admin" value="Remove." onFocus="if(this.blur)this.blur()" /></td>
    </tr>
  </table>
  <br>
  <table width="90%" border="1">
    <tr>
      <td colspan="4" align="left">MOD List: </td>
    </tr>
    <tr>
      <td height="20" colspan="4" align="center">Names of Mods will appear here</td>
    </tr>
    <tr>
      <td width="50" align="left"><b>Name:</b></td>
      <td align="center"><input name="Mod" type="text" id="Mod" style='width: 95%;' maxlength="20" /></td>
      <td width="100" align="right"><input name="Add_mod" type="submit" id="Add_mod" value="Add." onFocus="if(this.blur)this.blur()" /></td>
      <td width="100" align="right"><input name="Remove_Mod" type="submit" id="Remove_Mod" value="Remove." onFocus="if(this.blur)this.blur()" /></td>
    </tr>
  </table>
  <br>
  <table width="90%" border="1">
    <tr>
      <td colspan="4" align="left">Hdo List: </td>
    </tr>
    <tr>
      <td colspan="4" align="center">Names of HDOs will appear here</td>
    </tr>
    <tr>
      <td width="50" align="left"><b>Name:</b></td>
      <td align="center"><input name="hdo" type="text" id="hdo" style='width: 95%;' maxlength="20" /></td>
      <td width="100" align="right"><input name="Add_hdo" type="submit" id="Add_hdo" onFocus="if(this.blur)this.blur()" value="Add." /></td>
      <td width="100" align="right"><input name="Remove_hdo" type="submit" id="Remove_hdo" value="Remove." onFocus="if(this.blur)this.blur()" /></td>
    </tr>
  </table>
</form>
<?php } ?>
</body>
</html>

<?php require("Right.php"); ?>
