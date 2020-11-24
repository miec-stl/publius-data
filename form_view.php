<?php

include('backend/init.php');

// Handle requests
$SelectedElection = isset($_REQUEST['Election']) ? $_REQUEST['Election'] : null;
$SelectedCandidateId = isset($_REQUEST['CandidateId']) ? $_REQUEST['CandidateId'] : null;

$Dashboard = new LeafletPhpDashboard($_REQUEST);
$Sherlook = new Sherlook($_REQUEST);
?>

<head>
	<title>donor project - form view</title>
	<meta name='viewport' content='width=device-width, initial-scale=1.0'>
	<meta charset='utf-8'>

	<!-- Load Leaflet code library: see http://leafletjs.com/download.html -->
	<link rel='stylesheet' href='https://unpkg.com/leaflet@1.7.1/dist/leaflet.css'>
	<script src='https://unpkg.com/leaflet@1.7.1/dist/leaflet.js'></script>
</head>

<style>

</style>

<body>

<div id='SelectForm'>
	<?php echo $Dashboard->GetElectionFormHtml() ?>
</div>

<div id='DonationsInfo'>
	<ol>
	<?php
		// $CommitteeDonations = $Sherlook->GetCommitteeDonations();
		// $ListItems = array_map(function($Committee) {
		// 	return "<li>\$$Committee->TotalFromCommittee - <em>$Committee->FromCommittee</em></li>";
		// }, $CommitteeDonations);
		

		$AggregatedValues = $Sherlook->GetAggregateIndividualDonations();
		uasort($AggregatedValues, function($a,$b) { return ($a > $b) ? -1: 1; });
		$ListItems = array();
		foreach($AggregatedValues as $Index => $Row) {
			$ListItems[] = "<li>$Index: \$$Row[Total] - </em></li>";
		}

		echo implode("", $ListItems);
	?>
	</ol>
</div>

</body>

</body>