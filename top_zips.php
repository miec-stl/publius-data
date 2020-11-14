<?php

include('backend/init.php');

// Handle requests
$SelectedElection = isset($_REQUEST['Election']) ? $_REQUEST['Election'] : null;
$SelectedCandidateId = isset($_REQUEST['CandidateId']) ? $_REQUEST['CandidateId'] : null;

$StlZipCodes = GetStlZipCodes();

?>

<head>
	<title>publius - under construction</title>
	<meta name='viewport' content='width=device-width, initial-scale=1.0'>
	<meta charset='utf-8'>

	<!-- Load Leaflet code library: see http://leafletjs.com/download.html -->
	<link rel='stylesheet' href='https://unpkg.com/leaflet@1.7.1/dist/leaflet.css'>
	<script src='https://unpkg.com/leaflet@1.7.1/dist/leaflet.js'></script>
</head>

<!-- Position the map and title with Cascading Style Sheet (.css) -->
<style>
	body { margin:0; padding:0; }
	#map {
		position: absolute;
		top:0;
		bottom:0;
		right:0;
		left:0;
	}
	#map-title {
		position: relative;
		margin-top: 10px;
		margin-left: 50px;
		float: left;
		background: white;
		border: 2px solid rgba(0,0,0,0.2);
		padding: 6px 8px;
		font-family: Helvetica;
		font-weight: bold;
		font-size: 24px;
		z-index: 800;
	}
	#dashboard {
		position: relative;
		height: 100vh;
		width: 20%;
		min-width: 200px;
		background-color: white;
		border: 2px solid rgba(0,0,0,0.2);
		padding: 82px 8px 20px;
		z-index: 400;
		line-height: 1.6em;
	}

	#DonationStats {
		width: 80%;
		max-width: 240px;
		margin: auto;
		text-align: center;
	}

	.DonationStatsHeader {
		font-weight:bold;
		display: flex;
		justify-content: space-evenly;
		padding-right: 12px;
	}

	.DonationStatsScrollable > div {
		display: flex;
		justify-content: space-evenly;
	}

	.DonationStatsScrollable {
		max-height: 300px;
		overflow-y: scroll;
	}

</style>

<body>

	<?php
		// Create contributions Zip Code map
		$ThisMapId = 'map';
		$DonationsByZipProps = array(
			'MapName' => 'DonationsByZipMap',
			'MapId' => $ThisMapId,			
			'LeafletProps' => array(
				'center' => [38.62727, -90.24789],
				'zoom' => 12,
				'scrollWheelZoom' => true
			) 
		);
		$DonationsByZipMap = new LeafletPhp($DonationsByZipProps);
		$DonationsByZipMap->SetElection($SelectedElection);
		$DonationsByZipMap->SetCandidate($SelectedCandidateId);
	?>


	<!-- Div placeholder for leaflet map -->
	<div id='<?php echo $ThisMapId; ?>'></div>

	<!-- Dashboard -->
	<?php $DonationsByZipMap->PrintDashboardInput(); ?>

	<!-- Leaflet Javascript -->
	<script>
		<?php 
			LeafletPhp::PrintBasicZipMap($StlZipCodes); 
		?>
	</script>

</body>