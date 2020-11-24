<?php

class Sherlook {

	private $SelectedElectionId;
	private $SelectedCandidateId;
	private $SelectedCandidateMecId; // TODO: Candidates can 
	
	public function __construct($Props=array()){		
		$this->SelectedElectionId = isset($Props['ElectionId']) ? $Props['ElectionId'] : 1;
		$this->SelectedCandidateId = isset($Props['CandidateId']) ? $Props['CandidateId'] : null;
		$this->SelectedStartDate = isset($Props['StartDate']) ? $Props['StartDate'] : '2017-01-01';
		$this->SelectedEndDate = isset($Props['EndDate']) ? $Props['EndDate'] : '2021-03-10';		
	}

	public function SelectElection($ElectionId) {
		$this->SelectedElectionId = $ElectionId;
	}

	public function SelectCandidate($CandidateId) {
		$this->SelectedCandidateId = $CandidateId;
		$this->SelectedCandidateMecId = $this->GetCandidateMecId();
	}

	public function GetCandidateMecId() {
		if (!$this->SelectedCandidateId) { return null; }
		$Query = 'SELECT MecId 
			FROM candidate 
			WHERE CandidateId = ?';

		global $dbConnection;
		$stmt = $dbConnection->prepare($Query);
		$stmt->bind_param('i', $this->SelectedCandidateId);
		$stmt->execute();
		$result = $stmt->get_result(); 
		return $result->fetch_object()->MecId;
	}

	private function GetContributionsInDateRange() {

		$Query = 'SELECT * 
			FROM donation 
			WHERE MecId = ? AND ContributionDate > ? AND ContributionDate < ?';

		global $dbConnection;
		$stmt = $dbConnection->prepare($Query);
		$stmt->bind_param('sss', $this->GetCandidateMecId(), $this->SelectedStartDate, $this->SelectedEndDate);
		$stmt->execute();
		
		$result = $stmt->get_result();
		$ReturnArray = array();
		while ($row = $result->fetch_assoc()) {
			$ReturnArray[$row['DonationId']] = $row;
		}
		return $ReturnArray;
	}

	/**
	 * Retur
	 * @return Donation[] $DonationObjectArray
	 */
	public function GetCommitteeDonations() {
		$Query = 'SELECT FromCommittee, City, State, Zip, SUM(Amount) AS TotalFromCommittee
			FROM donation 
			WHERE MecId = ? AND Date > ? AND Date < ? AND FromCommittee IS NOT NULL AND ContributionType = "M"
			GROUP BY FromCommittee
			ORDER BY TotalFromCommittee DESC';

		$MecId =  $this->GetCandidateMecId();

		global $dbConnection;
		$stmt = $dbConnection->prepare($Query);
		$stmt->bind_param('sss', $MecId, $this->SelectedStartDate, $this->SelectedEndDate);
		$stmt->execute();
		
		$result = $stmt->get_result();
		$DonationObjectArray = array();
		while ($row = $result->fetch_object()) {
			$DonationObjectArray[] = $row;
		}
		return $DonationObjectArray;
	}
	
	public function GetAggregateIndividualDonations() {
		$Query = 'SELECT DonationId, Company, FirstName, LastName, City, State, Zip, Amount
			FROM donation 
			WHERE MecId = ? AND Date > ? AND Date < ? AND FromCommittee IS NULL AND ContributionType = "M" ';

		$MecId =  $this->GetCandidateMecId();

		global $dbConnection;
		$stmt = $dbConnection->prepare($Query);
		$stmt->bind_param('sss', $MecId, $this->SelectedStartDate, $this->SelectedEndDate);
		$stmt->execute();
		
		$result = $stmt->get_result();
		$ReturnArray = array();
		while ($row = $result->fetch_object()) {
			if (!empty($row->Company)) {
				$Key = $row->Company;
			} else if ($row->FirstName) {
				$Key = $row->FirstName."_".$row->LastName."_".$row->Zip;
			} else {
				$Key = "Donation_".$row->DonationId;
			}
			if (!isset($ReturnArray[$Key])) {
				$ReturnArray[$Key] = array("Total" => 0, "Occupations" => array());
			}
			$ReturnArray[$Key]["Total"] = $ReturnArray[$Key]["Total"] + $row->Amount;
			if (!empty($row->Occupation) && !in_array($row->Occupation, $ReturnArray[$Key]["Occupations"])) {
				$ReturnArray[$Key]["Occupations"][] = $row->Occupation;
			}
		}
		return $ReturnArray;
	}
}

?>