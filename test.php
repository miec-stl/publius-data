<?php

include('backend/init.php');

$Rows = array(
	array(
		"ContributorId" => null,
		"ContributionDate" => "2020-11-13",
		"CandidateId" => 1,
		"ReportId" => 1,
		"ZipCode" => "63118",
		"Amount" => 666,
		"ContributionData" => array()aoeu
	),
	array(
		"ContributorId" => null,
		"ContributionDate" => "2020-11-13",
		"CandidateId" => 1,
		"ReportId" => 1,
		"ZipCode" => "63119",
		"Amount" => 123,
		"ContributionData" => array()
	)
);

InsertContributions($Rows);

?>