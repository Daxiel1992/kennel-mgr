<?php
	session_start();
	include_once '../includes/dbConfig.php';

	if($_POST['username']) {
		$name = mysqli_real_escape_string($db, $_POST['name']);
		$username = mysqli_real_escape_string($db, $_POST['username']);
		$password = mysqli_real_escape_string($db, md5($_POST['password']));
	
		$nametest = $db->query("SELECT id FROM users WHERE username = '$username'");
		$testcount = mysqli_num_rows($nametest);

		if ($testcount == 1) {
			echo "User already exists. Try Another.";
		} else {
			$db->query("INSERT INTO `users` (`name`, `username`, `password`) VALUES ('$name', '$username', '$password')");
			echo "Success!";
			echo "<a href='login.html'>Login!</a>";
		}	
	}
?>
<html>
<head>
<title>Register Tester</title>
</head>
<body>
<?php
//require('./includes/header.php');
?>
<form action="" method="post">
Name: <input type="text" name="name"><br>
Username: <input type="text" name="username"><br>
Password: <input type="password" name="password"><br>
<input type="submit">
</form>
</body>
</html>
