<?php
	session_start();
	require_once('../includes/dbConfig.php');
	require '../includes/commonFunctions.php';

	// Controller for the search portion of the Client Viewer. Takes an input string, searches for matches in the users table in the name and phone number, and returns all matches as options in select.
	if(isset($_POST['searchString'])) {
		$searchString = substr(mysqli_real_escape_string($db, $_POST['searchString']), 0, 40);
		$result = $db->query("SELECT * FROM `clients` WHERE `first_name` LIKE '%{$searchString}%' OR `last_name` LIKE '%{$searchString}%' OR `phone` LIKE '%{$searchString}%' ORDER BY `last_name` ASC;");
		
		echo "<option value='new_client'>New Client</option>";

		while($client = $result->fetch_assoc()) {
			echo "<option value='{$client['id']}'>{$client['last_name']}, {$client['first_name']}</option>";
		}
	}

	// When an option is selected from the search, we grab all the client's info and echo it into the info div.
	if((isset($_POST['client_id']) OR isset($_SESSION['editing_client_id'])) AND (isset($_POST['data']) AND $_POST['data'] == "info")) {
		if(isset($_POST['client_id'])) {
			$_SESSION['editing_client_id'] = mysqli_real_escape_string($db, $_POST['client_id']);
		}
		$clientInfo = $db->query("SELECT * FROM `clients` WHERE `id` = '{$_SESSION['editing_client_id']}'");
		$petInfo = $db->query("SELECT `name`, `breed` FROM `pets` WHERE `client_id` = '{$_SESSION['editing_client_id']}'");

		// Format the data properly and set them to easy to use variables
		while($client = $clientInfo->fetch_assoc()) {
			$client_name = cleanOutputs($client['first_name']) . " " . cleanOutputs($client['last_name']);
			$client_phone = cleanOutputs($client['phone']);
			$client_address = cleanOutputs($client['address']);
			$client_address2 = cleanOutputs($client['address_2']);
			if($client['city'] != '') {
				$client_zip = cleanOutputs($client['city']) . ", " . cleanOutputs($client['state']) . " " . cleanOutputs($client['zip']);
			} else {
				$client_zip = "";
			}
		}

		while($pet = $petInfo->fetch_assoc()) {
			$petArray[] = cleanOutputs($pet['name']) . " - " . cleanOutputs($pet['breed']);
		}
		
		// If there are no pets, set the array blank to avoid errors
		if(!isset($petArray)) {
			$petArray[] = "";
		}
		// Display the information
		echo "<div id='clientInfo'>";
			echo "<p>Name: {$client_name}</p>";
			echo "<div style='position: relative;'>";
				echo "<p style='margin: 0px; display: inline-block; position: absolute; top: 0px; left: 0px'>Pets:</p>";
				echo "<p style='display: inline-block; margin: 0px 0px 0px 37px;'>";
				foreach ($petArray as $pet) {
						echo "{$pet}<br>";
				} 
				echo "</p>";
			echo "</div>";
		echo "</div>";
		echo "<div id='clientInfo'>";
			echo "<p>Phone: {$client_phone}</p>";
			echo "<div style='position: relative;'>";
				echo "<p style='margin: 0px; display: inline-block; position: absolute; top: 0px; left: 0px'>Address:</p>";
				echo "<p style='display: inline-block; margin: 0px 0px 0px 61px;'>";
				if($client_address != '') {
					echo "{$client_address}<br>";
				}
				if($client_address2 != '') {
					echo "{$client_address2}<br>";
				}
				if($client_zip != '') {
					echo "{$client_zip}<br>";
				}
				echo "</p>";
			echo "</div>";
		echo "</div>";
		echo "<div id='clientLinks'>
			<a href='#' onclick='showFloating(\"client\")'>Edit Client</a>
			<a href='#' onclick='showFloating(\"pet\")'>Add/Edit Pets</a>
		</div>";

	}

	// For the Previous Reservations Table, we pull the reservations, grab the pets related to it, and format them into a table.
	if(isset($_SESSION['editing_client_id']) AND (isset($_POST['data']) AND $_POST['data'] == "res")) {
		$clientRes = $db->query("SELECT * FROM `reservations` WHERE `client_id` = '{$_SESSION['editing_client_id']}' ORDER BY `start_date` DESC");

		echo "<table id='prevResTable'>
			<tr>
				<th style='width: 30%;'>Date</th>
				<th style='width: 30%;'>Type</th>
				<th style='width: 30%;'>Pets</th>
			</tr>";

		// Loop through each reservation found for the client
		while($res = $clientRes->fetch_assoc()){

			$relations = $db->query("SELECT `pet_id` FROM `reservations_pets` WHERE `reservation_id` = {$res['id']}");
			// Loop through every pet related to said reservation and put them into an array, properly formatted
			while($relation = $relations->fetch_assoc()) {
				$clientPet = $db->query("SELECT * FROM `pets` WHERE `id` = {$relation['pet_id']}");

				while($pet = $clientPet->fetch_assoc()) {
					if(isset($petsArray)) {
						$petsArray[] = "<br>" . cleanOutputs($pet['name']) . " - " . cleanOutputs($pet['breed']);
					} else {
						$petsArray[] = cleanOutputs($pet['name']) . " - " . cleanOutputs($pet['breed']);
					}
				}
			}
			// Finally, add them to a new row in our table.
			echo "  <tr>
					<td>{$res['start_date']} - {$res['end_date']}</td>
					<td>Boarding</td>
					<td>";
					foreach($petsArray as $pet){
						echo $pet;
					}
					echo "</td>
				</tr>";
			

			unset($petsArray);
		}
		
		echo "</table>";
	}

	if(isset($_SESSION['editing_client_id']) AND (isset($_POST['data']) AND $_POST['data'] == 'unsetClient')) {
		unset($_SESSION['editing_client_id']);
	}

	if(isset($_GET['data']) AND $_GET['data'] == "editing_client_id") {
		if(isset($_SESSION['client_created'])) {
			$_SESSION['editing_client_id'] = $_SESSION['client_created'];
			unset($_SESSION['client_created']);
		}
		if(isset($_SESSION['editing_client_id'])) {
			echo $_SESSION['editing_client_id'];
		}
	}
?>
