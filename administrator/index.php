<?php
	session_start();
	require '../includes/dbConfig.php';

	// If we have data, run our login
	if(isset($_POST['mode']) AND $_POST['mode'] == "login") {
		// Sanitize data and query for our user's account
		$username = mysqli_real_escape_string($db, $_POST['username']);
		$password = mysqli_real_escape_string($db, md5($_POST['password']));

		// Check to see if a user account exists and the password is correct
		$result = $db->query("SELECT `id`, `name` FROM users WHERE username = '$username' and password = '$password' LIMIT 1");
		$count = mysqli_num_rows($result);

		if($count == 1) {
			$row = mysqli_fetch_assoc($result);
			
			// Hold our user's data as session variables for easy access
			$_SESSION['user_name'] = $row['name'];
			$_SESSION['user_id'] = $row['id'];
			$_SESSION['valid_login'] = 1;
			$userid = $_SESSION['user_id'];
			
			// Create date and time for login time information
			$currentDate = new DateTime();
			$curDate = $currentDate->format('Y-m-d H:i:s');
	
			// Create Login Token
			$login_token = md5(rand(10000, 100000));
			$_SESSION['login_token'] = $login_token;
	
			$update_query = $db->query("UPDATE `users` SET `login_token` = '$login_token', `last_login` = '$curDate' WHERE id = '$userid'");

			header('Location: home.php');
		} else {
			$error = "Invalid Login!!";
		}
	}
?>
<html>
	<head>
		<title>Login Tester</title>
	</head>
	<body>
		<?php
			if(isset($error)) {
				echo "<p style='color: red; font-weight: bold;'>$error</p>";
			}
		?>
		<form action="index.php" method="POST">
			<input type="hidden" name="mode" value="login">
			Username: <input type="text" name="username"><br>
			Password: <input type="password" name="password"><br>
			<input type="submit">
		</form>
	</body>
</html>
