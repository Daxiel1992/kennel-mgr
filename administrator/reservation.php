<!DOCTYPE html>
<head>
	<title>Create Reservation</title>
	<link rel="stylesheet" type="text/css" href="popupform.css"/>
	<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/iframe-resizer@4.2.11/js/iframeResizer.contentWindow.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
	<script>
		$(document).ready(function() {
			$('.select2-multiple').select2();
		});
	</script>
</head>
<body>
	<h2 style="float: left; margin-top: 0px;">Create Reservation</h2>
	<?php
		if(!empty($message)) {
			echo "<h4 style='color: {$message_color}; float: left; margin-top: 6px; margin-left: 20px;'>{$message}</h4>";
		}
	?>

	<form method="POST" style="position: absolute; top: 45px; width: calc(100% - 12px);">
		<input type="hidden" name="mode" value="create_client">
		<div class="flex-wrapper">
			<div class="input_div">	
				<label for="start_date">Start Date</label>
				<input type="date" id="start_date" name="start_date" value="">
			</div>

			<div class="input_div">
				<label for="end_date">End Date</label>
				<input type="date" id="end_date" name="end_date" value="">
			</div>					
		</div>
		<div style="position: relative; left: 12px; width: 97.5%;">
			<label for="petSelector">Select Pets</label>
			<select class="select2-multiple" id="pets" name="pets[]" style="width: 75%;" multiple>
			</select>
		</div>

		<div style="float: right;">
			<input type="submit" class="form_button" value="Save Pet" style="margin-bottom: 8px;">
		</div>
	</form>	
	
</body>
</html>
