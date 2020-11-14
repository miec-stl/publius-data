<?php

class LeafletPhp {

	private $MapId;
	private $LeafletProps;

	function __construct($Props) {
		$this->MapName = $Props['MapName'];
		$this->MapId = $Props['MapId'];
		$this->LeafletProps = $Props['LeafletProps'];
	}

	/* Set up the initial map center and zoom level */
	public function PrintMapJs() {
		echo "var $this->MapName = L.map('$this->MapId', ".json_encode($this->LeafletProps).");\n";
	}

	/* display basemap tiles -- see others at https://leaflet-extras.github.io/leaflet-providers/preview/ */
	public function PrintBasemapTiles() {
		$TileSet = 'http://stamen-tiles-{s}.a.ssl.fastly.net/toner-background/{z}/{x}/{y}.png';
		$TileAttr = 'Map tiles by <a href="http://stamen.com">Stamen Design</a>, <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a> &mdash; Map data &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>';
		echo "L.tileLayer('$TileSet', {attribution: '$TileAttr'}).addTo($this->MapName);\n";
	}

	/* Display a point marker with pop-up text */
	public function AddMarker($Coordinates, $Text) {
		echo "L.marker($Coordinates).addTo($this->MapName).bindPopup(\"$Text\");";
	}

	/* Add a GeoJSON geography to the map */
	public function AddGeoJson($GeoJsonString, $GeoJsonProps=array()) {
		if (!is_array($GeoJsonProps)) { throw new Exception("Invalid props for AddGeoJson"); }
		echo "L.geoJson($GeoJsonString, ".json_encode($GeoJsonProps).").addTo($this->MapName);\n";
	}

	/**
	 * Full maps
	 */

	public static function BasicZipMap($ZipCodes, $SelectedZipCode) {
		$PrimaryMapProps = array(
			'MapName' => 'ZipMap',
			'MapId' => 'map', 
			'LeafletProps' => array(
				'center' => [38.62727, -90.24789],
				'zoom' => 12,
				'scrollWheelZoom' => false
			)
		);

		$PrimaryMap = new LeafletPhp($PrimaryMapProps);
		$PrimaryMap->PrintMapJs();
		$PrimaryMap->PrintBasemapTiles();
		$PrimaryMap->AddMarker("[38.62727, -90.19789]", "Popup text example");	// TODO: Find how to do coordinates better? At some point

		//TODO: Bulk version of this:
		foreach ($ZipCodes as $ThisZip) {
			$GeoJsonProps = array(
				'style' => array(
					'fillColor' => ($ThisZip == $SelectedZipCode ? 'green' : 'blue'),
					'weight' => 2,
					'color' => 'white'
				)
			);				
			$PrimaryMap->AddGeoJson(GetGeoJson("ZIP", $ThisZip), $GeoJsonProps);
		}
	}
}