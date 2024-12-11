<?php 
require("connect.php"); // Include your connection file for the database
require("Left.php"); 
?>

<html>
<head>
    <link rel="stylesheet" href="style.css">

</head> 
<body>

<?php
// Check if user has necessary permissions to access inbox page
if (isset($name)) {

    // Small Pagination code
    $amount = 6; // Number of messages per page

    // Query to get messages for the logged-in user with 'del' = 1
    $nsql = "SELECT * FROM pm WHERE sendto=? AND del='1'";
    $stmt = $conn->prepare($nsql);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $nres = $stmt->get_result();
    $totalmail = $nres->num_rows;

    // Determine the current page number
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
    $min = $amount * ($page - 1);
    
    // Calculate the total number of pages
    $numofpages = ceil($totalmail / $amount);

    // Pagination links
    if ($page > 1) {
        $previouspage = $page - 1;
        echo "<a href=\"Inbox.php?page=".$previouspage."\" onFocus=\"if(this.blur)this.blur()\">Previous</a>";
    }
?>
    <form method="post">
        <table width="319" border="2" align="center">
            <tr>
                <td width="40" align="center">
                    <?php if ($totalmail >= 1 && !isset($_POST['Clean'])): ?>
                        <input name="Clean" type="submit" id="Clean" value="Clean Inbox" onFocus="if(this.blur)this.blur()"/>
                    <?php endif; ?>
                </td>
                <td width="101" align="center">
                    <?php if ($totalmail >= 1 && !isset($_POST['Clean'])): ?>
                        <input name="Delete" type="submit" id="Delete" value="Delete Selected" onFocus="if(this.blur)this.blur()"/>
                    <?php endif; ?>
                </td>
                <td width="34" align="center">
                    <?php if ($page < $numofpages): 
                        $pagenext = $page + 1;
                        echo "<a href=\"Inbox.php?page=".$pagenext."\" onFocus=\"if(this.blur)this.blur()\">Next</a>";
                    endif; ?>
                </td>
            </tr>
        </table>

        <?php
        // Fetch the messages for the current page
        $presult = $conn->prepare("SELECT * FROM pm WHERE sendto=? AND del='1' ORDER BY id DESC LIMIT ?, ?");
        $presult->bind_param("sii", $name, $min, $amount);
        $presult->execute();
        $result = $presult->get_result();

        if ($result->num_rows > 0):
            // Display messages
            while ($row = $result->fetch_assoc()):
                $message = nl2br(htmlspecialchars(stripslashes($row['message'])));
        ?>
            <table width="90%" border="1">
                <tr>
                    <td align="left">
                        <span class="text">
                            <span class="head">
                                <?php echo "<a href=\"View_Profile.php?name=" . $row['sendby'] . "\" onFocus=\"if(this.blur)this.blur()\">" . $row['sendby'] . "</a>"; ?>
                            </span>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td align="left"><?php echo $message; ?></td>
                </tr>
                <tr>
                    <td align="left">
                        <table width="100%" border="1">
                            <tr>
                                <td width="25" align="left">
                                    <input type="checkbox" name="id[<?php echo $row['id']; ?>]" value="<?php echo $row['id']; ?>" onFocus="if(this.blur)this.blur()"/>
                                </td>
                                <td width="50" align="center">
                                    <?php echo "<a href=\"Send_Message.php?name=" . $row['sendby'] . "&reply=" . $row['id'] . "\" onFocus=\"if(this.blur)this.blur()\">Reply</a>"; ?>
                                </td>
                                <td width="50" align="center">
                                    <?php echo "<a href=\"Inbox.php?delete=" . $row['id'] . "\" onFocus=\"if(this.blur)this.blur()\">Delete</a>"; ?>
                                </td>
                                <td width="50" align="right"><?php echo $row['time']; ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <br />
        <?php
            endwhile;
        else:
            echo "You don't have any messages.";
        endif;
        ?>
    </form>

<?php } // end of user validation ?>

</body>
</html>

<?php require("Right.php"); ?>
