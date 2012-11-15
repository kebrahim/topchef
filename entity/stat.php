<?php
require_once 'commonEntity.php';

/**
 * Represents a type of statistic for a chef.
 */
class Stat {
  private $statId;
  private $name;
  private $points;
  private $abbreviation;

  const WINNER = 'W';
  const ELIMINATED = 'E';
  
  public function __construct($statId, $name, $points, $abbreviation) {
  	$this->statId = $statId;
    $this->name = $name;
    $this->points = $points;
    $this->abbreviation = $abbreviation;
  }

  public function getId() {
  	return $this->statId;
  }

  public function getName() {
  	return $this->name;
  }

  public function getPoints() {
  	return $this->points;
  }

  public function getAbbreviation() {
    return $this->abbreviation;
  }
}
?>