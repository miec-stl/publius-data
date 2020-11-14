<?php

function InsertGeoJson($GeoRows) {
	if (empty($GeoRows)) { return array(); }
	$Query = "INSERT INTO geojson (GeoType,GeoName,GeoJson) VALUES ";
	$ParamsForBinding = array();
	$ParamTypesString = '';

	foreach ($GeoRows as $Index => $ThisGeoRow) {
		if (!isset($ThisGeoRow['GeoType']) || !isset($ThisGeoRow['GeoName']) || !isset($ThisGeoRow['GeoJson']) || !is_array($ThisGeoRow['GeoJson'])) {
			throw new Exception("Problem with GeoRows: Index $Index");
		}
		// Add parameters for bind_param
		$ParamTypesString .= "sss";
		$ParamsForBinding[] = $ThisGeoRow['GeoType'];
		$ParamsForBinding[] = $ThisGeoRow['GeoName'];
		$ParamsForBinding[] = json_encode($ThisGeoRow['GeoJson']);
		$Query .= '(?,?,?)';
		if ($Index+1 == count($GeoRows)) { 
			$Query .= ";"; 
		} else { 
			$Query .= ","; 
		}
	}

	global $dbConnection;
	$stmt =  $dbConnection->stmt_init();
	if ($stmt->prepare($Query)) {
		// Using reflection to allow arbitrary number of params: https://www.php.net/manual/en/mysqli-stmt.bind-param.php#104073
		$ref = new ReflectionClass('mysqli_stmt');
		$method = $ref->getMethod("bind_param");
		array_unshift($ParamsForBinding, $ParamTypesString);
		// Suppressing error here because it yells at me for this, I hope this causes no problems lol
		@$method->invokeArgs($stmt, $ParamsForBinding);
		$stmt->execute();
		return count($GeoRows);
	} else {
		throw new Exception("Problem with insert: ".$stmt->error);
	}
}

function GetGeoJson($GeoType, $GeoName) {
	$AllowedGeoTypes = array("ZIP");
	if (!in_array($GeoType, $AllowedGeoTypes)) { throw new Exception("Only GeoType now are: ".implode(", ", $AllowedGeoTypes)); }
	
	global $dbConnection;
	$stmt = $dbConnection->prepare('SELECT GeoJson FROM geojson WHERE GeoType = ? AND GeoName = ?');
	$stmt->bind_param('ss', $GeoType, $GeoName);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_object();
	$PolygonJson = OnlyPolygonJson($row->GeoJson);
	return $PolygonJson;
}

function OnlyPolygonJson($GeoJsonObject) {
	$GeoJson = json_decode($GeoJsonObject);
	foreach ($GeoJson->features as $ThisFeature) {
		if ($ThisFeature->geometry->type == 'Polygon' || $ThisFeature->geometry->type == 'MultiPolygon') {
			return json_encode($ThisFeature);
		}
	}

}

?>