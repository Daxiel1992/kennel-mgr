<?php
	session_start();
	require '../includes/dbConfig.php';
	require 'includes/auth.php';

	$result = $db->query("SELECT * FROM clients");
	while($row = $result->fetch_assoc()) {
		$clients_array[$row['id']] = "{$row['first_name']} {$row['last_name']}";
	}

	if(isset($_POST['mode']) AND $_POST['mode'] == "search_availability") {
		$_SESSION['client_id'] = mysqli_real_escape_string($db, $_POST['client_id']);
		$_SESSION['start_date'] = date("Y-m-d",strtotime($_POST['start_date']));
		$_SESSION['end_date'] = date("Y-m-d",strtotime($_POST['end_date']));
		if($_SESSION['start_date'] > $_SESSION['end_date']){
			$message = "End Date can not be before the Start Date!";//Error message to show above form
			$message_color = "red";
			unset($_POST['mode'], $_SESSION['client_id'], $_SESSION['start_date'], $_SESSION['end_date']);
		} else {
			//Search database for available reservation between start and end dates
			$result = $db->query("SELECT `kennel_id` FROM `reservations` WHERE (`start_date` >= '{$_SESSION['start_date']}' AND `start_date` <= '{$_SESSION['end_date']}') OR (`end_date` >= '{$_SESSION['start_date']}' AND `end_date` <= '{$_SESSION['end_date']}') OR (`start_date` < '{$_SESSION['start_date']}' AND `end_date` > '{$_SESSION['end_date']}');");
	
			while($row = $result->fetch_assoc()) {
				$scheduled_array[] = $row['kennel_id'];
			}

			if(!is_array($scheduled_array)){$scheduled_array = array();}
		}
		
	}

	if (isset($_POST['mode']) AND $_POST['mode'] == "create_reservation") {
		$kennel_id = $_POST['kennel_id'];	
		$db->query("INSERT INTO reservations (client_id, kennel_id, start_date, end_date) VALUES ('{$_SESSION['client_id']}', '$kennel_id', '{$_SESSION['start_date']}', '{$_SESSION['end_date']}')");
		$message = "Reservation created for {$clients_array[$_SESSION['client_id']]} for {$_SESSION['start_date']} through {$_SESSION['end_date']}!";
		$message_color = "green";
		unset($_SESSION['client_id'], $_SESSION['start_date'], $_SESSION['end_date']);
	}
?>

<html>
	<head>
		<title>Database Testing</title>
	</head>
	<body>
		<?php
			//Header and any errors or messages
			require 'includes/header.php';
			if(isset($message)){
				echo "<h3 style='color: {$message_color};'>{$message}</h3>";
			}
		?>
		<form action="" method="post">
			<input type="hidden" name="mode" value="search_availability">
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
			</select><br>
			Start Date: <input type="date" name="start_date" value="<?php if(isset($_SESSION['start_date'])){echo $_SESSION['start_date'];}?>"><br>
			End Date: <input type="date" name="end_date" value="<?php if(isset($_SESSION['end_date'])){echo $_SESSION['end_date'];}?>"><br>
			<input type="submit" value="Search for Availabilities">
		</form>

		<?php
			if(isset($_POST['mode']) AND $_POST['mode'] == "search_availability") {
		?>
		<form action="" method="post">
			<input type="hidden" name="mode" value="create_reservation">
			Kennels: <select name="kennel_id">
			<?php
				$result = $db->query("SELECT * FROM kennels");
				while($row = $result->fetch_assoc()) {
					$kennels_array[] = $row;
				}

				foreach($kennels_array as $kennel => $value) {
					if(!in_array($value['id'], $scheduled_array)) {
						echo "<option value='{$value['id']}'>{$value['name']}</option>";
						$i++;
					}	
				}
			?>
			</select>
			<?php
				if(!isset($i)){
					echo "<h3 style='color: red;'>No Kennels Available.</h3>";
				} else {
					echo "<br><input type='submit' value='Create Reservation'>";

			
				}
			?>
					</form>
		<?php
			}
		?>
	</body>
</html>
