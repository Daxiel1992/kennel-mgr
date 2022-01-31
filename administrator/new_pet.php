<?php
	session_start();
	require '../includes/dbConfig.php';
	require 'includes/auth.php';
	
	$message = "";
	
	if(isset($_POST['mode']) AND $_POST['mode'] == "create_client") {
		if(empty($_SESSION['client_id'])) {
			$message = $message . "ERROR! Client not found. Try refreshing the page. <br>";
		} else {
			$client_id = mysqli_real_escape_string($db, $_SESSION['client_id']);
		}
		
		if(empty($_POST['pet_name'])) {
			$message = $message . "Pet's Name is Required! <br>";
		} else {
			$pet_name = mysqli_real_escape_string($db, $_POST['pet_name']);
		}
		
		if(empty($_POST['pet_breed'])) {
			$message = $message . "Pet's Breed is Required! <br>";
		} else {
			$pet_breed = mysqli_real_escape_string($db, $_POST['pet_breed']);
		}
		
		if(!empty($message)) {
			$message_color = "red";
		} else {
			$db->query("INSERT INTO `pets` (`client_id`, `name`, `breed`) VALUES ('$client_id', '$pet_name', '$pet_breed')");
			$message = "Pet \"{$pet_name}\" created!";
			$message_color = "green";
			unset($client_id, $pet_name, $pet_breed);
		}
	}
?>
<html>
<head>
	<title>Create New Pet</title>
	<link rel="stylesheet" type="text/css" href="popupform.css"/>
</head>
<body>

	<?php
		if(!empty($message)) {
			echo "<h3 style='color: {$message_color};'>{$message}</h3>";
		}
	?>

	<h2>Create New Pet</h2>
	<form method="POST">
		<input type="hidden" name="mode" value="create_client">
		<div class="flex-wrapper">
			<div class="input_div">	
				<label for="pet_name">Pet's Name</label>
				<input type="text" id="pet_name" name="pet_name" value="<?php if(isset($pet_name)){ echo $pet_name;}?>">
			</div>

			<div class="input_div">
				<label for="pet_breed">Pet's Breed</label>
				<input type="text" id="pet_breed" name="pet_breed" value="<?php if(isset($pet_breed)){ echo $pet_breed;}?>">
			</div>					
		</div>
		<br>
		<br>

		<div style="float: right;">
			<input type="submit" class="form_button" value="Create Pet">
		</div>
	</form>	
	
</body>
</html>
