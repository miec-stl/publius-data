<?php

include('backend/init.php');
include_once('backend/stl.php');

echo "<pre>";

$StlZipPops = GetStlZipCodes();
print_r($StlZipPops);

echo "</pre>";

?>