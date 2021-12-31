<?php
	session_start();
	require_once('../includes/dbConfig.php');
	
	if(isset($_POST['searchString'])) {
		$searchString = mysqli_real_escape_string($db, $_POST['searchString']);
		$result = $db->query("SELECT * FROM `clients` WHERE `first_name` LIKE '%{$searchString}%' OR `last_name` LIKE '%{$searchString}%' OR `phone` LIKE '%{$searchString}%' ORDER BY `last_name` ASC;");

		while($client = $result->fetch_assoc()) {
			echo "<option value='{$client['id']}'>{$client['last_name']}, {$client['first_name']}</option>";
		}
	}

	if(isset($_POST['client_id'])) {
		$client_id = mysqli_real_escape_string($db, $_POST['client_id']);
		$result = $db-> query("SELECT * FROM `clients` WHERE `id` = {$client_id}");
		
		while($client = $result->fetch_assoc()) {
			$client_name = $client['first_name'] . " " . $client['last_name'];
			$client_phone = $client['phone'];
			$client_address = $client['address'];
			$client_address2 = $client['address_2'];
			$client_zip = $client['city'] . ", " . $client['state'] . " " . $client['zip'];
		}

		echo "<div id='clientInfo'>";
			echo "<p>Name: {$client_name}</p>";
			echo "<p>Dogs: ";
		echo "</div>";
		echo "<div id='clientInfo'>";
			echo "<p>Phone: {$client_phone}</p>";
			echo "<p>Address: {$client_address}<br>{$client_address2}<br>{$client_zip}</p>";
		echo "</div>";
	}
?>
