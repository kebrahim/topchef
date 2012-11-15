<?php

require_once 'commonEntity.php';
CommonEntity::requireFileIn('/../dao/', 'chefDao.php');
CommonEntity::requireFileIn('/../dao/', 'teamDao.php');

/**
 * Represents a weekly Pick.
 */
class Pick {
  private $pickId;
  private $week;
  private $pickNumber;
  private $teamId;
  private $teamLoaded;
  private $team;
  private $chefId;
  private $chefLoaded;
  private $chef;
  private $record;
  private $points;
  
  const WIN = 'W';
  const LOSS = 'L';
  
  public function __construct($pickId, $week, $pickNumber, $teamId, $chefId, $record, $points) {
    $this->pickId = $pickId;
    $this->week = $week;
    $this->pickNumber = $pickNumber;
  	$this->teamId = $teamId;
    $this->teamLoaded = false;
    $this->chefId = $chefId;
    $this->chefLoaded = false;
    $this->record = $record;
    $this->points = $points;    
  }

  public function getId() {
    return $this->pickId;
  }

  public function setId($pickId) {
  	return $this->pickId = $pickId;
  }
  
  public function getWeek() {
  	return $this->week;
  }
  
  public function getPickNumber() {
  	return $this->pickNumber;
  }
  
  public function getTeam() {
    if ($this->teamLoaded != true) {
      $this->team = TeamDao::getTeamById($this->teamId);
      $this->teamLoaded = true;
    }
    return $this->team;
  }
  
  public function getTeamId() {
  	return $this->teamId;
  }
  
  public function getChef() {
  	if ($this->chefId == null) {
  	  return null;
  	}
  	if ($this->chefLoaded != true) {
  	  $this->chef = ChefDao::getChefById($this->chefId);
  	  $this->chefLoaded = true;
  	}
  	return $this->chef;
  }
  
  public function getChefId() {
  	return $this->chefId;
  }
  
  public function setChef(Chef $chef) {
  	$this->chef = $chef;
  	$this->chefLoaded = true;
  	$this->chefId = $chef->getId();
  }
  
  public function getRecord() {
  	return $this->record;
  }
  
  public function setRecord($record) {
  	$this->record = $record;
  }
  
  public function getPoints() {
  	return $this->points;
  }
  
  public function setPoints($points) {
  	return $this->points = $points;
  }
  
  public function toString() {
  	return "Wk: " . $this->week . ", Pk: " . $this->pickNumber . ", Team: " . $this->teamId . 
  	    ", Chef: " . $this->chefId . ", Record: " . $this->record . ", Pts: " . $this->points;
  }
}
?>