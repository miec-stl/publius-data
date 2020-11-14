<?php

include('backend/init.php');

$Path = "reports/cara.csv";

$ContributionRowsToAdd = array();
if (($handle = fopen($Path, "r")) !== false) {
	while (($data = fgetcsv($handle, 1000, ',')) !== false) {
		if ($data[0] == 'MECID') { continue; }
		
		$Date = new DateTime($data[3]);
		$ThisRowFormatted = array(
			'ContributorId' => null,	// Not using currently
			'ContributionDate' => $Date->format("Y-m-d"),
			'MecId' => $data[0],
			'ReportId' => 1,
			'ZipCode' => $data[1],
			'IsInKind' => $data[5] == "Monetary" ? 0 : 1,
			'Amount' => $data[4],
			'ContributionData' => array(
				'EmployerData' => $data[2]
			)
		);
		$ContributionRowsToAdd[] = $ThisRowFormatted;
	}
	fclose($handle);
}

InsertContributions($ContributionRowsToAdd);

?>