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
	include_once('sherlook.php');
	include_once('geojson.php');
	include_once('leaflet-php.php');
	include_once('leaflet-php-props.php');
	include_once('leaflet-php-dashboard.php');
	include_once('stl.php');
	include_once('common.php');
	include_once('html_printing.php');
	
?>