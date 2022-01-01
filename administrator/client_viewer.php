<?php
	session_start();
	require_once '../includes/dbConfig.php';

?>

<html>
<head>
	<title>Customer Viewer</title>

	<style>
		#clientSearchDiv {
			height: 100%;
    			width: 300px;
			background: #eeeeee;
			position: absolute;
			top: 0px;
			left: 0px;
		}
		
		#selectClient {
			position: absolute;
			left: 0px;
			right: 0px;
			bottom: 20px;
			width: 256px;
			height: calc(100% - 137px);
			margin: 0px auto !important;	
		}

		select {
			width: 100%;
			height: 100%;
		}

		#searchBar {
			position: absolute;
			left: 0px;
			right: 0px;
			width: 256px;
			margin: 0px auto !important;
		}

		#clientSearch {
			width: 219px;
		}

		#clientInfoDiv {
			display: flex;
			position: relative;
			left: 300px;
			width: fit-content;
		}

		#clientInfo {
			float: left;
			margin-left: 20px;
			width: 300px;
		}

		#clientInfo * {
			margin-left: 20px
		}
	</style>

	<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
	<script>
		$(document).ready(function(){
			$("#clientSelector").load("get_clients.php", {
				searchString: ''
			});

			//$("#searchButton").click(function(){
			$("#clientSearch").keyup(function() {
				var searchValue = document.getElementById("clientSearch").value;
				$("#clientSelector").load("get_clients.php", {
					searchString: searchValue
				});
			});

			$("#clientSelector").change(function() {
				var clientID = this.value;
				$("#clientInfoDiv").load("get_clients.php", {
					client_id: clientID
				});
			});
		});
		

	</script>
</head>
<body>
	<div id="clientSearchDiv">
		<h1 style="margin-left: 22px;">Client Viewer</h1>
		<div id="searchBar">
			<input type="text" name="clientSearch" id="clientSearch">
			<button type="button" id="searchButton" style="width: 32.625px; height: 21px; vertical-align: bottom;">&#x1F50E</button>
		</div>
		<div id="selectClient">
			<select id="clientSelector" multiple></select>
		</div>
	</div>
	
	<h1 style="position: relative; left: 315px; margin-bottom: 0px; width: fit-content;">Client Information</h1>
	<div id="clientInfoDiv">
	</div>
	<hr style="position: relative; left: 300px; width: calc(100% - 325px); margin: 0 15px;">
</body>
</html>
