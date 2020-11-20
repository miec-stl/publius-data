<?php

class LeafletPhp {

	private $MapId;
	private $LeafletProps;

	private $CurrentElection;
	private $ElectionCandidates;
	private $CurrentCandidate;

	private $SelectedStartDate;
	private $SelectedEndDate;

	function __construct($Props = array()) {
		$this->MapName = isset($Props['MapName']) ? $Props['MapName'] : 'LeafletPhpMap';
		$this->MapId = isset($Props['MapId']) ? $Props['MapId'] : 'map';
		$this->LeafletProps = isset($Props['LeafletProps']) && is_array($Props['LeafletProps']) ? $Props['LeafletProps'] : LeafletPhpProps::GetDefaultMapProps();

		$this->SelectedStartDate = '2017-01-01';
		$this->SelectedEndDate = '2021-03-10';
	}

	function GetDefaultLeafletProps() {
		return array(
			'center' => [38.62727, -90.34789],
			'zoom' => 12,
			'scrollWheelZoom' => false
		);
	}
	
	function SetElection($SelectedElectionId) {
		$CurrentElection = Election::GetElection($SelectedElectionId);
		$CurrentElectionData = json_decode($CurrentElection['Data']);
		$this->ElectionCandidates = Candidate::GetCandidates($CurrentElectionData->Candidates);
		$this->SelectedEndDate = $CurrentElection['Date'];
		$this->SelectedStartDate = date('Y-m-d', strtotime("-4 years", strtotime($CurrentElection['Date'])));
	}

	function SetCandidate($SelectedCandidateId) {
		if (!is_null($SelectedCandidateId) && isset($this->ElectionCandidates[$SelectedCandidateId])) {
			$this->CurrentCandidate = $this->ElectionCandidates[$SelectedCandidateId];
			$this->DonationsPerZip = GetDonationsPerZip($this->CurrentCandidate['MecId'], $this->SelectedStartDate, $this->SelectedEndDate);
		} else {
			$this->CurrentCandidate = null;
		}
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
	public function AddGeoJson($GeoJsonString, $GeoJsonProps=array(), $BindPopup=null) {
		if (!is_array($GeoJsonProps)) { throw new Exception("Invalid props for AddGeoJson"); } 
		echo "L.geoJson($GeoJsonString, ".json_encode($GeoJsonProps).").addTo($this->MapName)";
		if (!is_null($BindPopup)) { echo ".bindPopup($BindPopup)"; }
		echo ";\n";
	}

	/**
	 * Full maps
	 */

	public static function PrintBasicZipMap($ZipCodes, $SelectedZipCode=null) {
		$PrimaryMapProps = array(
			'MapName' => 'ZipMap',
			'MapId' => 'map', 
			'LeafletProps' => array(
				'center' => [38.62727, -90.24789],
				'zoom' => 12,
				'scrollWheelZoom' => true,
				'zoomControl' => false,
				'preferCanvas' => true
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

	public function PrintGeoJsonMap($Style) {
		$GeoJsonType = "ZIP";
		$GeoName = "63118";
		$GeoJson = json_decode(GetGeoJson($GeoJsonType, $GeoName));
		$GeoJsonProps = array(
			'style' => array(
				'fillColor' => $FillColor,
				'fillOpacity' => 0.8,
				'weight' => 3,
				'color' => 'white'
			), 
		);
		
		$this->AddGeoJson(
			json_encode($GeoJson), 
			$GeoJsonProps, 
			"\"".StripLinebreaks(PrintZipDonationPopup($ThisZip['ZipCode'], "$".number_format($DonationsHere)))."\""
		);
	}

	public function PrintZipCodeMap($ZipCodes, $Style) {
		foreach ($ZipCodes as $ThisZipCode) {
			$ZipGeoJson = json_decode(GetGeoJson("ZIP", $ThisZipCode));
			$this->AddGeoJson(
				json_encode($ZipGeoJson), 
				$Style
			);
		}
	}

	public function PrintWardMap() {
		$WardsGeoJson = json_decode(GetGeoJson("Ward", "All"));
		foreach ($WardsGeoJson->features as $ThisWardGeoJson) {
			$this->AddGeoJson(
				json_encode($ThisWardGeoJson),
			);
		}
	}

	public function PrintDonationsByZipMap() {
		$StlZipCodes = GetStlZipCodes();
		foreach ($this->DonationsPerZip as $ThisZip) {
			if (!in_array($ThisZip['ZipCode'], $StlZipCodes)) { continue; }

			$DonationsHere = $ThisZip['TotalFromZip'];

			// TODO: Pull into a function
			if ($DonationsHere > 50000) {
				$FillColor = '#e00016';
			} else if ($DonationsHere > 25000) {
				$FillColor = '#ff263C';
			} else if ($DonationsHere > 10000) {
				$FillColor = '#ff5C6C';
			} else if ($DonationsHere > 5000) {
				$FillColor = '#ff919c';
			} else if ($DonationsHere > 1000) {
				$FillColor = '#ffbac1';
			} else {
				$FillColor = '#ffe8ea';
			}

			$GeoJson = json_decode(GetGeoJson("ZIP", $ThisZip['ZipCode']));

			// TODO: Figure out better handling for props
			$GeoJsonProps = array(
				'style' => array(
					'fillColor' => $FillColor,
					'fillOpacity' => 0.8,
					'weight' => 3,
					'color' => 'white'
				), 
				'zoomControl' => array(
					'position' =>'bottomright'
				)
			);
			
			$this->AddGeoJson(
				json_encode($GeoJson), 
				$GeoJsonProps, 
				"\"".StripLinebreaks(PrintZipDonationPopup($ThisZip['ZipCode'], "$".number_format($DonationsHere)))."\""
			);
		}
	}

	/** 
	 * Input
	 */
	public function PrintDashboardInput() {
		$DashboardHtml = "<div id='dashboard'>
			<div class='Header'>Donor Project - Dashboard</div>
			
		</div>";
		echo $DashboardHtml;
	}

	public function GetElectionFormHtml() {
		return "<form>
			<div>
				<label for='ElectionInput'>Election</label>
				<select id='ElectionInput' name='Election'>
					<optgroup label='2021 Municipal Election'>
						<option value='1'>2021 Mayoral Race</option>
					</optgroup>
				</select>
			</div>
			".$this->CandidateSelect()."
			".$this->DateSelect()."
			<button>go</button>
		</div>";
	}

	public function CandidateSelect() {
		$CandidateOptions = array_map(function($Candidate) {
			$CandidateData = json_decode($Candidate['Data']);
			return "<option value='$Candidate[CandidateId]' ".($this->CurrentCandidate && $this->CurrentCandidate['CandidateId'] == $Candidate['CandidateId'] ? " selected='selected'" : "").">
				$CandidateData->CandidateName
			</option>";
		}, $this->ElectionCandidates);

		return "<div>
			<label for='CandidateInput'>Candidate</label>
			<select id='CandidateInput' name='CandidateId'>
				<option value='' ".($this->CurrentCandidate == null ? "selected='selected'" : "").">Select a candidate</option>
				".implode("",$CandidateOptions)."
			</select>
		</div>";
	}

	public function DateSelect() {
		return "<div>
			<label for='StartDate'>StartDate</label>
			<input type='date' id='StartDate' name='StartDate' value='$this->SelectedStartDate' />
			<br/>
			<label for='EndDate'>EndDate</label>
			<input type='date' id='EndDate' name='EndDate' value='$this->SelectedEndDate' />
		</div>";
	}

	public function PrintDonationStats() {
		if ($this->CurrentCandidate == null) { return ""; }
		return "<div id='DonationStats'>
				<div class='DonationStatsHeader'><span>ZIP Code</span><span>Total $</span></div>
				<div class='DonationStatsScrollable'>
					<div><span>
							".implode("</span></div><div><span>", array_map(function ($Row) {
								$ParsedCurrency = number_format($Row['TotalFromZip']);
								return "$Row[ZipCode]</span><span>\$$ParsedCurrency";
							}, $this->DonationsPerZip))."
					</span></div>
				</div>
		</div>";
	}

	public static function PrintZipCodeInput($SelectedZipCode) {
		echo "<div id='map-title'>
			publius
			<form>
				<label for='ZipCodeInput'>Zip Code</label>
				<input type='text' id='ZipCodeInput' name='ZipCode' value='$SelectedZipCode' />
				<button>go</button>
			</form>
		</div>";
	}




	/**
	 * HTML printing
	 */
	function PrintMapHtml() {
		echo "<div id='$this->MapId'></div>";
	}

	/**
	 * CSS and Styling Stuff
	 */
	
	function PrintDefaultCss() {
		echo "
		body { margin:0; padding:0; }
		#$this->MapId {
			position: absolute;
			top:0;
			bottom:0;
			right:0;
			left:0;
		}

		#map-title {
			position: relative;
			margin-top: 10px;
			margin-left: 50px;
			float: left;
			background: white;
			border: 2px solid rgba(0,0,0,0.2);
			padding: 6px 8px;
			font-family: Helvetica;
			font-weight: bold;
			font-size: 24px;
			z-index: 800;
		}

		#dashboard {
			position: relative;
			width: 20%;
			height: 100%;
			min-width: 200px;
			background-color: white;
			border: 2px solid rgba(0,0,0,0.2);
			z-index: 400;
			line-height: 1.6em;
		}
		
		.Header {
			color: white;
			background-color: #e00016;
			text-align: center;
			padding: 3px;
			font-size: 1.3em;
		}
		";
	}
}