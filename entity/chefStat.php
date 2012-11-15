<?php
require_once 'commonEntity.php';
CommonEntity::requireFileIn('/../dao/', 'statDao.php');

/**
 * Represents a set of fantasy statistics for a single chef in a single week.
 */
class ChefStat {
  private $chefStatId;
  private $week;
  private $chefId;
  private $statId;
  private $statLoaded = false;
  private $stat;
  
  public function __construct($chefStatId, $week, $chefId, $statId) {
  	$this->chefStatId = $chefStatId;
  	$this->week = $week;
  	$this->chefId = $chefId;
  	$this->statId = $statId;
  }

  public function getId() {
  	return $this->chefStatId;
  }

  public function setId($chefStatId) {
    $this->chefStatId = $chefStatId;
  }

  public function getWeek() {
  	return $this->week;
  }

  public function getChefId() {
  	return $this->chefId;
  }

  public function getStatId() {
    return $this->statId;
  }
  
  public function getStat() {
  	if (!$this->statLoaded) {
  	  $this->stat = StatDao::getStatById($this->statId);
  	  $this->statLoaded = true;
  	}
  	return $this->stat;
  }
}
?>