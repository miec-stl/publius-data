<?php

include('backend/init.php');

$GeoJson = GetGeoJson("ZIP", "63118");

echo "<pre>";
print_r($GeoJson);
echo "</pre>";

?>