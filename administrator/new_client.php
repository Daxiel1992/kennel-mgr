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
		
		if(empty($_POST['client_address'])) {
			$message = $message . "Client's Home Address is Required! <br>";
		} else {
			$client_address = mysqli_real_escape_string($db, $_POST['client_address']);
		}
		
		if(empty($_POST['client_address2'])) {
			$client_address2 = NULL;
		} else {
			$client_address2 = mysqli_real_escape_string($db, $_POST['client_address2']);
		}
		
		if(empty($_POST['client_city'])) {
			$message = $message . "Client's City is Required! <br>";
		} else {
			$client_city = mysqli_real_escape_string($db, $_POST['client_city']);
		}
	
		if(empty($_POST['client_state'])) {
			$message = $message . "Client's State is Required! <br>";
		} else {
			$client_state = mysqli_real_escape_string($db, $_POST['client_state']);
		}

		if(empty($_POST['client_zip'])) {
			$message = $message . "Client's Zip Code is Required! <br>";
		} else {
			$client_zip = mysqli_real_escape_string($db, $_POST['client_zip']);
		}
	
		if(!empty($message)) {
			$message_color = "red";
		} else {
			$db->query("INSERT INTO `clients` (`first_name`, `last_name`, `phone`, `address`, `address_2`, `city`, `state`, `zip`) VALUES ('$client_fname', '$client_lname', '$client_phone', '$client_address', '$client_address2', '$client_city', '$client_state', '$client_zip')");
			$message = "Client \"{$client_fname} {$client_lname}\" created!";
			$message_color = "green";
			unset($client_fname, $client_lname, $client_phone, $client_address, $client_address2, $client_city, $client_state, $client_zip);
		}
	}
?>
<html>
<head>
	<title>Create New Client</title>

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

	<h2>Create New Client</h2>
	<form method="POST">
		<input type="hidden" name="mode" value="create_client">
		<div class="flex-wrapper">
			<div class="input_div">	
				<label for="client_fname">First Name</label>
				<input type="text" id="client_fname" name="client_fname" value="<?php if(isset($client_fname)){ echo $client_fname;}?>">

				<label for="client_lname">Last Name</label>
				<input type="text" id="client_lname" name="client_lname" value="<?php if(isset($client_lname)){ echo $client_lname;}?>">

				<label for="client_phone">Phone Number</label>
				<input type="text" id="client_phone" name="client_phone" value="<?php if(isset($client_phone)){ echo $client_phone;}?>">
			</div>

			<div class="input_div">
				<label for="client_address">Home Address</label>
				<input type="text" id="client_address" name="client_address" value="<?php if(isset($client_address)){ echo $client_address;}?>">

				<label for="client_address2">Mailing Address</label>
				<input type="text" id="client_address2" name="client_address2" value="<?php if(isset($client_address2)){ echo $client_address2;}?>">

				<div class="location_inputs" style="margin-left: 0px;">
					<label for="client_city">City</label>
					<input type="text" id="client_city" name="client_city" value="<?php if(isset($client_city)){ echo $client_city;}?>">
				</div>
				<div class="location_inputs">
					<label for="client_state">State</label>
					<select name="client_state" id="client_state" value="<?php if(isset($client_state)){ echo $client_state;}?>">
						<option value="AL">Alabama</option>
						<option value="AK">Alaska</option>
						<option value="AZ">Arizona</option>
						<option value="AR">Arkansas</option>
						<option value="CA">California</option>
						<option value="CO">Colorado</option>
						<option value="CT">Connecticut</option>
						<option value="DE">Delaware</option>
						<option value="DC">District Of Columbia</option>
						<option value="FL">Florida</option>
						<option value="GA">Georgia</option>
						<option value="HI">Hawaii</option>
						<option value="ID" selected>Idaho</option>
						<option value="IL">Illinois</option>
						<option value="IN">Indiana</option>
						<option value="IA">Iowa</option>
						<option value="KS">Kansas</option>
						<option value="KY">Kentucky</option>
						<option value="LA">Louisiana</option>
						<option value="ME">Maine</option>
						<option value="MD">Maryland</option>
						<option value="MA">Massachusetts</option>
						<option value="MI">Michigan</option>
						<option value="MN">Minnesota</option>
						<option value="MS">Mississippi</option>
						<option value="MO">Missouri</option>
						<option value="MT">Montana</option>
						<option value="NE">Nebraska</option>
						<option value="NV">Nevada</option>
						<option value="NH">New Hampshire</option>
						<option value="NJ">New Jersey</option>
						<option value="NM">New Mexico</option>
						<option value="NY">New York</option>
						<option value="NC">North Carolina</option>
						<option value="ND">North Dakota</option>
						<option value="OH">Ohio</option>
						<option value="OK">Oklahoma</option>
						<option value="OR">Oregon</option>
						<option value="PA">Pennsylvania</option>
						<option value="RI">Rhode Island</option>
						<option value="SC">South Carolina</option>
						<option value="SD">South Dakota</option>
						<option value="TN">Tennessee</option>
						<option value="TX">Texas</option>
						<option value="UT">Utah</option>
						<option value="VT">Vermont</option>
						<option value="VA">Virginia</option>
						<option value="WA">Washington</option>
						<option value="WV">West Virginia</option>
						<option value="WI">Wisconsin</option>
						<option value="WY">Wyoming</option>
					</select>	
				</div>
				<div class="location_inputs" style="margin-right: 0px;">
					<label for="client_zip">Zip Code</label>
					<input type="text" id="client_zip" name="client_zip" value="<?php if(isset($client_zip)){ echo $client_zip;}?>">
				</div>
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
