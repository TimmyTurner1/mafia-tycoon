<?php
// Include the database connection
require("connect.php");
require("Left.php");

// Sanitize and fetch the 'type' parameter from URL
$type = isset($_GET['type']) ? (int)$_GET['type'] : 1;
$type = $mysqli->real_escape_string($type);

// Admin actions (locking, unlocking, sticky, etc.)
if (isset($_POST['action']) && in_array($name, $admin_array)) {
    $action = $_POST['action'];
    if (isset($_POST['topic_id'])) {
        $topic_id = (int)$_POST['topic_id'];
        switch ($action) {
            case 'Lock':
                $query = "UPDATE topics SET locked = 2 WHERE id = ?";
                break;
            case 'Unlock':
                $query = "UPDATE topics SET locked = 1 WHERE id = ?";
                break;
            case 'Sticky':
                $query = "UPDATE topics SET topicstate = 2 WHERE id = ?";
                break;
            case 'Important':
                $query = "UPDATE topics SET topicstate = 1 WHERE id = ?";
                break;
            case 'Remove':
                $query = "DELETE FROM topics WHERE id = ?";
                break;
            case 'Delete':
                $query = "DELETE FROM topics WHERE id = ?";
                break;
            case 'Clean Forum':
                $query = "DELETE FROM topics WHERE topicstate = 0";
                break;
        }
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('i', $topic_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Pagination
$per_page = 10; // number of topics per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $per_page;

// Fetch topics based on 'topicstate' and 'type'
$sql = "SELECT * FROM topics WHERE topicstate = 1 ORDER BY id DESC LIMIT ?, ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('ii', $start, $per_page);
$stmt->execute();
$res = $stmt->get_result();

// Count total topics for pagination
$total_topics_sql = "SELECT COUNT(*) AS total FROM topics WHERE topicstate = 1";
$total_res = $mysqli->query($total_topics_sql);
$total_row = $total_res->fetch_assoc();
$total_topics = $total_row['total'];
$total_pages = ceil($total_topics / $per_page);

?>

<html>
<head>
    <title>Forum</title>
</head>
<body>

<?php require("_Forum.php"); ?>

<form method="post" id="form" name="form" action="Forum.php?type=<?php echo $type; ?>">
    <?php if (in_array($name, $admin_array)) { ?>
    <table width="500" border="0" align="center" cellpadding="0" cellspacing="2">
        <tr>
            <td align="center"><input name="action" type="submit" value="Lock" /></td>
            <td align="center"><input name="action" type="submit" value="Unlock" /></td>
            <td align="center"><input name="action" type="submit" value="Sticky" /></td>
            <td align="center"><input name="action" type="submit" value="Important" /></td>
            <td align="center"><input name="action" type="submit" value="Remove" /></td>
        </tr>
        <tr>
            <td colspan="3" align="center"><input name="action" type="submit" value="Delete" /></td>
            <td colspan="2" align="center"><input name="action" type="submit" value="Clean Forum" /></td>
        </tr>
    </table>
    <br />
    <?php } ?>

    <table width="90%" border="1" align="center">
        <tr>
            <td align="left">Main Forum</td>
            <td align="left"><b>Posts:</b></td>
        </tr>

        <?php
        while ($row = $res->fetch_assoc()) {
            $nsql = "SELECT tid FROM replys WHERE tid = ?";
            $nstmt = $mysqli->prepare($nsql);
            $nstmt->bind_param('i', $row['id']);
            $nstmt->execute();
            $nres = $nstmt->get_result();
            $msg = $nres->num_rows;
        ?>
        <tr>
            <td align="left">
                <?php if (in_array($name, $admin_array)) { ?>
                <input name="topic_id" type="radio" value="<?php echo $row['id']; ?>" />
                <?php } ?>
                <b>Important:</b>
                <a href="Forum_View.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars(stripslashes($row['title'])); ?></a>
                <?php if ($row['locked'] == 2) { echo " (Locked)"; } ?>
            </td>
            <td align="left"><?php echo $msg; ?></td>
        </tr>
        <?php } ?>

    </table>

    <br />

    <?php if ($_GET['create'] == "create" || isset($_POST['Preview'])) { ?>
    <table width="100%" border="1" align="center">
        <tr>
            <td width="75" align="left"><b>Subject:</b></td>
            <td colspan="3"><input type="text" name="title" size="40" /></td>
        </tr>
        <tr>
            <td colspan="4" align="center">
                <textarea name="message" rows="7" cols="50"></textarea>
            </td>
        </tr>
        <tr>
            <td colspan="4" align="center">
                <input type="submit" value="Preview" name="Preview" />
                <input type="submit" value="Submit" name="Submit" />
            </td>
        </tr>
    </table>
    <?php } ?>
</form>

<br />

<?php
// Display pagination
for ($i = 1; $i <= $total_pages; $i++) {
    echo "<a href='Forum.php?page=$i&type=$type'>$i</a> ";
}
?>

</body>
</html>
