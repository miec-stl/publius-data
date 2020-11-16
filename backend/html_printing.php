<?php

// This is because I want to have readable HTML functions on this page, but sometimes JS was mad at me for having linebreaks ¯\_(ツ)_/¯
function StripLinebreaks($String) {
	// Via https://stackoverflow.com/questions/10757671/how-to-remove-line-breaks-no-characters-from-the-string
	$LinebreaklessString = str_replace(array("\r", "\n", "\t"), '', $String);
	return $LinebreaklessString;
}

function PrintZipDonationPopup($Zip, $Donation) {
	return "
		<div>
			<h3>ZIP Code $Zip</h3>
			<strong>Donations here:</strong> $Donation
		</div>
	";
}



?>