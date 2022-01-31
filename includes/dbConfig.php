<?php
	$program_name = "Kennel Manager";
	$program_version = "Kennel Manager Alpha 2.0.0";

	$dbHost = "localhost";
	$dbUsername = "root";
	$dbPassword = "root";
	$dbName = "test";

	$prod_dev_warning = 1;
	$prod_dev_database_warning = 1;
	
	date_default_timezone_set('America/Boise');
	setlocale(LC_MONETARY, 'en_US');

	//mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	
	$db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

	if ($db->connect_error) {
		die("Connection failed: " . $db->connect_error);
	}

	
?>
