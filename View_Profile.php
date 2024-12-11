<?php 
require("Left.php");
require("connect.php"); // Ensure the connection to the database is included

// Sanitize the user input for security
$userName = isset($_GET['name']) ? $_GET['name'] : '';
$userName = htmlspecialchars($userName);

// Fetch the user profile details
$stmt = $conn->prepare("SELECT * FROM users WHERE name = ?");
$stmt->bind_param("s", $userName);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_object();

// Check if the user exists
if (!$row) {
    die('User not found');
}

$profile_id = htmlspecialchars($row->id);
$profile_userip = htmlspecialchars($row->userip);
$profile_name = htmlspecialchars($row->name);
$profile_money = htmlspecialchars($row->money);
$profile_rank = htmlspecialchars($row->rank);
$profile_gang = htmlspecialchars($row->gang);
$profile_exp = htmlspecialchars($row->exp);
$profile_profile = nl2br(htmlspecialchars($row->profile));

// Define arrays for wealth and rank
$wealth_array = ["Poor", "Middle Class", "Rich", "Wealthy", "Millionaire", "Billionaire"];
$rank_array = ["Newbie", "Member", "Veteran", "Expert", "Master"];

// Determine wealth
$profile_wealth = $wealth_array[0];
if ($profile_money >= 10000) { $profile_wealth = $wealth_array[1]; }
if ($profile_money >= 25000) { $profile_wealth = $wealth_array[2]; }
if ($profile_money >= 50000) { $profile_wealth = $wealth_array[3]; }
if ($profile_money >= 100000) { $profile_wealth = $wealth_array[4]; }
if ($profile_money >= 1000000) { $profile_wealth = $wealth_array[5]; }

// Determine rank
$profile_rank = isset($rank_array[$profile_rank]) ? $rank_array[$profile_rank] : 'Unknown';

// Check the user’s online status
$online_status = "Offline";
$stmt = $conn->prepare("SELECT lastactive FROM users WHERE name = ? AND DATE_SUB(NOW(), INTERVAL 5 MINUTE) <= lastactive");
$stmt->bind_param("s", $userName);
$stmt->execute();
$lastActiveResult = $stmt->get_result();
if ($lastActiveResult->num_rows > 0) {
    $online_status = "Online";
} else {
    $stmt = $conn->prepare("SELECT lastactive FROM users WHERE name = ? AND DATE_SUB(NOW(), INTERVAL 1 MINUTE) <= lastactive");
    $stmt->bind_param("s", $userName);
    $stmt->execute();
    $idleResult = $stmt->get_result();
    if ($idleResult->num_rows > 0) {
        $online_status = "Away";
    }
}

// Display the profile
?>
<html>
<head></head>
<body>
<table width="90%" border="1">
  <tr>
    <td width="32%">Username: <a href="Send_Message.php?name=<?= $profile_name ?>" onFocus="if(this.blur)this.blur()"><?= $profile_name ?></a> - <?= $profile_account ?? 'User' ?></td>
    <td width="68%" rowspan="5">A picture if you choose to make avatars</td>
  </tr>
  <tr>
    <td>Rank: <?= $profile_rank ?></td>
  </tr>
  <tr>
    <td>Wealth: <?= $profile_wealth ?></td>
  </tr>
  <tr>
    <td>Crew: Coming Soon in later Parts</td>
  </tr>
  <tr>
    <td>Status: <?= $online_status ?></td>
  </tr>
  <tr>
    <td height="82" colspan="2">
      <?= $profile_profile ?>
    </td>
  </tr>
</table>
</body>
</html>
<?php require("Right.php"); ?>
