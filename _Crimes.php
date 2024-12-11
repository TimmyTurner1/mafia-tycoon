<?php
if (isset($_POST['Commit'])) {
    $radiobutton = $_POST['radiobutton'];

    if ($radiobutton == "1") {
        // Random crime chance and values
        $crime_chance = rand(1, 10);
        $crimetime1 = 10;
        $Crime_exp = rand(1, 10);
        $Crime_money = rand(100, 500);
        $timewait1 = time() + $crimetime1;

        // Prepared statement to get crime times
        $stmt = $conn->prepare("SELECT * FROM crimetimes WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result2 = $stmt->get_result();

        while ($rows2 = $result2->fetch_assoc()) {
            $timeleft1 = $rows2['crime1'];
            $available1 = $rows2['crime1a'];

            $last1 = $timeleft1 - time();

            if ($available1 == 1) {
                echo "You need to wait before you can do this crime";
            } elseif ($crime_chance == 2) {
                echo "You fail HAHAHAHA";
                // Prepare statement for updating the crime time
                $stmt_update = $conn->prepare("UPDATE crimetimes SET crime1a = '1', crime1 = ? WHERE name = ?");
                $stmt_update->bind_param("is", $timewait1, $name);
                $stmt_update->execute();
            } else {
                // Update the crimetimes and users table
                $stmt_update = $conn->prepare("UPDATE crimetimes SET crime1a = '1', crime1 = ? WHERE name = ?");
                $stmt_update->bind_param("is", $timewait1, $name);
                $stmt_update->execute();

                $stmt_user = $conn->prepare("UPDATE users SET exp = exp + ?, money = money + ? WHERE name = ?");
                $stmt_user->bind_param("iis", $Crime_exp, $Crime_money, $name);
                $stmt_user->execute();

                echo "You stole from a child and received $$Crime_money";
            }
        }
    }

    // Check all crime times and reset them if necessary
    $stmt = $conn->prepare("SELECT * FROM crimetimes WHERE name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result2 = $stmt->get_result();

    while ($rows2 = $result2->fetch_assoc()) {
        $timeleft1 = $rows2['crime1'];
        $timeleft2 = $rows2['crime2'];
        $timeleft3 = $rows2['crime3'];
        $timeleft4 = $rows2['crime4'];
        $timeleft5 = $rows2['crime5'];

        $last1 = $timeleft1 - time();
        $last2 = $timeleft2 - time();
        $last3 = $timeleft3 - time();
        $last4 = $timeleft4 - time();
        $last5 = $timeleft5 - time();

        // Reset crime times if expired
        if ($last1 <= 0) {
            $stmt_update = $conn->prepare("UPDATE crimetimes SET crime1a = '0', crime1 = '' WHERE name = ?");
            $stmt_update->bind_param("s", $name);
            $stmt_update->execute();
        }
        if ($last2 <= 0) {
            $stmt_update = $conn->prepare("UPDATE crimetimes SET crime2a = '0', crime2 = '' WHERE name = ?");
            $stmt_update->bind_param("s", $name);
            $stmt_update->execute();
        }
        if ($last3 <= 0) {
            $stmt_update = $conn->prepare("UPDATE crimetimes SET crime3a = '0', crime3 = '' WHERE name = ?");
            $stmt_update->bind_param("s", $name);
            $stmt_update->execute();
        }
        if ($last4 <= 0) {
            $stmt_update = $conn->prepare("UPDATE crimetimes SET crime4a = '0', crime4 = '' WHERE name = ?");
            $stmt_update->bind_param("s", $name);
            $stmt_update->execute();
        }
        if ($last5 <= 0) {
            $stmt_update = $conn->prepare("UPDATE crimetimes SET crime5a = '0', crime5 = '' WHERE name = ?");
            $stmt_update->bind_param("s", $name);
            $stmt_update->execute();
        }
    }
}
?>
