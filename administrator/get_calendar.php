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
	$reservations = $db->query("SELECT * FROM `reservations` WHERE `start_date` <= '$calDate' AND `end_date` >= '$calDate' ORDER BY `start_date` ASC");

	// Print the date and display the reservations with all relevant information
	echo $calDate . "<br>";

	while($reservation = $reservations->fetch_assoc()) {
		$kennels = $db->query("SELECT `id`, `name` FROM `kennels` WHERE `id` = '{$reservation['kennel_id']}'");
		$clients = $db->query("SELECT `id`, `first_name`, `last_name` FROM `clients` WHERE `id` = '{$reservation['client_id']}'");

		while($kennel = $kennels->fetch_assoc()) {
			$kennel_name = $kennel['name'];
		}
	
		while($client = $clients->fetch_assoc()) {
			$client_name = $client['first_name'] . " " . $client['last_name'];
		}	
		
		echo $reservation['id'] . " | " . $kennel_name . " | " . $client_name . " | Starting: " . $reservation['start_date'] . " Ending: " . $reservation['end_date'] . "<br>";
	}
?>

