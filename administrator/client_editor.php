<?php
	session_start();
	require '../includes/dbConfig.php';
	//require 'includes/auth.php';
	
	$message = "";

	if(isset($_GET['client_id'])) {
		$client_id = mysqli_real_escape_string($db, $_GET['client_id']);
		$result = $db->query("SELECT * FROM `clients` WHERE `id` = {$client_id}");
		while($row = $result->fetch_assoc()) {
			$client_fname = $row['first_name'];
			$client_lname = $row['last_name'];
			$client_phone = $row['phone'];
		}
		
	}

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
			$db->query("UPDATE `clients` SET `first_name` = '$client_fname', `last_name` = '$client_lname', `phone` = '$client_phone' WHERE `id` = {$client_id}");
			$message = "Client \"{$client_fname} {$client_lname}\" updated!";
			$message_color = "green";
			unset($client_fname, $client_lname, $client_phone);
		}
	}
?>
<html>
<head>
	<title>Edit Client</title>

	<style> 
		.flex-wrapper {
			display: flex;
		}
	
		.input_div {
			width: 50%; 
			float: left; 
			margin: 0 12px;
		}

		.location_inputs {
			width: 32%;
			float: left;
			margin: 0px 1%;
		}

		.form_button {
			float: right;
			padding: 15px 20px;
			margin: 0 12px;
			background-color: #2c7636;
			color: white;
			border: none;
			text-decoration: none;
			cursor: pointer;
			transition: .2s ease-in-out;
		}

		.form_button:hover {
			background-color: #1b4922;
		}
	
		input[type=text] {
			width: 100%;
			padding: 12px 20px;
			margin: 8px 0;
	 		box-sizing: border-box;
		}
		
		select {
			width: 100%;
			padding: 12px 20px;
			margin: 8px 0;
	 		box-sizing: border-box;
		}
	</style>
</head>
<body>

	<?php
		if(!empty($message)) {
			echo "<h3 style='color: {$message_color};'>{$message}</h3>";
		}
	?>

		<h2>Edit Client</h2>
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
			<input type="submit" class="form_button" value="Save Changes">
		</div>
	</form>	
	
</body>
</html>
