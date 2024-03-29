<?php
	session_start();
	require '../includes/dbConfig.php';
	require 'includes/commonFunctions.php';

	// Set empty variables to avoid errors	
	$message = "";
	$missingInputs = "";
	$longInputs = "";
	$phoneArray = array();
	$phoneIDArray = array();

	// Initial GET from the Client Viewer. Sets all of the proper variables.
	if(isset($_SESSION['editing_client_id'])) {
		$clientInfo = $db->query("SELECT * FROM `clients` WHERE `id` = '{$_SESSION['editing_client_id']}'");
		$phoneInfo = $db->query("SELECT `id`, `phone` FROM `phone_numbers` WHERE `client_id` = '{$_SESSION['editing_client_id']}'");
					
		while($client = $clientInfo->fetch_assoc()) {
			$client_fname = $client['first_name'];
			$client_lname = $client['last_name'];
			$client_address = $client['address'];
			$client_address2 = $client['address_2'];
			$client_city = $client['city'];
			$client_state = $client['state'];
			$client_zip = $client['zip'];
		}
	
		while($phone = $phoneInfo->fetch_assoc()) {
			$phoneArray[] = $phone['phone'];
			$phoneIDArray[] = $phone['id'];
		}
	}

	// When the form is submitted, run our inputs through the checkInputs fuction and update the client if no errors are thrown.
	if(isset($_POST['mode']) AND $_POST['mode'] == "create_client") {		
		$inputsArray = array (
			array('client_fname', 'First Name', 1, 255),
			array('client_lname', 'Last Name', 1, 255),
			array('client_address', 'Address', 0, 255),
			array('client_address2', 'Mailing Address', 0, 255)
		);
		checkInputs($inputsArray);

		// If City, State, and Zip are all empty, none are required. But if one is filled, all are required just to help keep data in order.
		if(empty($_POST['client_city']) AND empty($_POST['client_state']) AND empty($_POST['client_zip'])) {
			$client_city = "";
			$client_state = "";
			$client_zip = "";
		} else {
			$inputsArray = array (
				array('client_city', 'City', 1, 255),
				array('client_state', 'State', 1, 2),
				array('client_zip', 'Zip', 1, 255),
			);
			checkInputs($inputsArray);
		}

		// Phone Number checks
		// If at least one phone number is given
		if(array_filter($_POST['client_phone'])) {
			$phonePostArray = array_values($_POST['client_phone']);
			foreach($phonePostArray as $key => $phone) {
				if(strlen($phone) > 20) {
					if($longInputs == '') {
						$longInputs = "Phone Number #" . intval($key) + 1;
					} else {
						$longInputs = $longInputs . ", Phone Number #" . intval($key) + 1;
					}
				}
			}
		} else {
			if($missingInputs == '') {
				$missingInputs = "Phone Number";
			} else {
				$missingInputs = $missingInputs . ", Phone Number";
			}
		}

		// If no errors are given, update the client's info
		if(!empty($missingInputs)) {
			$message_color = "red";
			$message = "Client's {$missingInputs} is Required!";
		} else {
			if(!empty($longInputs)) {
				$message_color = "red";
				$message = "{$longInputs} is too long of a value!";
				$phoneArray = $phonePostArray;
			} else {
				if(isset($_SESSION['editing_client_id'])) {
					$db->query("UPDATE `clients` SET `first_name` = '$client_fname', `last_name` = '$client_lname', `address` = '$client_address', `address_2` = '$client_address2', `city` = '$client_city', `state` = '$client_state', `zip` = '$client_zip' WHERE `id` = '{$_SESSION['editing_client_id']}'");
					
					foreach($phonePostArray as $key => $phone) {
						$client_phone = mysqli_real_escape_string($db, $phone);
						if(isset($phoneIDArray[$key])) {
							if($phone != '') {
								$db->query("UPDATE `phone_numbers` SET `phone` = '$client_phone' WHERE `id` = '{$phoneIDArray[$key]}'");
							} else {
								$db->query("DELETE FROM `phone_numbers` WHERE `id` = '{$phoneIDArray[$key]}'");
							}
						} else {
							if($phone != '') {
								$db->query("INSERT INTO `phone_numbers` SET `client_id` = '{$_SESSION['editing_client_id']}', `phone` = '$client_phone'");
							}
						}
					}	
					
					$phoneInfo = $db->query("SELECT `id`, `phone` FROM `phone_numbers` WHERE `client_id` = '{$_SESSION['editing_client_id']}'");
					$phoneArray = array();
					
					while($phone = $phoneInfo->fetch_assoc()) {
						$phoneArray[] = $phone['phone'];
						$phoneIDArray[] = $phone['id'];
					}
					
					$message = "Client \"" . cleanOutputs($client_fname) . " " .  cleanOutputs($client_lname) . "\" updated!";
					$message_color = "green";
				} else {					
					$db->query("INSERT INTO `clients` SET `first_name` = '$client_fname', `last_name` = '$client_lname', `address` = '$client_address', `address_2` = '$client_address2', `city` = '$client_city', `state` = '$client_state', `zip` = '$client_zip'");
					$_SESSION['client_created'] = $db->insert_id;
					
					foreach($phonePostArray as $key => $phone) {
						if($phone != '') {
							$client_phone = mysqli_real_escape_string($db, $phone);
							$db->query("INSERT INTO `phone_numbers` SET `client_id` = '{$_SESSION['client_created']}', `phone` = '$client_phone'");
						}
					}	
					
					$message = "Client \"" . cleanOutputs($client_fname) . " " .  cleanOutputs($client_lname) . "\" created!";
					$message_color = "green";
					
					unset($client_fname, $client_lname, $client_address, $client_address2, $client_city, $client_state, $client_zip);
					$phoneArray = array();
					$phoneIDArray = array();
				}
			}
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
<!DOCTYPE html>
<head>
	<title>Edit Client</title>

	<link rel="stylesheet" type="text/css" href="popupform.css"/>
	<script src="https://cdn.jsdelivr.net/npm/iframe-resizer@4.2.11/js/iframeResizer.contentWindow.min.js"></script>
</head>
<body>
	<h2 id="header" style="float: left; margin-top: 0px;"></h2>
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
				<div id="phone_numbers"></div>
				<a href="#" onclick="newNumber()">Add Number</a>

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
			<input type="submit" class="form_button" value="Save Client">
		</div>
	</form>	
	
	<script>
		var phoneArray = <?php if(array_filter($phoneArray)) { echo json_encode($phoneArray);} else { echo "''"; } ?>;
		document.getElementById("phone_numbers").innerHTML = '';

		const el = document.createElement('input');
		el.setAttribute('type', 'text');
		el.setAttribute('name', 'client_phone[]');
		
		function newNumber() {
			document.getElementById("phone_numbers").appendChild(el.cloneNode(true));
		}

		if(phoneArray != '') {
			document.getElementById("header").innerHTML = "Edit Client";
			phoneArray.forEach(function(phone) {
				document.getElementById("phone_numbers").innerHTML += `<input type='text' name='client_phone[]' value='${phone}'>`;
			});
		} else {
			document.getElementById("header").innerHTML = "New Client";
			newNumber();
		}
	</script>
</body>
</html>
