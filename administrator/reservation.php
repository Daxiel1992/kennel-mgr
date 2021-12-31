<?php
	session_start();
	require_once '../includes/dbConfig.php';

	$result = $db->query("SELECT * FROM clients");
	while($row = $result->fetch_assoc()) {
		$clients_array[$row['id']] = "{$row['first_name']} {$row['last_name']}";
	}

	if(!isset($_POST['mode'])) {
			unset($_SESSION['client_id'], $_SESSION['start_date'], $_SESSION['end_date'], $_SESSION['open_kennels'], $_SESSION['separated'], $_SESSION['pets_array']);
	}
		
	if(isset($_POST['mode']) AND $_POST['mode'] == "select_pets") {
		$_SESSION['client_id'] = mysqli_real_escape_string($db, $_POST['client_id']);
		$_SESSION['start_date'] = date("Y-m-d",strtotime($_POST['start_date']));
		$_SESSION['end_date'] = date("Y-m-d",strtotime($_POST['end_date']));
		if($_SESSION['start_date'] > $_SESSION['end_date']){
			$message = "End Date can not be before the Start Date!";//Error message to show above form
			$message_color = "red";
			unset($_POST['mode'], $_SESSION['client_id'], $_SESSION['start_date'], $_SESSION['end_date']);
		} else {
			// Search database for conflicting reservations between start and end dates
			$result = $db->query("SELECT `id` FROM `reservations` WHERE (`start_date` >= '{$_SESSION['start_date']}' AND `start_date` <= '{$_SESSION['end_date']}') OR (`end_date` >= '{$_SESSION['start_date']}' AND `end_date` <= '{$_SESSION['end_date']}') OR (`start_date` < '{$_SESSION['start_date']}' AND `end_date` > '{$_SESSION['end_date']}');");
	
			while($row = $result->fetch_assoc()) {
				$scheduled_ids[] = $row['id'];
			}

			$scheduled_array = array();
			
			// Find which kennels are being used for the conflicting reservations
			if(isset($scheduled_ids)) {
				$result = $db->query("SELECT `kennel_id` FROM `reservations_pets` WHERE `reservation_id` IN (".implode(',',$scheduled_ids).")");
			
				while($row = $result->fetch_assoc()) {
					if(!in_array($row['kennel_id'], $scheduled_ids)) {
						$scheduled_array[] = $row['kennel_id'];
					}
				}
			}
		}
		
	}


	// Create the Reservation
	if (isset($_POST['mode']) AND $_POST['mode'] == "create_reservation") {
		// Make the entry into the main reservations table and grab it's ID
		$db->query("INSERT INTO reservations (client_id, start_date, end_date) VALUES ('{$_SESSION['client_id']}', '{$_SESSION['start_date']}', '{$_SESSION['end_date']}')");	
		$reservation_id = $db->insert_id;

		// Check if the pets are going to be in seperate kennels
		if($_SESSION['separated'] == '0') {
			// If not, make the entries for each pet in the same kennel
			$kennel_id = $_POST['kennel_id'];
			foreach($_SESSION['pets_array'] as $pet) {
				$db->query("INSERT INTO `reservations_pets` (`reservation_id`, `pet_id`, `kennel_id`) VALUES ('{$reservation_id}', '{$pet['id']}', '{$kennel_id}')");
			}
		} else if($_SESSION['separated'] == '1') {
			// If so, grab the values for the unique select boxes created and make the reservation with those
			foreach($_SESSION['pets_array'] as $pet) {
				$kennel_id = $_POST['kennel' . $pet['id']];
				$db->query("INSERT INTO `reservations_pets` (`reservation_id`, `pet_id`, `kennel_id`) VALUES ('{$reservation_id}', '{$pet['id']}', '{$kennel_id}')");
			}
		}
		
		$message = "Reservation created for {$clients_array[$_SESSION['client_id']]} for {$_SESSION['start_date']} through {$_SESSION['end_date']}!";
		$message_color = "green";
		unset($_SESSION['client_id'], $_SESSION['start_date'], $_SESSION['end_date']);
	}
?>

<html>
	<head>
		<title>Database Testing</title>
		<!--- Script for Select 2 for multiple selections --->
		<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
		<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
		<script>
			$(document).ready(function() {
				$('.select2-multiple').select2();
				
				// Set our Separate Checkbox as display none and only show it if more than 1 pet is selected as staying
				var sep_check = document.getElementById("sep_wrapper")
				sep_check.style.display = "none";

				$('#pets').on('change', function (e) {
					if($('#pets').val().length < '2') {
						sep_check.style.display = "none";
					} else {
						sep_check.style.display = "inline-block";
					}
				});
			});
			
			function openFloating() {
				$(`#floating`).css('display', 'block');
				$(`#floating_bg`).css('display', 'block');
			}
			
			function closeFloating() {
				location.replace("reservation.php");
			}

			function openCreatePet() {
				$(`#createPet`).css('display', 'block');
				$(`#floating_bg`).css('display', 'block');
			}

			function closeCreatePet() {
				document.forms["clientForm"].submit();
			}
		</script>
		<style>
			#floating {
				position: absolute;
				top: 85px;
				left: 0px;
				right: 0px;
   				width: 892px;
    				height: 309px;
				margin: 0px auto !important;
				background-color: white;
				box-shadow: #00000040 0px 0px 30px;
				z-index: 2;
			}
			
			#floating_bg {
				background: rgba(0, 0, 0, 0.578);
				position: absolute;
				top: 0px;
				left: 0px;
				width: 100%;
				height: 100%;
				z-index: 1;
			}

			#createPet {
				position: absolute;
				top: 85px;
				left: 0px;
				right: 0px;
   				width: 892px;
    				height: 231px;
				margin: 0px auto !important;
				background-color: white;
				box-shadow: #00000040 0px 0px 30px;
				z-index: 2;
			}
		</style>
	</head>
	<body>
		<?php
			//Header and any errors or messages
			if(isset($message)){
				echo "<h3 style='color: {$message_color};'>{$message}</h3>";
			}
		?>
		<div id="floating_bg" style="display: none;" ></div>
		<div id="floating" style="display: none;">
			<button type="button" onclick="closeFloating()" style="position: absolute; top: 0; right: 0; margin: 5px">X</button>
			<iframe src="new_client.php" style="width: inherit; height: inherit; display: block; border: none;"></iframe>
		</div>
		
		<!--- First form to select the Client, Start Date, and End Date --->
		<form id="clientForm" action="" method="post">
			<input type="hidden" name="mode" value="select_pets">
			Client: <select name="client_id">
			<?php 
				foreach($clients_array as $client => $value) {
					if(isset($_SESSION['client_id']) AND $client == $_SESSION['client_id']) {
						$selected = "SELECTED";
					} else {
						$selected = "";
					}
					echo "<option value='{$client}' {$selected}>{$value}</option>";
				}
			?>
			</select><button type="button" onclick="openFloating()" style="margin-left: 5px;">New Client</button>
			<br>
			Start Date: <input type="date" name="start_date" value="<?php if(isset($_SESSION['start_date'])){echo $_SESSION['start_date'];}?>"><br>
			End Date: <input type="date" name="end_date" value="<?php if(isset($_SESSION['end_date'])){echo $_SESSION['end_date'];}?>"><br>
			<input type="submit" value="Select Pets">
		</form>

		<!--- After the first POST, we find all the open kennels and hold them as SESSION variables so we don't have to query again :P --->
		<?php
			if(isset($_POST['mode']) AND $_POST['mode'] == "select_pets") {
				
				if(isset($_POST['mode'])) {
					$result = $db->query("SELECT * FROM kennels");
					while($row = $result->fetch_assoc()) {
						$kennels_array[] = $row;
					}
			
					foreach($kennels_array as $kennel) {
						if(!in_array($kennel['id'], $scheduled_array)) {
							$open_kennels[] = $kennel;
						}	
					}

					$num_open = count($open_kennels);
				}
				
				// Tell how many kennels are open before we go to far with the reservation in case of full kennel.
				if($num_open > 0) {
					echo $num_open . " open kennels!";
					$_SESSION['open_kennels'] = $open_kennels;
				} else {
					echo "No Open Kennels";
				}

		?>

		<div id="createPet" style="display: none;">
			<button type="button" onclick="closeCreatePet()" style="position: absolute; top: 0; right: 0; margin: 5px">X</button>
			<iframe src="new_pet.php" style="width: inherit; height: inherit; display: block; border: none;"></iframe>
		</div>

		<form action="" method="post">
			<input type="hidden" name="mode" value="find_kennels">
			<!--- Our Multiple Selector that displays every pet the client owns --->
			Pets: <select class="select2-multiple" id="pets" name="pets[]" style="width: 75%;" multiple>
				<?php
					$result = $db->query("SELECT * FROM `pets` WHERE `client_id` = '{$_SESSION['client_id']}'");
					while($row = $result->fetch_assoc()) {
						$clients_pets[] = $row;
					}

					foreach($clients_pets as $pet) {
						echo "<option value='{$pet['id']}'>{$pet['name']} - {$pet['breed']}</option>";
					}
					
					// Save as SESSION variable so we don't make an unneeded query later
					$_SESSION['clients_pets'] = $clients_pets;
				?>
			</select><button type="button" onclick="openCreatePet()">New Pet</button>
			<br>
			<!--- The magic disappearing Checkbox that only appears when their are two or more pets selected --->
			<label for="separated" id="sep_wrapper">
				<input type="checkbox" id="separated" name="separated" value="y">
				<span>Separated?</span>
			</label>
			<br>
			<input type="submit" value="Find Kennels">
		</form>
		<?php

			}
		?>

		<!--- Finally, ask which kennels we want to put the pets in --->
		<?php
			if(isset($_POST['mode']) AND $_POST['mode'] == "find_kennels") {
		?>
		<form action="" method="post">
			<input type="hidden" name="mode" value="create_reservation">
			<?php
				// Grab the client's pet's info that is relevant
				foreach($_SESSION['clients_pets'] as $pet) {
					if(in_array($pet['id'], $_POST['pets'])) {
						$pets_array[] = $pet;
					}
				}
				
				// If pets need to be separated, display unique kennel select boxes for each pet. Else, just display one kennel select.
				// Set the Separated value as SESSION for when the reservation is being created.
				if(isset($_POST['separated'])) {
					foreach($pets_array as $pet) {
						echo "Kennel for {$pet['name']}: <select name='kennel{$pet['id']}'>";
						foreach($_SESSION['open_kennels'] as $kennel) {
								echo "<option value='{$kennel['id']}'>{$kennel['name']}</option>";
						}							
						echo "</select><br>";
					}
					$_SESSION['separated'] = '1';
				} else {
					echo "Kennel: <select name='kennel_id'>";
					foreach($_SESSION['open_kennels'] as $kennel) {
						echo "<option value='{$kennel['id']}'>{$kennel['name']}</option>";
					}
					echo "</select>";
					$_SESSION['separated'] = '0';
				}

				// Set as SESSION to pass to our processing
				$_SESSION['pets_array'] = $pets_array;
			?>
			<br>
			<input type="submit" value="Create Reservation">
		</form>
		<?php
			}
		?>
	</body>
</html>
