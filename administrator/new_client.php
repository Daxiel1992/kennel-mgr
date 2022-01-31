<?php
	session_start();
	require '../includes/dbConfig.php';
	require 'includes/auth.php';
	
	$message = "";
	
	if(isset($_POST['mode']) AND $_POST['mode'] == "create_client") {
		if(empty($_POST['client_fname'])) {
			$message = $message . "Client's First Name is Required! <br>";
		} else {
			$client_fname = mysqli_real_escape_string($db, $_POST['client_fname']);
		}
		
		if(empty($_POST['client_lname'])) {
			$message = $message . "Client's Last Name is Required! <br>";
		} else {
			$client_lname = mysqli_real_escape_string($db, $_POST['client_lname']);
		}
		
		if(empty($_POST['client_phone'])) {
			$message = $message . "Client's Phone Number is Required! <br>";
		} else {
			$client_phone = mysqli_real_escape_string($db, $_POST['client_phone']);
		}
		
		if(!empty($message)) {
			$message_color = "red";
		} else {
			$db->query("INSERT INTO `clients` (`first_name`, `last_name`, `phone`, `address`, `address_2`, `city`, `state`, `zip`) VALUES ('$client_fname', '$client_lname', '$client_phone', '', '', '', '', '')");
			$message = "Client \"{$client_fname} {$client_lname}\" created!";
			$message_color = "green";
			unset($client_fname, $client_lname, $client_phone);
		}
	}
?>
<html>
<head>
	<title>Create New Client</title>
	<link rel="stylesheet" type="text/css" href="popupform.css"/>
</head>
<body>

	<?php
		if(!empty($message)) {
			echo "<h3 style='color: {$message_color};'>{$message}</h3>";
		}
	?>

	<h2>Create New Client</h2>
	<form method="POST">
		<input type="hidden" name="mode" value="create_client">
		<div class="flex-wrapper">
			<div class="input_div">	
				<label for="client_fname">First Name</label>
				<input type="text" id="client_fname" name="client_fname" value="<?php if(isset($client_fname)){ echo $client_fname;}?>">

				<label for="client_lname">Last Name</label>
				<input type="text" id="client_lname" name="client_lname" value="<?php if(isset($client_lname)){ echo $client_lname;}?>">
				</div>

			<div class="input_div">
				<label for="client_phone">Phone Number</label>
				<input type="text" id="client_phone" name="client_phone" value="<?php if(isset($client_phone)){ echo $client_phone;}?>">
			</div>					
		</div>
		<br>
		<br>

		<div style="float: right;">
			<input type="submit" class="form_button" value="Create Client">
		</div>
	</form>	
	
</body>
</html>
