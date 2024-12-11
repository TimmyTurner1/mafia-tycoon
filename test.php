<?php include_once("connect.php"); ?>
<html>
<head>
    <title>Test</title>
</head>
<body>

<?php
$select_money = ''; // Initialize the variable to avoid undefined warning

if (isset($_POST['Submit'])) {
    // Use prepared statements for security
    $stmt = $connection->prepare("INSERT INTO users (name) VALUES (?)");
    $stmt->bind_param("s", $_POST['name']);
    $stmt->execute();
    echo $_POST['name'] . " ADDED";
}

// Selecting data from users table
$result = $connection->query("SELECT name, id, money FROM users ORDER BY id DESC") or die($connection->error);

// Populating the dropdown menu with existing users
$member_list = "";
while ($row = $result->fetch_assoc()) {
    if (isset($_POST['Name_list']) && $row['name'] == $_POST['Name_list']) {
        $member_list .= "<option selected=\"selected\" value=\"" . htmlspecialchars($row['name']) . "\">" . htmlspecialchars($row['name']) . "</option>";
    } else {
        $member_list .= "<option value=\"" . htmlspecialchars($row['name']) . "\">" . htmlspecialchars($row['name']) . "</option>";
    }
}

// Selecting data when "Select" or "update" is clicked
if (isset($_POST['Select']) || isset($_POST['update'])) {
    // Check if 'Name_list' is set before executing query
    if (isset($_POST['Name_list'])) {
        $stmt = $connection->prepare("SELECT * FROM users WHERE name = ?");
        $stmt->bind_param("s", $_POST['Name_list']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_object();
        $select_money = htmlspecialchars($row->money);
    }
}

// Updating the user's money
if (isset($_POST['update'])) {
    if (isset($_POST['money']) && isset($_POST['Name_list'])) {
        $stmt = $connection->prepare("UPDATE users SET money = ? WHERE name = ?");
        $stmt->bind_param("ss", $_POST['money'], $_POST['Name_list']);
        $stmt->execute();
        echo $_POST['Name_list'] . "'s money has been updated.";
    }
}
?>

<form method="post">
    <center>
        <p>ENTER NAME <br />
            <input type="text" name="name" id="name" />
            <br />
            <input type="submit" name="Submit" id="Submit" value="Submit" />
        </p>
        
        <p><br>
            <span class="cell">
                Select a Name
                <select name="Name_list" class="textbox" id="Name_list">
                    <!-- Check if 'user' is set in $_GET before using it -->
                    <option value="<?php echo isset($_GET['user']) ? htmlspecialchars($_GET['user']) : ''; ?>">Select.</option>
                    <?php echo $member_list; ?>
                </select>
            </span>
            <input type="submit" name="Select" id="Select" value="Select">
            <br>
            Change the Money
            <input type="text" name="money" id="money" value="<?php echo $select_money; ?>">
            <br>
            <input type="submit" name="update" id="update" value="Update">
            <br>
        </p>
        
        <table width="50%" border="2" cellspacing="1" cellpadding="0">
            <tr align="center">
                <td colspan="3">Users Information</td>
            </tr>
            <tr align="center">
                <td>ID</td>
                <td>Name</td>
                <td>Money</td>
            </tr>
            <?php
            $result = $connection->query("SELECT * FROM users") or die($connection->error);
            while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['money']); ?></td>
                </tr>
            <?php } ?>
        </table>
    </center>
</form>

</body>
</html>
