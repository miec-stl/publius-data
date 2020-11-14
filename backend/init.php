<?php

	include('config.php');

	global $dbConnection;
	$dbConnection = new mysqli(
		'localhost',
		DB_POPLICOLA_USERNAME,
		DB_POPLICOLA_PASSWORD,
		DB_PUBLIUS_NAME
	);

	include_once('poplicola.php');

?>