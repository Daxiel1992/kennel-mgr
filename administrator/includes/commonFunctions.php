<?php
	// A function to check if an input is required and set the input to a variable variable.
	function checkInputs($inputsArray) {
		foreach($inputsArray as $input) {
			global $db, $missingInputs, $longInputs, ${$input[0]};
			if($_POST[$input[0]] == '') {
				${$input[0]} = "";
				if($input[2] == 1) {
					if(empty($missingInputs)) {
						$missingInputs = $missingInputs . "$input[1]";
					} else {	
						$missingInputs = $missingInputs . ", $input[1]";
					}
				}
			} else {
				if(strlen($_POST[$input[0]]) > $input[3]) {
					if(empty($longInputs)) {
						$longInputs = $longInputs . "$input[1]";
					} else {	
						$longInputs = $longInputs . ", $input[1]";
					}
					${$input[0]} = mysqli_real_escape_string($db, $_POST[$input[0]]);
				} else {
					${$input[0]} = mysqli_real_escape_string($db, $_POST[$input[0]]);
				}
			}
		}
	}

	// Makes the variable look presentable in HTML
	function cleanOutputs($input) {
		$input = stripslashes(htmlspecialchars($input));
		return $input;
	}

	function autoFetchAssoc($mysqlObject) {
		while($row = $mysqlObject->fetch_assoc()) {
			$rowArray[] = $row;
		}
		if(!isset($rowArray)) {
			$rowArray = NULL;
		}
		return $rowArray;
	}

	function getPets($clientID) {
		global $db;
		$pets = $db->query("SELECT * FROM `pets` WHERE `client_id` = '{$clientID}'");
		$petsArray = autoFetchAssoc($pets);
		return $petsArray;
	}
	
	function getClientInfo($clientID) {
		global $db;
		$client = $db->query("SELECT * FROM `clients` WHERE `id` = '{$clientID}'");
		$clientInfo = autoFetchAssoc($client);
		return $clientInfo;
	}

	function getKennelAvail($startDate, $endDate) {
		global $db;
		$scheduledIDsQuery = $db->query("SELECT `id` FROM `reservations` WHERE (`start_date` >= '{$startDate}' AND `start_date` <= '{$endDate}') OR (`end_date` >= '{$startDate}' AND `end_date` <= '{$endDate}') OR (`start_date` < '{$startDate}' AND `end_date` > '{$endDate}');");
		if($scheduledIDsQuery->num_rows == 0) {
			$kennelsQuery = $db->query("SELECT * FROM `kennels`");
			return autoFetchAssoc($kennelsQuery);
		}
		$scheduledIDsArray = autoFetchAssoc($scheduledIDsQuery);
		foreach($scheduledIDsArray as $scheduledID) {
			$scheduledIDs[] = $scheduledID['id'];
		}
		$scheduledKennelsQuery = $db->query("SELECT `kennel_id` FROM `reservations_pets` WHERE `reservation_id` IN (".implode(',',$scheduledIDs).")");
		$scheduledKennelsArray = autoFetchAssoc($scheduledKennelsQuery);
		foreach($scheduledKennelsArray as $scheduledKennel) {
			$scheduledKennels[] = $scheduledKennel['kennel_id'];
		}
		$kennelsQuery = $db->query("SELECT * FROM `kennels` WHERE `id` NOT IN (".implode(',',$scheduledKennels).")");
		$kennels = autoFetchAssoc($kennelsQuery);
		return $kennels;
	}
?>
