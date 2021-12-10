<?php
	require	'../includes/dbConfig.php';

	// Format date string passed by the JS. If none was provided, just use today's date.	
	if(isset($_POST['newDate'])) {
		$calDate = strtotime($_POST['newDate']);
		$calDate = date("Y-m-d", $calDate);
	} else {
		$calDate = new DateTime();
		$calDate = $calDate->format("Y-m-d");
	}
	
	// Grab all reservations that are current for that day and order them by start date
	$get_reservations = $db->query("SELECT * FROM `reservations` WHERE `start_date` <= '$calDate' AND `end_date` >= '$calDate' ORDER BY `start_date` ASC");

	// Print the date and display the reservations with all relevant information
	echo $calDate . "<br>";

	while($reservation = $get_reservations->fetch_assoc()) {
		$relations = $db->query("SELECT * FROM `reservations_pets` WHERE `reservation_id` = '{$reservation['id']}'");
			while($relation = $relations->fetch_assoc()) {
				$get_pets = $db->query("SELECT `name`, `breed` FROM `pets` WHERE `id` = '{$relation['pet_id']}'");
				$get_kennels = $db->query("SELECT `name` FROM `kennels` WHERE `id` = '{$relation['kennel_id']}'");
				$get_clients = $db->query("SELECT `first_name`, `last_name` FROM `clients` WHERE `id` = '{$reservation['client_id']}'");

				while($pets_array = $get_pets->fetch_assoc()) {
					$pets[] = $pets_array;
				}

				while($kennel = $get_kennels->fetch_assoc()) {
					$kennel_name = $kennel['name'];
				}
	
				while($client = $get_clients->fetch_assoc()) {
					$client_name = $client['first_name'] . " " . $client['last_name'];
				}	
		
				echo $reservation['id'] . " | " . $kennel_name . " | " . $client_name . " | "; foreach($pets as $pet){echo $pet['name'] . " - " . $pet['breed'] . " | ";} echo "Starting: " . $reservation['start_date'] . " Ending: " . $reservation['end_date'] . "<br>";

				unset($pets);

			}	

	}
?>

