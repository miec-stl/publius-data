<?php

include('../backend/init.php');

// Create map (flesh out these comments to help new users play around with it)
$TestLeafletPhp = new LeafletPhp();

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

		$TestLeafletPhp->PrintMapHtml();
		$TestLeafletPhp->Dashboard->PrintDashboardPaneHtml();

	?>
</body>

<script>
	<?php 

	$MapStyle = LeafletPhpProps::GetZipCodeMapProps();

	$TestLeafletPhp->PrintMapJs();
	// $TestLeafletPhp->PrintBasemapTiles();
	$TestLeafletPhp->PrintWardMapJs();

	// $StlZipCodes = GetStlZipCodes();
	// $TestLeafletPhp->PrintZipCodeMap($StlZipCodes, $MapStyle);
		
	?>
</script>
