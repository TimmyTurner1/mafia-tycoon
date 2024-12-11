<?php require("connect.php"); ?>
<?php require("Left.php"); ?>
<html>
<head>
</head> 
<body>

<?php if (in_array($name, $admin_array) or in_array($name, $manager_array) or in_array($name, $hdo_array)){ ?>

<?php 
// Clear the help desk message for a specific user if the 'name' parameter is provided
if(!empty($_GET['name'])){
    $name = mysql_real_escape_string($_GET['name']);
    $result = mysql_query("UPDATE users SET help_desk='' WHERE name='$name'") or die(mysql_error());
}
?>

<form method="post">
    <table width="550" border="1" align="center">
        <?php 
        // Query to get all help desk messages and usernames
        $result = mysql_query("SELECT help_desk, name FROM users ORDER BY name DESC") or die(mysql_error());

        // Loop through each row and display the help desk messages
        while($row = mysql_fetch_array($result)) {
            $row['help_desk'] = nl2br(htmlspecialchars(stripslashes($row['help_desk'])));

            // Only display rows with a help desk message
            if(!empty($row['help_desk'])){
        ?>
            <tr>
                <td colspan="2" align="left">Help Desk:</td>
            </tr>
            <tr>
                <td colspan="2" align="left">
                    <?php echo "<a href=\"View_Profile.php?name=".$row['name']."\" onFocus=\"if(this.blur)this.blur()\">".$row['name']."</a>"; ?> Wrote:
                </td>
            </tr>
            <tr>
                <td colspan="2" align="left"><?php echo $row['help_desk']; ?></td>
            </tr>
            <tr>
                <td width="275" align="left">
                    <?php echo "<a href=\"?name=".$row['name']."\" onFocus=\"if(this.blur)this.blur()\">Delete.</a>"; ?>
                </td>
                <td width="275" align="right">
                    <?php echo "<a href=\"Send_Message.php?name=".$row['name']."&action=helpdesk\" onFocus=\"if(this.blur)this.blur()\">Reply.</a>"; ?>
                </td>
            </tr>
        <?php 
            } // if there's a help desk message
        } // while
        ?>
    </table>
</form>

<?php } // check if the user has admin, manager, or hdo privileges ?>

</body>
</html>
<?php require("Right.php"); ?>
