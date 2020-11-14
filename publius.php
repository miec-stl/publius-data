<?php

include('backend/init.php');
if (isset($_REQUEST['ZipCode']) || strlen($_REQUEST['ZipCode']) == 5) {
	$ZipCode = $_REQUEST['ZipCode'];
} else {
	$ZipCode = '63118';
}

try {
	$GeoJsonString = GetGeoJson("ZIP", $ZipCode);
} catch (Exception $e) {
	$GeoJsonString = null;
}

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
			<input type='text' id='ZipCodeInput' name='ZipCode' value=<?php echo $ZipCode; ?> />
			<button>go</button>
		</form>
	</div>

	<!-- Div placeholder for leaflet map -->
	<div id='map'></div>

	<!-- Create the interactive map content with JavaScript (.js) -->
	<script>
		/* Set up the initial map center and zoom level */
		var map = L.map('map', {
			center: [38.62727, -90.19789],
			zoom: 12,
			scrollWheelZoom: false
		});

		/* display basemap tiles -- see others at https://leaflet-extras.github.io/leaflet-providers/preview/ */
		L.tileLayer('http://stamen-tiles-{s}.a.ssl.fastly.net/toner-background/{z}/{x}/{y}.png', {
			attribution: 'Map tiles by <a href="http://stamen.com">Stamen Design</a>, <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a> &mdash; Map data &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
		}).addTo(map);

		/* Display a point marker with pop-up text */
		L.marker([38.62727, -90.19789]).addTo(map)
		.bindPopup("Insert pop-up text here");

		<?php
		if (!is_null($GeoJsonString)) {
			echo "
				const ZipGeoJsonData = $GeoJsonString;
				L.geoJson(ZipGeoJsonData, {}).addTo(map);
			";
		}
		?>

	</script>

</body>