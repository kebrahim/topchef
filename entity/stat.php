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
  private $shortName;
  private $ordinal;
  
  const WINNER = 'W';
  const ELIMINATED = 'E';
  
  public function __construct($statId, $name, $points, $abbreviation, $shortName, $ordinal) {
  	$this->statId = $statId;
    $this->name = $name;
    $this->points = $points;
    $this->abbreviation = $abbreviation;
    $this->shortName = $shortName;
    $this->ordinal = $ordinal;
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
  
  public function getShortName() {
  	return $this->shortName;
  }
  
  public function getOrdinal() {
  	return $this->ordinal;
  }
}
?>