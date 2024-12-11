<?php 
require("Left.php"); 
require("_Crimes.php"); 

// Initialize variables for crime times
$last1 = $last2 = $last3 = $last4 = $last5 = 0; // Set these from your database or session values
$timeleft1 = $timeleft2 = $timeleft3 = $timeleft4 = $timeleft5 = 0; // Set these based on your cooldown logic

// Check if the form is submitted
if (isset($_POST['Commit'])) {
    if (isset($_POST['radiobutton']) && is_numeric($_POST['radiobutton'])) {
        $crime_id = (int) $_POST['radiobutton']; // Sanitize the input to ensure it is an integer
        // Handle committing the crime here based on the selected radio button
        switch ($crime_id) {
            case 1:
                // Logic for stealing from a child
                echo "Stealing from a child committed!";
                break;
            case 2:
                // Logic for robbing Denis' house
                echo "Robbing Denis' house committed!";
                break;
            case 3:
                // Logic for kidnapping a member from DIC STAFF
                echo "Kidnapping a member from DIC STAFF committed!";
                break;
            case 4:
                // Logic for robbing a bank
                echo "Robbing a bank committed!";
                break;
            case 5:
                // Logic for kidnapping Steve Jobs
                echo "Kidnapping Steve Jobs committed!";
                break;
            default:
                echo "Invalid crime selected.";
                break;
        }
    } else {
        echo "Please select a valid crime.";
    }
}

?>
<html>
<head>
</head> 
<body>
<form id="form1" name="form1" method="post" action="">
    <table width="90%" border="0" cellpadding="0" cellspacing="2" class="table">
        <tr>
            <td class="header">Crimes</td>
            <td class="header">Availability</td>
        </tr>
        <tr>
            <td class="cell"><input type="radio" name="radiobutton" id="radio" value="1" />
                Steal from a child 
            </td>
            <td class="cell"><?php echo ($last1 <= 0) ? "<font color=lightgreen>Available</font>" : crimemaketime($timeleft1); ?></td>
        </tr>
        <tr>
            <td class="cell"><input type="radio" name="radiobutton" id="radio2" value="2" />
                Rob Denis' house.
            </td>
            <td class="cell"><?php echo ($last2 <= 0) ? "<font color=lightgreen>Available</font>" : crimemaketime($timeleft2); ?></td>
        </tr>
        <tr>
            <td class="cell"><input type="radio" name="radiobutton" id="radio3" value="3" />
                Kidnap a member from the DIC STAFF for ransom
            </td>
            <td class="cell"><?php echo ($last3 <= 0) ? "<font color=lightgreen>Available</font>" : crimemaketime($timeleft3); ?></td>
        </tr>
        <tr>
            <td class="cell"><input type="radio" name="radiobutton" id="radio4" value="4" />
                Rob a bank
            </td>
            <td class="cell"><?php echo ($last4 <= 0) ? "<font color=lightgreen>Available</font>" : crimemaketime($timeleft4); ?></td>
        </tr>
        <tr>
            <td class="cell"><input type="radio" name="radiobutton" id="radio5" value="5" />
                Kidnap Steve Jobs for ransom
            </td>
            <td class="cell"><?php echo ($last5 <= 0) ? "<font color=lightgreen>Available</font>" : crimemaketime($timeleft5); ?></td>
        </tr>
        <tr>
            <td colspan="2" align="right" class="cell">
                <input type="submit" name="Commit" id="Commit" value="Commit" />
            </td>
        </tr>
    </table>
</form>
</body>
</html>

<?php 
require("Right.php"); 
?>
