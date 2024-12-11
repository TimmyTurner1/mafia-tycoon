<?php
// Server settings
$page_url = explode(".", $_SERVER['REQUEST_URI']);
$_SERVER['REQUEST_URI'] = $page_url[0] . ".php";

if ($_SERVER['REQUEST_URI'] == "/_Forum_View.php") {
    exit();
}

// Up topic
if ($_POST['Edit'] == "Edit") {
    $date = gmdate("m-d-y-H:i:s");

    // Establish MySQLi connection
    $mysqli = new mysqli("localhost", "username", "password", "database_name");
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    $stmt = $mysqli->prepare("SELECT name FROM topics WHERE id = ?");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $stmt->bind_result($topic_name);
    $stmt->fetch();
    $stmt->close();

    // Check if the user is allowed to edit the post
    if ($topic_name != $name && !in_array($name, $admin_array) && !in_array($name, $manager_array)) {
        echo "You are not allowed to edit this post.";
    } else {
        $m_check = str_replace(' ', '', $_POST['message']);
        $t_check = str_replace(' ', '', $_POST['topictitle']);

        if (empty($m_check) || empty($t_check)) {
            echo "All fields need to be filled.";
        } else {
            if (strlen($_POST['topictitle']) > 50) {
                echo "Your subject may not be longer than 50 characters.";
            } else {
                // Update the topic message and title
                $stmt = $mysqli->prepare("UPDATE topics SET message = ?, title = ? WHERE id = ?");
                $stmt->bind_param("ssi", $_POST['message'], $_POST['topictitle'], $_GET['id']);
                $stmt->execute();
                $stmt->close();

                echo "Your topic has been updated.";
            }
        }
    }
}

// Page script
$amount = 10;

$mysqli = new mysqli("localhost", "username", "password", "database_name");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$stmt = $mysqli->prepare("SELECT * FROM replys WHERE tid = ? ORDER BY date DESC");
$stmt->bind_param("i", $_GET['id']);
$stmt->execute();
$res = $stmt->get_result();
$totaltopics = $res->num_rows;

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
$min = $amount * ($page - 1);
$max = $amount;

$stmt = $mysqli->prepare("SELECT * FROM topics WHERE id = ?");
$stmt->bind_param("i", $_GET['id']);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();

$date = gmdate("m-d-y-H:i:s");

if (isset($_POST['Reply'])) {
    $m_check = str_replace(' ', '', $_POST['message']);
    if (empty($m_check)) {
        echo "You didn't type anything in the message box.";
    } else {
        if (!in_array($name, $admin_array) && $row['locked'] == "2") {
            echo "You can't post in a locked topic.";
        } else {
            $stmt = $mysqli->prepare("INSERT INTO replys (name, message, date, tid, topictype) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $name, $_POST['message'], $date, $_GET['id'], $_GET['id']);
            $stmt->execute();
            $stmt->close();

            // Update topic date
            $stmt = $mysqli->prepare("UPDATE topics SET date = ? WHERE id = ?");
            $stmt->bind_param("si", $date, $_GET['id']);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Selecting replys
$stmt = $mysqli->prepare("SELECT id, title, message, date, name FROM topics WHERE id = ?");
$stmt->bind_param("i", $_GET['id']);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();

// Delete reply topic
if ($_GET['action'] == "Rreply" && in_array($name, $admin_array)) {
    $stmt = $mysqli->prepare("DELETE FROM replys WHERE id = ?");
    $stmt->bind_param("i", $_GET['rid']);
    $stmt->execute();
    $stmt->close();

    echo "Reply Deleted.";
}

// Lock topic script
if ($_GET['action'] == "lock" && $row['name'] == $name) {
    $stmt = $mysqli->prepare("UPDATE topics SET locked = '2' WHERE id = ?");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $stmt->close();

    echo "The topic has been locked.";
}

$mysqli->close();
?>
