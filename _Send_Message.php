<?php
// Ensure safe and correct page URL handling
$page_url = explode(".", $_SERVER['REQUEST_URI']);
$_SERVER['REQUEST_URI'] = $page_url[0] . ".php";

if ($_SERVER['REQUEST_URI'] == "/_Send_Message.php") {
    exit();
}

// Establish MySQLi connection
$mysqli = new mysqli("localhost", "username", "password", "database_name");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

if (isset($_POST['Send'])) {

    // Sanitize and trim inputs
    $_POST['sendto'] = str_replace(' ', '', $_POST['sendto']);
    $m_check = str_replace(' ', '', $_POST['message']);

    // Validation: ensure no empty fields
    if (empty($m_check) || empty($_POST['sendto'])) {
        echo "You left one or more fields open.";
    } else {

        // Allow mass messages to up to 5 people
        $multi_messages = explode("-", $_POST['sendto']);
        $multi_messages = array_unique($multi_messages);

        if (count($multi_messages) > 5) {
            echo "5 is the maximum amount of people you are allowed to send to at once.";
        } else {
            // Loop through each recipient to send messages
            foreach ($multi_messages as $recipient) {
                
                // Check if user exists
                $stmt = $mysqli->prepare("SELECT name FROM users WHERE name = ?");
                $stmt->bind_param("s", $recipient);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                
                // Prevent sending message to self
                if ($row['name'] == $name) {
                    echo "<br />It's not allowed to send a message to yourself.";
                } else {
                    if (!empty($row['name'])) {
                        // Insert the message into the database
                        $stmt = $mysqli->prepare("INSERT INTO pm (sendto, message, sendby) VALUES (?, ?, ?)");
                        $stmt->bind_param("sss", $row['name'], $_POST['message'], $name);
                        $res = $stmt->execute();

                        if ($res) {
                            // Confirm message sent
                            $send_to = "<a href=\"View_Profile.php?name=" . $row['name'] . "\" onFocus=\"if(this.blur)this.blur()\">" . $row['name'] . "</a>,";
                            echo "<br />Your message to " . $send_to . " has been sent.";

                            // Update user's message status
                            $stmt = $mysqli->prepare("UPDATE users SET newmail = '0' WHERE name = ?");
                            $stmt->bind_param("s", $row['name']);
                            $stmt->execute();

                            $stmt = $mysqli->prepare("UPDATE users SET messages = messages + 1 WHERE id = ?");
                            $stmt->bind_param("i", $_SESSION['user_id']);
                            $stmt->execute();

                            // Helpdesk-specific actions
                            if ($_GET['action'] == "helpdesk" && (in_array($name, $admin_array) || in_array($name, $manager_array) || in_array($name, $hdo_array))) {
                                $stmt = $mysqli->prepare("UPDATE login SET help_desk = '' WHERE name = ?");
                                $stmt->bind_param("s", $_GET['name']);
                                $stmt->execute();
                            }
                        } else {
                            // Error if the message couldn't be sent
                            echo "Error! Your message could not be sent.";
                        }
                    } else {
                        // If the recipient doesn't exist
                        echo "<br />" . $recipient . " doesn't play this game.";
                    }
                }
            }
        }
    }
}

// Close the database connection
$mysqli->close();
?>
