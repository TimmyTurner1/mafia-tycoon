<?php
// Ensure safe and correct page URL handling
$page_url = explode(".", $_SERVER['REQUEST_URI']);
$_SERVER['REQUEST_URI'] = $page_url[0] . ".php";

if ($_SERVER['REQUEST_URI'] == "/_Inbox.php") {
    exit();
}

// Establish MySQLi connection
$mysqli = new mysqli("localhost", "username", "password", "database_name");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Clean inbox (mark all as deleted)
if (isset($_POST['Clean'])) {
    $stmt = $mysqli->prepare("UPDATE pm SET del = '2' WHERE sendto = ?");
    $stmt->bind_param("s", $name);  // Assuming $name is the sender's username
    $stmt->execute();
    $stmt->close();

    echo "Your Inbox has been cleaned.";
}

// Delete a specific message
if (!empty($_GET['delete'])) {
    $stmt = $mysqli->prepare("UPDATE pm SET del = '2' WHERE sendto = ? AND id = ?");
    $stmt->bind_param("si", $name, $_GET['delete']);
    $stmt->execute();
    $stmt->close();
}

// Bulk delete selected messages
if (isset($_POST['Delete'])) {
    $id = $_POST['id'];
    if (!empty($id)) {
        $delete = implode(",", $id);
        $delete = explode(",", $delete);

        foreach ($delete as $messageId) {
            $stmt = $mysqli->prepare("UPDATE pm SET del = '2' WHERE sendto = ? AND id = ?");
            $stmt->bind_param("si", $name, $messageId);
            $stmt->execute();
            $stmt->close();
        }

        echo "All selected messages have been deleted.";
    } else {
        echo "You didn't select any posts.";
    }
}

// Close the database connection
$mysqli->close();
?>
