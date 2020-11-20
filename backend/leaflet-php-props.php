<?php

class LeafletPhpProps {
	public static function GetDefaultMapProps() {
		return array(
			'center' => [38.62727, -90.34789],
			'zoom' => 12,
			'scrollWheelZoom' => false
		);
	}

	public static function GetZipCodeMapProps() {
		return array(
			'style' => array(
				'fillColor' => 'green',
				'fillOpacity' => 0.8,
				'weight' => 3,
				'color' => 'white'
			), 
		);
	}
}