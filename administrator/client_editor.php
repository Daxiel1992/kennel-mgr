<?php
	session_start();
	require '../includes/dbConfig.php';
	require '../includes/commonFunctions.php';

	// Set empty variables to avoid errors	
	$message = "";
	$missingInputs = "";

	// Initial GET from the Client Viewer. Sets all of the proper variables.
	if(isset($_GET['client_id']) AND !isset($getDone)) {
		$client_id = mysqli_real_escape_string($db, $_GET['client_id']);
		$result = $db->query("SELECT * FROM `clients` WHERE `id` = {$client_id}");
		while($row = $result->fetch_assoc()) {
			$client_fname = $row['first_name'];
			$client_lname = $row['last_name'];
			$client_phone = $row['phone'];
			$client_address = $row['address'];
			$client_address2 = $row['address_2'];
			$client_city = $row['city'];
			$client_state = $row['state'];
			$client_zip = $row['zip'];
		}
		$getDone = 1;
	}

	// When the form is submitted, run our inputs through the checkInputs fuction and update the client if no errors are thrown.
	if(isset($_POST['mode']) AND $_POST['mode'] == "create_client") {
		
		$inputsArray = array (
			array('client_fname', 'First Name', 1),
			array('client_lname', 'Last Name', 1),
			array('client_phone', 'Phone Number', 1),
			array('client_address', 'Address', 0),
			array('client_address2', 'Mailing Address', 0)
		);
		checkInputs($inputsArray);

		// If City, State, and Zip are all empty, none are required. But if one is filled, all are required just to help keep data in order.
		if(empty($_POST['client_city']) AND empty($_POST['client_state']) AND empty($_POST['client_zip'])) {
			$client_city = "";
			$client_state = "";
			$client_zip = "";
		} else {
			$inputsArray = array (
				array('client_city', 'City', 1),
				array('client_state', 'State', 1),
				array('client_zip', 'Zip', 1),
			);
			checkInputs($inputsArray);
		} 

		// If no errors are given, update the client's info
		if(!empty($missingInputs)) {
			$message_color = "red";
			$message = "Client's {$missingInputs} is Required!";
		} else {
			$db->query("UPDATE `clients` SET `first_name` = '$client_fname', `last_name` = '$client_lname', `phone` = '$client_phone', `address` = '$client_address', `address_2` = '$client_address2', `city` = '$client_city', `state` = '$client_state', `zip` = '$client_zip' WHERE `id` = {$client_id}");
			$message = "Client \"" . cleanOutputs($client_fname) . " " .  cleanOutputs($client_lname) . "\" updated!";
			$message_color = "green";
		}
	}

	// Create States Array
	$statesArray = [
		"AL"=>"Alabama",
		"AK"=>"Alaska",
		"AZ"=>"Arizona",
		"AR"=>"Arkansas",
		"CA"=>"California",
		"CO"=>"Colorado",
		"CT"=>"Connecticut",
		"DE"=>"Delaware",
		"DC"=>"District Of Columbia",
		"FL"=>"Florida",
		"GA"=>"Georgia",
		"HI"=>"Hawaii",
		"ID"=>"Idaho",
		"IL"=>"Illinois",
		"IN"=>"Indiana",
		"IA"=>"Iowa",
		"KS"=>"Kansas",
		"KY"=>"Kentucky",
		"LA"=>"Louisiana",
		"ME"=>"Maine",
		"MD"=>"Maryland",
		"MA"=>"Massachusetts",
		"MI"=>"Michigan",
		"MN"=>"Minnesota",
		"MS"=>"Mississippi",
		"MO"=>"Missouri",
		"MT"=>"Montana",
		"NE"=>"Nebraska",
		"NV"=>"Nevada",
		"NH"=>"New Hampshire",
		"NJ"=>"New Jersey",
		"NM"=>"New Mexico",
		"NY"=>"New York",
		"NC"=>"North Carolina",
		"ND"=>"North Dakota",
		"OH"=>"Ohio",
		"OK"=>"Oklahoma",
		"OR"=>"Oregon",
		"PA"=>"Pennsylvania",
		"RI"=>"Rhode Island",
		"SC"=>"South Carolina",
		"SD"=>"South Dakota",
		"TN"=>"Tennessee",
		"TX"=>"Texas",
		"UT"=>"Utah",
		"VT"=>"Vermont",
		"VA"=>"Virginia",
		"WA"=>"Washington",
		"WV"=>"West Virginia",
		"WI"=>"Wisconsin",
		"WY"=>"Wyoming",
	];

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
	<h2 style="float: left; margin-top: 0px;">Edit Client</h2>
	<?php
		if(!empty($message)) {
			echo "<h4 style='color: {$message_color}; float: left; margin-top: 6px; margin-left: 20px;'>{$message}</h4>";
		}
	?>

	<form method="POST" style="display: inline-block; margin-bottom: 0px;">
		<input type="hidden" name="mode" value="create_client">
		<div class="flex-wrapper">
			<div class="input_div">	
				<label for="client_fname">First Name</label>
				<input type="text" id="client_fname" name="client_fname" value="<?php if(isset($client_fname)){ echo cleanOutputs($client_fname);}?>">

				<label for="client_lname">Last Name</label>
				<input type="text" id="client_lname" name="client_lname" value="<?php if(isset($client_lname)){ echo cleanOutputs($client_lname);}?>">

				<label for="client_phone">Phone Number</label>
				<input type="text" id="client_phone" name="client_phone" value="<?php if(isset($client_phone)){ echo cleanOutputs($client_phone);}?>">

			</div>

			<div class="input_div">
				<label for="client_address">Home Address</label>
				<input type="text" id="client_address" name="client_address" value="<?php if(isset($client_address)){ echo cleanOutputs($client_address);}?>">

				<label for="client_address2">Mailing Address</label>
				<input type="text" id="client_address2" name="client_address2" value="<?php if(isset($client_address2)){ echo cleanOutputs($client_address2);}?>">

				<div class="location_inputs" style="margin-left: 0px;">
					<label for="client_city">City</label>
					<input type="text" id="client_city" name="client_city" value="<?php if(isset($client_city)){ echo cleanOutputs($client_city);}?>">
				</div>
				<div class="location_inputs">
					<label for="client_state">State</label>
					<select name="client_state" id="client_state">
						<option value=""></option>
						<?php
							// Loop through our states array and check if one is set already.
							foreach($statesArray as $key=>$value) {
								echo "<option value='{$key}'";
								if(isset($client_state) AND $client_state == $key) { echo "selected"; }
								echo ">{$value}</option>";
							}
						?>
						</select>	
				</div>
				<div class="location_inputs" style="margin-right: 0px;">
					<label for="client_zip">Zip Code</label>
					<input type="text" id="client_zip" name="client_zip" value="<?php if(isset($client_zip)){ echo cleanOutputs($client_zip);}?>">
				</div>
			</div>					
		</div>
		
		<div style="float: right;">
			<input type="submit" class="form_button" value="Save Changes">
		</div>
	</form>	
	
</body>
</html>
