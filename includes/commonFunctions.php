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

?>
