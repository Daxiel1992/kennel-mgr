<?php
	$userid = mysqli_real_escape_string($db, $_SESSION['user_id']);
	$login_token = mysqli_real_escape_string($db, $_SESSION['login_token']);
	
	$result = $db->query("SELECT `id`, `name`, `last_active` FROM users WHERE id = '$userid' and login_token = '$login_token' LIMIT 1");
	$count = mysqli_num_rows($result);
	$row = $result->fetch_assoc();

	if($count == 1) {
		updateActivity($userid, $db);
	} else {
		//IF session id and/or token are not correct, kick out
		header('Location: includes/logout.php');
	}

	$last_active = $row['last_active'];
	$last_active = strtotime($last_active . "+2 Hours");
	$last_active = date('Y-m-d H:i:s', $last_active);
	$currentTime = date('Y-m-d H:i:s');

	if($last_active < $currentTime) {
		header('Location: includes/logout.php');
	}

	function updateActivity($userid, $db) {
		$currentTime = date('Y-m-d H:i:s');
		$updateQuery = $db->query("UPDATE `users` SET `last_active` = '$currentTime' WHERE `id` = '$userid'");
	}


?>
<script>
	function logOutPrompt() {
		if(confirm("You will be logged out soon. Click OK to stay logged in! Click Cancel to log out now!")) {
			<?php updateActivity($userid, $db); ?>
		} else {
			location.replace("includes/logout.php");
		}
	}
	//setInterval(logOutPrompt(), 5400000);
</script>
