<?php
	session_start();
	require 'includes/header.php';
?>
<html>
<head>
	<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
	<script>
		// Create today's date
		var date = new Date();

		$(document).ready(function(){
			// Previous day button
			$("#prev_day").click(function(){
				// Subtract one day and format properly to be passed to PHP
				date.setDate(date.getDate() - 1);
				forDate = date.toDateString();
				$("#reservations").load("get_calendar.php", {
					newDate: forDate
				});
			});
			
			// Next day button
			$("#next_day").click(function(){
				date.setDate(date.getDate() + 1);
				forDate = date.toDateString();
				$("#reservations").load("get_calendar.php", {
					newDate: forDate
				});
			});
		});	
	</script>
</head>
<body>
	<br>
	<div id="reservations">
		<!--- Require our calendar page to load the data without AJAX to begin with --->
		<?php require './get_calendar.php'; ?>
	</div>
	<br>
	<button id="prev_day">Previous Day</button>
	<button id="next_day">Next Day</button>
</body>
</html>
