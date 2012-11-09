<?php
require_once 'commonEntity.php';
CommonEntity::requireFileIn('/../dao/', 'chefDao.php');
CommonEntity::requireFileIn('/../entity/', 'stat.php');

/**
 * Represents a set of fantasy statistics for a single chef in a single week.
 */
class StatLine {
  private $statLineId;
  private $week;
  private $chefId;
  private $chef;
  private $chefLoaded = false;
  private $stats;

  public function __construct($statLineId, $week, $chefId, $stats) {
  	$this->statLineId = $statLineId;
  	$this->week = $week;
  	$this->chefId = $chefId;
  	$this->stats = $stats;
  }

  public function getId() {
  	return $this->statLineId;
  }

  public function getWeek() {
  	return $this->week;
  }

  public function getChef() {
  	if ($this->chefLoaded != true) {
  		$this->chef = ChefDao::getChefById($this->chefId);
  		$this->chefLoaded = true;
  	}
  	return $this->chef;
  }

  public function getChefId() {
  	return $this->chefId;
  }

  public function getStats() {
  	return $this->stats;
  }

  public function setStats($stats) {
    $this->stats = $stats;
  }

  public function isWinner() {
    foreach ($this->stats as $stat) {
      if ($stat->getAbbreviation() == "W") {
        return true;
      }
    }
    return false;
  }

  public function isEliminated() {
    foreach ($this->stats as $stat) {
      if ($stat->getAbbreviation() == "E") {
        return true;
      }
    }
    return false;
  }
}
?>