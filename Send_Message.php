<?php require("Left.php"); ?>
<html>
<head>
</head> 
<body>

<?php
require("_Send_Message.php");
?>

<form id="messageform" name="messageform" method="post">
<table width="90%" border="1">
  <tr>
    <td colspan="2" align="left">Send Letter: </td>
  </tr>
  <tr>
    <td width="75" align="left"><b>Send to: </b></td>
    <td width="475" align="center"><input name="sendto" type="text" style='width: 98%;' value='<?php echo htmlspecialchars($_GET['name']); ?>' maxlength="110" /></td>
  </tr>
  <tr>
    <td width="75" align="left" valign="top"><b>Message: </b></td>
    <td width="475" align="center">
      <textarea name="message" rows="10"><?php 
      
      if ($_GET['action'] == "helpdesk" && (in_array($name, $admin_array) || in_array($name, $manager_array) || in_array($name, $hdo_array))) {

          $stmt = $mysqli->prepare("SELECT help_desk FROM users WHERE name=?");
          $stmt->bind_param("s", $_GET['name']);
          $stmt->execute();
          $result = $stmt->get_result();
          $row = $result->fetch_object();
          $help_desk = htmlspecialchars($row->help_desk);
          $stmt->close();

          echo "[hr][b]Your question was:[/b]\n\n" . stripslashes($help_desk);
      }

      if (!empty($_GET['reply'])) {
          $stmt = $mysqli->prepare("SELECT * FROM pm WHERE id=?");
          $stmt->bind_param("i", $_GET['reply']);
          $stmt->execute();
          $result = $stmt->get_result();
          $row = $result->fetch_array();
          $stmt->close();

          if ($row['sendto'] != $name) {
              echo "You are not allowed to view this information.";
          } else {
              echo "[b]" . htmlspecialchars($row['sendby']) . " wrote:[/b]\n\n" . htmlspecialchars(stripslashes($row['message']));
          }
      }

      ?></textarea>
    </td>
  </tr>
  <tr>
    <td colspan="2" align="right" valign="top"><input name="Send" type="submit" value="Send." onfocus="if(this.blur)this.blur()" /></td>
  </tr>
</table>
<p>&nbsp;</p>
<br />
</form>

</body>
</html>

<?php require("Right.php"); ?>
