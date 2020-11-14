<?php

function GetStlZipCodesFromCsv () {
	$Csv = fopen('reports/stl-zip-pop.csv', 'r');
	// $Csv = fopen('reports/stl-zip-pop-10k-plus.csv', 'r');
	
	$ReturnArray = array();
	$Headers = fgetcsv($Csv);
	while (($CsvRow = fgetcsv($Csv)) !== false) {
		$ReturnRow = array();
		foreach ($Headers as $Index => $Value) {
			$ReturnRow[$Value] = $CsvRow[$Index];
		}
		$ReturnArray[] = $ReturnRow;
	}
	return $ReturnArray;
}

function GetStlZipCodes () {
	$StlCityZipCodes = array(63147,63120,63115,63107,63106,63113,63112,63108,63103,63104,63118,63110,63139,63109,63116,63111,63101,63102);
	$StlCountyZipCodes = array(63137,63136,63138,63033,63135,63134,63121,63133,63130,63105,63114,63132,63124,63117,63144,63143,63119,63123,63125,63128,63127,63126,63122,63131,63141,63034,63031,63042,63044,63043,63140,63074,63045,63146,63017,63005,63011,63021,63040,63038,63088,63129,63025,63069);
	return array_merge($StlCityZipCodes, $StlCountyZipCodes);
}

?>