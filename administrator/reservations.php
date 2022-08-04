<?php
	session_start();
	require '../includes/dbConfig.php';
	require 'includes/commonFunctions.php';
	
	// Create the options for our pets select box
	if(isset($_POST['data']) AND $_POST['data'] == "pet_options") {
		$petsArray = getPets($_SESSION['editing_client_id']);
		$petSelectOptionsStr = '';
		if($petsArray != null) {
			foreach($petsArray as $pet) {
				$petSelectOptionsStr = $petSelectOptionsStr .  "<option value=\"{$pet['id']}\">" . cleanOutputs($pet['name']) . " - " . cleanOutputs($pet['breed']) . "</option>";
			}
		}

		echo $petSelectOptionsStr;
	}
	
	// Respond with the info of kennels available during given dates
	if(isset($_POST['data'], $_POST['start_date'], $_POST['end_date']) AND $_POST['data'] == "get_kennel_avail") {
		$startDate = date("Y-m-d",strtotime($_POST['start_date']));
		$endDate = date("Y-m-d",strtotime($_POST['end_date']));
		echo json_encode(getKennelAvail($startDate, $endDate));
	}

	// Create Reservation
	if(isset($_POST['data'], $_POST['start_date'], $_POST['end_date'], $_POST['pets_kennels_array']) AND $_POST['data'] == "create_reservation") {
		// Grab info and check validity as we go. Respond with "ERROR" and exit if anything is off.
		$startDate = date("Y-m-d",strtotime($_POST['start_date']));
		$endDate = date("Y-m-d",strtotime($_POST['end_date']));
		if($startDate > $endDate) {
			echo "ERROR: Start Date is after End Date!";
			exit();
		}
		$petsKennelsArray = $_POST['pets_kennels_array'];
		$petsArray = getPets($_SESSION['editing_client_id']);
		if(!is_array($petsArray)) {
			echo "ERROR: Customer has no pets!";
			exit();
		}
		foreach($petsArray as $pet) {
			$pets[$pet['id']] = $pet['name'];
		}
		$kennelsArray = getKennelAvail($startDate, $endDate);
		if(!is_array($kennelsArray)) {
			echo "ERROR: No Kennels Available for Given Dates";
		}
		foreach($kennelsArray as $kennel) {
			$kennels[] = $kennel['id'];
		}
		$petsKeys = array_keys($petsKennelsArray);
		foreach($petsKeys as $key) {
			if(!in_array($key, array_keys($pets))) {
				echo "ERROR: Invalid Pet ID";
				exit();
			}
		}
		foreach(array_keys($pets) as $petID) {
			if(!in_array($petID, array_keys($petsKennelsArray))) {
				unset($pets[$petID]);
			}
		}
		foreach($petsKennelsArray as $item) {
			if(!in_array($item, $kennels)) {
				echo "ERROR: Invalid Kennel ID";
				exit();
			}
		}

		// Make the entry into the main reservations table and grab it's ID
		$db->query("INSERT INTO reservations (client_id, start_date, end_date) VALUES ('{$_SESSION['editing_client_id']}', '{$startDate}', '{$endDate}')");	
		$reservation_id = $db->insert_id;
		foreach($petsKennelsArray as $pet => $kennel) {
			$db->query("INSERT INTO `reservations_pets` (`reservation_id`, `pet_id`, `kennel_id`) VALUES ('{$reservation_id}', '{$pet}', '{$kennel}')");
		}

		// Grab client's info and then echo our SUCCESS message with helpful info
		$clientInfoQuery = getClientInfo($_SESSION['editing_client_id']);
		foreach($clientInfoQuery as $client) {
			$clientInfo = $client;
		}
		
		echo "SUCCESS: Reservation has been created for {$clientInfo['first_name']} {$clientInfo['last_name']} on {$startDate} - {$endDate} for pets: " . implode(', ',$pets) . "!";
	}
?>
