<?php
	session_start();
	require_once('../includes/dbConfig.php');

	// Controller for the search portion of the Client Viewer. Takes an input string, searches for matches in the users table in the name and phone number, and returns all matches as options in select.
	if(isset($_POST['searchString'])) {
		$searchString = mysqli_real_escape_string($db, $_POST['searchString']);
		$result = $db->query("SELECT * FROM `clients` WHERE `first_name` LIKE '%{$searchString}%' OR `last_name` LIKE '%{$searchString}%' OR `phone` LIKE '%{$searchString}%' ORDER BY `last_name` ASC;");

		while($client = $result->fetch_assoc()) {
			echo "<option value='{$client['id']}'>{$client['last_name']}, {$client['first_name']}</option>";
		}
	}

	// When an option is selected from the search, we grab all the client's info and echo it into the info div.
	if(isset($_POST['client_id']) AND $_POST['data'] == "info") {
		$client_id = mysqli_real_escape_string($db, $_POST['client_id']);
		$clientInfo = $db-> query("SELECT * FROM `clients` WHERE `id` = {$client_id}");

		// Format the data properly and set them to easy to use variables
		while($client = $clientInfo->fetch_assoc()) {
			$client_name = $client['first_name'] . " " . $client['last_name'];
			$client_phone = $client['phone'];
			$client_address = $client['address'];
			$client_address2 = $client['address_2'];
			if($client['city'] != '') {
				$client_zip = $client['city'] . ", " . $client['state'] . " " . $client['zip'];
			}  
		}

		// Display the information
		echo "<div id='clientInfo'>";
			echo "<p>Name: {$client_name}</p>";
			echo "<p>Pets: ";
		echo "</div>";
		echo "<div id='clientInfo'>";
			echo "<p>Phone: {$client_phone}</p>";
			if(!is_null($client_address)) {
				echo "<p style='margin-bottom: 0px;'>Address: {$client_address}</p>";
				echo "<p style='margin-left: 80px; margin-top: 0px; margin-bottom: 0px;'>{$client_address2}</p>";
				if(isset($client_zip)) {
					echo "<p style='margin-left: 80px; margin-top: 0px; margin-bottom: 0px;'>{$client_zip}</p>";
				}
			}
		echo "</div>";
	}

	// For the Previous Reservations Table, we pull the reservations, grab the pets related to it, and format them into a table.
	if(isset($_POST['client_id']) AND $_POST['data'] == "res") {
		$client_id = mysqli_real_escape_string($db, $_POST['client_id']);
		$clientRes = $db->query("SELECT * FROM `reservations` WHERE `client_id` = {$client_id} ORDER BY `start_date` DESC");

		echo "<table id='prevResTable'>
			<tr>
				<th>Date</th>
				<th>Type</th>
				<th>Pets</th>
			</tr>";

		// Loop through each reservation found for the client
		while($res = $clientRes->fetch_assoc()){

			$relations = $db->query("SELECT `pet_id` FROM `reservations_pets` WHERE `reservation_id` = {$res['id']}");
			// Loop through every pet related to said reservation and put them into an array, properly formatted
			while($relation = $relations->fetch_assoc()) {
				$clientPet = $db->query("SELECT * FROM `pets` WHERE `id` = {$relation['pet_id']}");

				while($pet = $clientPet->fetch_assoc()) {
					if(isset($petsArray)) {
						$petsArray[] = "<br>" . $pet['name'] . " - " . $pet['breed'];
					} else {
						$petsArray[] = $pet['name'] . " - " . $pet['breed'];
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
?>
