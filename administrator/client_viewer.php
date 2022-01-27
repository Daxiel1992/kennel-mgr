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
			position: relative;
			left: 300px;
			width: calc(100% - 300px);
		}

		#clientInfo {
			float: left;
			margin-left: 20px;
			margin-bottom: 10px;
			width: 400px;
		}

		#clientInfo * {
			margin-left: 20px
		}

		#clientPrevRes {
			position: relative;
			left: 300;
			width: calc(100% - 330px);
			flex: 1 1 auto;
			margin: 10px 15px;
			border: solid 0.5px #9a9a9a;
			background: ;
			overflow-y: scroll;
		}

		#clientLinks {
			position: relative;
			width: fit-content;
			left: 20px;
		}

		#floating {
			position: absolute;
			top: 85px;
			left: 0px;
			right: 0px;
			width: 892px;
			height: 380px;
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

		table {
			width: 100%;
			border-collapse: collapse;
		}

		td, th {
			border: solid 1px #dddddd;
			text-align: left;

		}
	</style>

	<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
	<script>
		// Set clientID and searchValue to use globally
		var clientID = null;
		var searchValue = null;

		$(document).ready(function(){
			$("#clientSelector").load("get_clients.php", {
				searchString: ''
			});

			// Load the select box based on the search term given
			//$("#searchButton").click(function(){
			$("#clientSearch").keyup(function() {
				searchValue = document.getElementById("clientSearch").value;
				$("#clientSelector").load("get_clients.php", {
					searchString: searchValue
				}, function() {
					document.getElementById("clientSelector").value = clientID;
				});
			});

			// When a client is selected, load their info and reservations into seperate divs
			$("#clientSelector").change(function() {
				clientID = this.value;
				$("#clientInfoDiv").load("get_clients.php", {
					client_id: clientID,
					data: "info"
				});
				$("#clientPrevRes").load("get_clients.php", {
					client_id: clientID,
					data: "res"
				});
			});
		});


		// Functions to control the iFrame and their parent divs
		function showFloating() {
			document.getElementById("floating").innerHTML = `<iframe src="client_editor.php?client_id=${clientID}" style="width: 100%; height: 100%; border: 0;"></iframe>`;
			$("#floating").css('display', 'block');
			$(`#floating_bg`).css('display', 'block');
		}

		function hideFloating() {
			$("#floating").css('display', 'none');
			$(`#floating_bg`).css('display', 'none');
			$("#clientSelector").load("get_clients.php", {
				searchString: searchValue
			}, function() {
				document.getElementById("clientSelector").value = clientID;
			});
			$("#clientInfoDiv").load("get_clients.php", {
				client_id: clientID,
				data: "info"
			});
		}
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
	<div id="clientContainer" style="display: flex; flex-direction: column; height: 100%;">
		<h1 style="position: relative; left: 315px; margin-bottom: 0px; width: fit-content;">Client Information</h1>
		<div id="clientInfoDiv">	
		</div>
		<hr style='position: relative; left: 300px; width: calc(100% - 330px); margin: 0 15px;'>
		<div id="clientPrevRes">
		</div>
	</div>
	<div id="floating_bg" style="display: none;" onclick="hideFloating()"></div>
	<div id="floating" style="display: none;"></div>
</body>
</html>
