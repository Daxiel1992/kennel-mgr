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

	$appointments = array();

	while($reservation = $get_reservations->fetch_assoc()) {
		$get_relations = $db->query("SELECT * FROM `reservations_pets` WHERE `reservation_id` = '{$reservation['id']}'");
		
		while($relation = $get_relations->fetch_assoc()) {
			$get_pets = $db->query("SELECT `name`, `breed` FROM `pets` WHERE `id` = '{$relation['pet_id']}'");
			$get_kennels = $db->query("SELECT `name` FROM `kennels` WHERE `id` = '{$relation['kennel_id']}'");
			$get_clients = $db->query("SELECT `first_name`, `last_name` FROM `clients` WHERE `id` = '{$reservation['client_id']}'");	
		
			while($kennel = $get_kennels->fetch_assoc()) {
				$kennel_name = $kennel['name'];
			}
			while($pets_array = $get_pets->fetch_assoc()) {
				$pet = $pets_array['name'] . " - " . $pets_array['breed'];	
			}
			while($client = $get_clients->fetch_assoc()) {
				$client_name = $client['first_name'] . " " . $client['last_name'];
			}

			if(count($appointments) > 0) {
				foreach ($appointments as &$a) {
					if($a['kennel_name'] == $kennel_name AND $a['reservation_id'] == $reservation['id'] AND $a['pet'] != $pet) {
						$new_pet = $a['pet'] . " and " . $pet;
						$a['pet'] = $new_pet;
						$added = '1';
					}
				}

				if(!isset($added)) {
					$appointment = array("reservation_id"=>$reservation['id'], "kennel_name"=>$kennel_name, "client_name"=>$client_name, "pet"=>$pet, "start_date"=>$reservation['start_date'], "end_date"=>$reservation['end_date']);
					$appointments[] = $appointment;
				}

			} else {
				$appointment = array("reservation_id"=>$reservation['id'], "kennel_name"=>$kennel_name, "client_name"=>$client_name, "pet"=>$pet, "start_date"=>$reservation['start_date'], "end_date"=>$reservation['end_date']);
				$appointments[] = $appointment;
			}
			unset($added);
		}

		
	}

	foreach($appointments as $appointment) {
		echo "{$appointment['reservation_id']} | {$appointment['kennel_name']} | {$appointment['client_name']} | {$appointment['pet']} | Starting: {$appointment['start_date']} - {$appointment['end_date']}";
		echo "<br>";
	}
	echo "<br>";
?>
