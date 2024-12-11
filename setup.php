<?php
include('connect.php');

// Check if form is submitted
if (isset($_POST['Admin'])) {
    // Check if admin name is provided
    if (!empty($_POST['admin_name'])) {
        $admin_name = $_POST['admin_name'];

        // Validate the admin name to only allow letters and numbers
        if (preg_match('/^[A-Za-z0-9]+$/', $admin_name)) {
            // Prepare SQL statement to check if user exists
            $stmt = $mysqli->prepare("SELECT * FROM users WHERE name = ?");
            $stmt->bind_param('s', $admin_name);
            $stmt->execute();
            $result = $stmt->get_result();

            // Check if the user exists
            if ($result->num_rows == 1) {
                // Fetch user data
                $row = $result->fetch_object();
                $name = htmlspecialchars($row->name);

                // Insert the first admin into sitestats table
                $stmt2 = $mysqli->prepare("INSERT INTO sitestats (id, admins) VALUES (1, ?)");
                $stmt2->bind_param('s', $name);
                $stmt2->execute();

                // Output success message
                echo "<div style='color: green;'>$name has been set as the first admin successfully.</div>";
            } else {
                echo "<div style='color: red;'>Please create your admin account in the game first.</div>";
            }
        } else {
            echo "<div style='color: red;'>Invalid Name: Only letters (A-Z, a-z) and numbers (0-9) are allowed.</div>";
        }
    } else {
        echo "<div style='color: red;'>You didn't enter a name to be set as admin.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="ISO-8859-1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create First Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            width: 100%;
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        input[type="text"], input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .warning {
            color: #ff6f6f;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Create First Admin</h1>
        <form method="post" action="">
            <!-- Display messages from PHP code -->
            <?php
                if (isset($_POST['Admin'])) {
                    // Messages are already echoed in PHP based on conditions
                }
            ?>
            <label for="admin_name">Admin Name:</label>
            <input name="admin_name" type="text" id="admin_name" required />

            <input name="Admin" type="submit" id="Admin" value="Create" />

            <div style="text-align: center;">
                <h2 class="warning">Delete this file after setup</h2>
            </div>
        </form>
    </div>

</body>
</html>
