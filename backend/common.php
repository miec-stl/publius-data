<?php

function CheckDateFormat($DateString, $DesiredFormat='Y-m-d') {
	$TestDateTimeObject = DateTime::createFromFormat($DesiredFormat, $DateString);
	if (!$TestDateTimeObject) {
		throw new Exception("Error (#44902) Date is not in '".$DesiredFormat."' format)");
	}
}

?>