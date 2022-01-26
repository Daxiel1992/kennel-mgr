<?php
	session_start();
	require '../includes/dbConfig.php';
	require 'includes/auth.php';
?>
<html>
<head>
	<title>iFrame Test</title>

	<style>
		#header {
			position: absolute;
			top: 0px;
			left: 0px;
			height: 37px;
			width: 100%;
		}

		#header-background {
			background-color: #eeeeee;
			height: 100%;
			width: 100%;
		}
		
		#header-logo {
			position: absolute;
			top: 1px;
			left: 1px;
			float: left;
			height: 35px;
		}

		#header-logo img {
			aspect-ratio: 365/215;
			height: 100%;
		}
		
		#nav-links {
			position: absolute;
			vertical-align: baseline;
			top: 0px;
			left: 85px;
		}

		#nav-links input[type=button] {
			float: left;
			margin-top: 8px;
			margin-right: 8px;
		}

		iframe {
			position: absolute;
			width: 100%;
			height: calc(100% - 37px);
			top: 37px;
			left: 0px;
			border: none;
		}
	</style>

	<script src="https://code.jquery.com/jquery-3.6.0.js"></script>

	<script>
		function showiFrame(iFrame) {
			$("iframe").css("display", "none");
			$(`#${iFrame}`).css("display", "block");
		}
	</script>
</head>
<body>
<div id="header">
	<div id="header-background"></div>	
	<div id="header-logo">
		<img src="../img/kennel-mgr-alpha-banner.png">
	</div>
	<div id="nav-links">	
		<input type="button" id="show_cal" onclick="showiFrame('cal')" value="Calendar">
		<input type="button" id="show_viewer" onclick="showiFrame('clientViewer')" value="Client Viewer">
		<input type="button" id="show_res" onclick="showiFrame('res')" value="New Reservation">
	</div>
</div>
<div id="main_block">
<iframe id="cal" src="calendar.php" style="display: block;"></iframe>
<iframe id="res" src="reservation.php" style="display: none;"></iframe>
<iframe id="clientViewer" src="client_viewer.php" style="display: none;"></iframe>
</div>
</body>
</html>
