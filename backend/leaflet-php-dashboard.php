<?php

class LeafletPhpDashboard {

	public function __construct($Props=array()){		
		$this->SelectedElectionId = isset($Props['ElectionId']) ? $Props['ElectionId'] : 1;
		$this->SelectedCandidateId = isset($Props['CandidateId']) ? $Props['CandidateId'] : null;
		$this->SelectedStartDate = isset($Props['StartDate']) ? $Props['StartDate'] : '2020-09-01';
		$this->SelectedEndDate = isset($Props['EndDate']) ? $Props['EndDate'] : '2017-09-01';		
	}

	public function PrintDashboardPaneHtml($Contents=null) {
		if (is_null($Contents)) { $Contents = $this->GetElectionFormHtml(); }
		$DashboardHtml = "<div id='dashboard'>
			<div style='margin-top:80px;'>
				<div class='Header'>Donor Project - Dashboard</div>
				$Contents
			</div>
		</div>";
		echo $DashboardHtml;
	}

	
	public function GetElectionFormHtml() {
		return "
		<form>
			".$this->GetElectionSelectHtml()."
			".$this->GetCandidateSelectHtml()."
			".$this->GetDateRangeSelectHtml()."
			<button>go</button>
		</form>
		</div>";
	}

	public function GetElectionSelectHtml() {
		$Elections = array(
			array(
				'ElectionId' => 1,
				'ElectionDate' => '2021-03-02',
				'Data' => json_encode(array(
					'ElectionName' => '2021 Mayor\'s race',
					'Candidates' => array(1, 2, 4)
				))
			)
		);

		$ElectionOptions = array_map(function($Election) {
			$ElectionData = json_decode($Election['Data']);
			return "<option value='$Election[ElectionId]' ".($this->SelectedElectionId && $this->SelectedElectionId == $Election['ElectionId'] ? " selected='selected'" : "").">
				$ElectionData->ElectionName
			</option>";
		}, $Elections);

		return "<div>
			<label for='ElectionInput'>Election</label>
			<select id='ElectionInput' name='ElectionId'>
				<option value='' ".($this->SelectedElectionId == null ? "selected='selected'" : "").">Select a candidate</option>
				".implode("",$ElectionOptions)."
			</select>
		</div>";
	}

	public function GetCandidateSelectHtml() {
		$CurrentElection = Election::GetElection($this->SelectedElectionId);
		$CurrentElectionData = json_decode($CurrentElection['Data']);
		$this->ElectionCandidates = Candidate::GetCandidates($CurrentElectionData->Candidates);
		$this->SelectedEndDate = $CurrentElection['Date'];
		$this->SelectedStartDate = date('Y-m-d', strtotime("-2 years", strtotime($CurrentElection['Date'])));
	
		$CandidateOptions = array_map(function($Candidate) {
			$CandidateData = json_decode($Candidate['Data']);
			return "<option value='$Candidate[CandidateId]' ".($this->SelectedCandidateId && $this->SelectedCandidateId == $Candidate['CandidateId'] ? " selected='selected'" : "").">
				$CandidateData->CandidateName
			</option>";
		}, $this->ElectionCandidates);

		return "<div>
			<label for='CandidateInput'>Candidate</label>
			<select id='CandidateInput' name='CandidateId'>
				<option value='' ".($this->SelectedCandidateId == null ? "selected='selected'" : "").">Select a candidate</option>
				".implode("",$CandidateOptions)."
			</select>
		</div>";
	}

	public function GetDateRangeSelectHtml() {
		return "<div>
			<label for='StartDate'>StartDate</label>
			<input type='date' id='StartDate' name='StartDate' value='$this->SelectedStartDate' />
			<br/>
			<label for='EndDate'>EndDate</label>
			<input type='date' id='EndDate' name='EndDate' value='$this->SelectedEndDate' />
		</div>";
	}
}