<style>
	.Error { color:red; }
</style>

<?php

include('init.php');

die('Comment out this die to run this script, and be sure to do it in chunks, starting from ChunkIndex=0!');

// You can a folder of each of your state's zipcodes as geojson at https://github.com/greencoder/us-zipcode-to-geojson
$GeoJsonPath = "../reports/GeoJSON/IL";
$PathFiles = scandir($GeoJsonPath, 1);
$GeoJsonArray = array();

// We can't do a full states GeoJson all at once, it's too much, but
// doing 100 seems to work fine. Run this script ONCE and then update
// the $ChunkNum below to do the next 100 files in the directory. 
// TODO: Clean this up so people can't junk DB running it wrong
$MaxRows = 100;
$ChunkNum = 0;

$FileChunks = array_chunk($PathFiles, $MaxRows);

foreach ($FileChunks[$ChunkNum] as $ThisFile) {
	// echo "<div><h4>$ThisFile</h4>";
	if (!strpos($ThisFile, '.geojson')) {
		echo "<p class='Error'>This is not a .geojson file: skipped</p>";
	} else {
		$GeoJsonContents = file_get_contents($GeoJsonPath.'/'.$ThisFile);
		$GeoJson = json_decode($GeoJsonContents, true);
		$GeoJsonArray[] = array(
			"GeoType" => 'ZIP',
			"GeoName" => substr($ThisFile,0,5),
			"GeoJson" => $GeoJson
		);
	}
	// echo "</div>";
}

// echo "<pre>";
// print_r($GeoJsonArray);
// echo "</pre>";

$Result = InsertGeoJson($GeoJsonArray);
echo "<div>Finished! $Result rows inserted</div>";