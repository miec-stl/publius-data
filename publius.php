<?php

include('backend/init.php');
if (isset($_REQUEST['ZipCode']) && strlen($_REQUEST['ZipCode']) == 5) {
	$SelectedZipCode = $_REQUEST['ZipCode'];
} else {
	$SelectedZipCode = '63118';
}

// try {
// 	$GeoJsonString = GetGeoJson("ZIP", $SelectedZipCode);
// } catch (Exception $e) {
// 	$GeoJsonString = null;
// }

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
</style>

<body>
	<!-- Map form (to become sidebar) -->
	<div id='map-title'>
		publius
		<form>
			<label for='ZipCodeInput'>Zip Code</label>
			<input type='text' id='ZipCodeInput' name='ZipCode' value=<?php echo $SelectedZipCode; ?> />
			<button>go</button>
		</form>
	</div>

	<!-- Div placeholder for leaflet map -->
	<div id='map'></div>

	<!-- Leaflet Javascript -->
	<script>
		<?php 
			
			$StlZipCodes = GetStlZipCodes();
			LeafletPhp::BasicZipMap($StlZipCodes, $SelectedZipCode);

		?>
	</script>

</body>