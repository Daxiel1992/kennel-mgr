<?php
	session_start();
	require '../includes/dbConfig.php';
	require '../includes/commonFunctions.php';
	
	// Create needed variables
	$message = "";
	$missingInputs = "";
	$petSelectOptionsStr = "";

	// On form submit
	if(isset($_POST['mode']) AND $_POST['mode'] == "create_client") {
		// Check for required inputs and return cleaned variables
		$inputsArray = array (
			array('pet_name', "Name", 1, 100),
			array('pet_breed', "Breed", 1, 255)
		);
		checkInputs($inputsArray);
		$pet_id = $_POST['petSelector'];

		// Check if any inputs were missing
		if(!empty($missingInputs)) {
			$message_color = "red";
			$message = "Pet's {$missingInputs} is Required!";
		} else {
			if(!empty($longInputs)) {
				$message_color = "red";
				$message = "{$longInputs} is too long of a value!";
			} else {
				// Check if the Pet's ID was a valid input
				if(is_numeric($pet_id)) {
					if($_POST['petSelector'] == 0) {
						// New Pet
						$db->query("INSERT INTO `pets` (`client_id`, `name`, `breed`) VALUES ('{$_SESSION['editing_client_id']}', '$pet_name', '$pet_breed')");
						$message = "Pet \"" . cleanOutputs($pet_name) . "\" created!";
						$message_color = "green";
						unset($pet_id, $pet_name, $pet_breed);
					} else {
						// Existing Pet
						$result = $db->query("SELECT * FROM `pets` WHERE `client_id` = '{$_SESSION['editing_client_id']}' AND `id` = '$pet_id'");
						$count = mysqli_num_rows($result);
						if($count == 1) {
							$db->query("UPDATE `pets` SET `name` = '$pet_name', `breed` = '$pet_breed' WHERE `id` = '$pet_id'");
							$message = "Pet \"" . cleanOutputs($pet_name) . "\" saved!";
							$message_color = "green";
						} else {
							$message = "Invalid Pet!";
							$message_color = "red";
							unset($pet_id);
						}
					}
				} else {
					$message_color = "red";
					$message = "Invalid Pet!";
					unset($pet_id);
				}
			}
		}
	}
	
	// Grab all pets linked to the client and put them in an array to put in our selector and pass to JS to fill in our form	
	$petInfo = $db->query("SELECT * FROM `pets` WHERE `client_id` = '{$_SESSION['editing_client_id']}'");
	$petArray = array();
	while($pet = $petInfo->fetch_assoc()) {
		$petArray[$pet['id']] = array($pet['name'], cleanOutputs($pet['breed']));  

		if(isset($pet_id) AND $pet_id == $pet['id']) {
			$petSelectOptionsStr = $petSelectOptionsStr .  "<option value=\"{$pet['id']}\" selected>" . cleanOutputs($pet['name']) . " - " . cleanOutputs($pet['breed']) . "</option>";
		} else {
			$petSelectOptionsStr = $petSelectOptionsStr .  "<option value=\"{$pet['id']}\">" . cleanOutputs($pet['name']) . " - " . cleanOutputs($pet['breed']) . "</option>";
		}
	}
	$petArrayJS = json_encode($petArray);
?>
<html>
<head>
	<title>Edit Pets</title>
	<link rel="stylesheet" type="text/css" href="popupform.css"/>
	<script>
		<?php
			// Passing the pet array into JS
			echo "var petArray = {$petArrayJS};";
		?>

		// On select change, if pet is selected, fill in form with pet's data. If new pet is selected, clear the form.
		function fillForm() {
			var petID = document.getElementById("petSelector").value;
			if(petID != 0) {
				var petName = petArray[petID][0];
				var petBreed = petArray[petID][1];
			} else {
				var petName = "";
				var petBreed = "";
			}

			document.getElementById("pet_name").value = petName;
			document.getElementById("pet_breed").value = petBreed;
		}
		
	</script>
</head>
<body>
	<h2 style="float: left; margin-top: 0px;">Create/Edit Pet</h2>
	<?php
		if(!empty($message)) {
			echo "<h4 style='color: {$message_color}; float: left; margin-top: 6px; margin-left: 20px;'>{$message}</h4>";
		}
	?>

	<form method="POST" style="position: absolute; top: 45px; width: calc(100% - 12px);">
		<input type="hidden" name="mode" value="create_client">
		<div style="position: relative; left: 12px; width: 97.5%;">
			<label for="petSelector">Select Pet</label>
			<select id="petSelector" name="petSelector" onchange="fillForm();">
				<option value="0">New Pet</option>
				<?php
					// Fill in the selector with the pets
					echo $petSelectOptionsStr;
				?>
			</select>
		</div>
		<div class="flex-wrapper">
			<div class="input_div">	
				<label for="pet_name">Pet's Name</label>
				<input type="text" id="pet_name" name="pet_name" value="<?php if(isset($pet_name)) { echo cleanOutputs($pet_name); } ?>">
			</div>

			<div class="input_div">
				<label for="pet_breed">Pet's Breed</label>
				<input type="text" id="pet_breed" name="pet_breed" value="<?php if(isset($pet_breed)) { echo cleanOutputs($pet_breed); } ?>">
			</div>					
		</div>

		<div style="float: right;">
			<input type="submit" class="form_button" value="Save Pet">
		</div>
	</form>	
	
</body>
</html>
