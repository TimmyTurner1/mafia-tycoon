<?php
// Server settings that we are going to use later
$page_url = explode(".", $_SERVER['REQUEST_URI']);
$_SERVER['REQUEST_URI'] = $page_url[0] . ".php";

// Exit if accessing the forum page directly
if ($_SERVER['REQUEST_URI'] == "/_Forum.php") {
    exit();
}

// Default 'type' value if not provided, validate if numeric
if (empty($_GET['type'])) {
    $_GET['type'] = 1;
} else {
    if (!is_numeric($_GET['type'])) {
        $_GET['type'] = 1;
    }
}

// Pagination setup
$amount = 20;

// Initialize database connection (Using MySQLi for better security)
$mysqli = new mysqli("localhost", "username", "password", "database_name");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$nsql = "SELECT * FROM topics WHERE topicstate = '0' and type=?";
$stmt = $mysqli->prepare($nsql);
$stmt->bind_param("i", $_GET['type']);
$stmt->execute();
$nres = $stmt->get_result();
$totaltopics = $nres->num_rows;

// Pagination logic
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
$min = $amount * ($page - 1);
$max = $amount;

// Set the selected type for UI
if ($_GET['type'] == 1) {
    $select_1 = "selected='selected'";
}

// Auto clean forum script if topic count exceeds threshold
if ($totaltopics >= 500) {
    $mysqli->query("DELETE FROM topics WHERE topicstate=0");
    $mysqli->query("DELETE FROM replys WHERE topictype=0");
}

if (in_array($name, $admin_array)) {
    // Clean forum
    if ($_POST['action'] == "Clean Forum") {
        $mysqli->query("DELETE FROM topics WHERE topicstate=0");
        $mysqli->query("DELETE FROM replys WHERE topictype=0");
    }

    // Delete selected topics
    if ($_POST['action'] == "Delete") {
        $id = $_POST['id'];
        if (!empty($id)) {
            $delete = implode(",", $id);
            $delete = explode(",", $delete);
            foreach ($delete as $topic_id) {
                $stmt = $mysqli->prepare("DELETE FROM topics WHERE id=?");
                $stmt->bind_param("i", $topic_id);
                $stmt->execute();

                $stmt = $mysqli->prepare("DELETE FROM replys WHERE tid=?");
                $stmt->bind_param("i", $topic_id);
                $stmt->execute();
            }
        }
    }

    // Sticky topic
    if ($_POST['action'] == "Sticky") {
        $id = $_POST['id'];
        if (!empty($id)) {
            $delete = implode(",", $id);
            $delete = explode(",", $delete);
            foreach ($delete as $topic_id) {
                $stmt = $mysqli->prepare("UPDATE topics SET topicstate='2' WHERE id=?");
                $stmt->bind_param("i", $topic_id);
                $stmt->execute();

                $stmt = $mysqli->prepare("UPDATE replys SET topictype='2' WHERE tid=?");
                $stmt->bind_param("i", $topic_id);
                $stmt->execute();
            }
        }
    }

    // Lock topic
    if ($_POST['action'] == "Lock") {
        $id = $_POST['id'];
        if (!empty($id)) {
            $delete = implode(",", $id);
            $delete = explode(",", $delete);
            foreach ($delete as $topic_id) {
                $stmt = $mysqli->prepare("UPDATE topics SET locked='2' WHERE id=?");
                $stmt->bind_param("i", $topic_id);
                $stmt->execute();
            }
        }
    }

    // Unlock topic
    if ($_POST['action'] == "Unlock") {
        $id = $_POST['id'];
        if (!empty($id)) {
            $delete = implode(",", $id);
            $delete = explode(",", $delete);
            foreach ($delete as $topic_id) {
                $stmt = $mysqli->prepare("UPDATE topics SET locked='1' WHERE id=?");
                $stmt->bind_param("i", $topic_id);
                $stmt->execute();
            }
        }
    }

    // Mark as Important
    if ($_POST['action'] == "Important") {
        $id = $_POST['id'];
        if (!empty($id)) {
            $delete = implode(",", $id);
            $delete = explode(",", $delete);
            foreach ($delete as $topic_id) {
                $stmt = $mysqli->prepare("UPDATE topics SET topicstate='1' WHERE id=?");
                $stmt->bind_param("i", $topic_id);
                $stmt->execute();

                $stmt = $mysqli->prepare("UPDATE replys SET topictype='1' WHERE tid=?");
                $stmt->bind_param("i", $topic_id);
                $stmt->execute();
            }
        }
    }

    // Remove importance/sticky
    if ($_POST['action'] == "Remove") {
        $stmt = $mysqli->prepare("UPDATE topics SET topicstate='0' WHERE id=?");
        $stmt->bind_param("i", $_POST['topic_id']);
        $stmt->execute();

        $stmt = $mysqli->prepare("UPDATE replys SET topictype='0' WHERE tid=?");
        $stmt->bind_param("i", $_POST['topic_id']);
        $stmt->execute();
    }

    // Move topic
    if ($_POST['action'] == "Move") {
        $id = $_POST['id'];
        if (!empty($id)) {
            $delete = implode(",", $id);
            $delete = explode(",", $delete);
            foreach ($delete as $topic_id) {
                $stmt = $mysqli->prepare("UPDATE topics SET type=? WHERE id=?");
                $stmt->bind_param("ii", $_POST['type_move'], $topic_id);
                $stmt->execute();
            }
        }
    }
}

// Add new topic
if (isset($_POST['Submit'])) {
    $topicTitle = trim($_POST['topictitle']);
    $message = trim($_POST['message']);

    if (empty($topicTitle) || empty($message)) {
        echo "All fields need to be filled.";
    } elseif (strlen($topicTitle) > 50) {
        echo "Your subject may not be longer than 50 characters.";
    } else {
        // Insert topic using prepared statements
        $date = gmdate("m-d-y-H:i:s");
        $stmt = $mysqli->prepare("INSERT INTO topics (title, message, date, name, type) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $topicTitle, $message, $date, $name, 1);
        $stmt->execute();
    }
}
?>
