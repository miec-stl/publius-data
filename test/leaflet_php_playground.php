<?php

include('../backend/init.php');

// Create map (flesh out these comments to help new users play around with it)
$TestLeafletPhp = new LeafletPhp();

$ElectionId = 1;

?>

<head>
	<title>leaflet php playground</title>
	<meta name='viewport' content='width=device-width, initial-scale=1.0'>
	<meta charset='utf-8'>

	<!-- Load Leaflet code library: see http://leafletjs.com/download.html -->
	<link rel='stylesheet' href='https://unpkg.com/leaflet@1.7.1/dist/leaflet.css'>
	<script src='https://unpkg.com/leaflet@1.7.1/dist/leaflet.js'></script>
</head>

<style>
	<?php $TestLeafletPhp->PrintDefaultCss(); ?>
</style>

<body>
	<?php 

		// Here we call functions that print our HTML we need to display the map and dashboard
		$TestLeafletPhp->PrintMapHtml(); 
		//FIXME:
		$TestLeafletPhp->PrintDashboardInput();

	?>
</body>

<script>
	<?php

	// Same deal with Javascript. This needs to happen after the HTML for it to work.
	$TestLeafletPhp->PrintMapJs();
	$TestLeafletPhp->PrintBasemapTiles();

	$MapStyle = LeafletPhpProps::GetZipCodeMapProps();

	// $StlZipCodes = GetStlZipCodes();
	// $TestLeafletPhp->PrintZipCodeMap($StlZipCodes, $MapStyle);

	$TestLeafletPhp->PrintWardMap();
		
	?>
</script>
