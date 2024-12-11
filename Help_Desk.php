<?php require("connect.php"); ?>
<?php require("Left.php"); ?>
<html>
<head>
    <link rel="stylesheet" href="style.css">

</head> 
<body>

<?php

if(isset($_POST['Send']) and !empty($_POST['message'])){

    // Validate message length
    if(strlen($_POST['message']) > 500){
        echo "Your message may not contain more than 500 characters.";
    } else {
    
        // Sanitize and escape user input before querying the database
        $message = mysql_real_escape_string($_POST['message']);
        $user_id = mysql_real_escape_string($_SESSION['user_id']);
        
        // Update the help desk ticket in the database
        $result = mysql_query("UPDATE users SET help_desk='$message' WHERE id='$user_id'") 
        or die(mysql_error());
        
        echo "<b>Your help desk ticket has been sent.</b><br />Please be patient, we will reply to your message as soon as possible.";
    }
}

?>

<form method="post">
    <table width="400" border="1">
        <tr>
            <td align="left">Help Desk: </td>
        </tr>
        <tr>
            <td align="left">Please ask a question if you don't understand</td>
        </tr>
        
        <tr>
            <td align="center"><textarea name="message" rows="10"></textarea></td>
        </tr>
        <tr>
            <td align="right"><input name="Send" type="submit" id="Send" onFocus="if(this.blur)this.blur()" value="Send."/></td>
        </tr>
    </table>
    <br />
    Please make sure that the question you ask is based on the game.
</form>

</body>
</html>
<?php require("Right.php"); ?>
