<?php

require_once 'commonEntity.php';
CommonEntity::requireFileIn('/../dao/', 'teamDao.php');

/**
 * Represents a chef-testant.
 */
class Chef {
  private $chefId;
  private $firstName;
  private $lastName;
  private $statLines;

  private static $HEADSHOT_URL = "http://www.bravotv.com/media/imagecache/170x145-headshot/images/person/head/2012/";
  private static $BODY_URL =     "http://www.bravotv.com/media/imagecache/400x600/images/person/body/2012/";

  /**
   * Sets the specified fields loads the positions from the database.
   */
  public function __construct($chefId, $firstName, $lastName) {
    $this->chefId = $chefId;
    $this->firstName = $firstName;
    $this->lastName = $lastName;
    $this->statLines = array();
  }

  public function getId() {
    return $this->chefId;
  }

  public function setId($chefId) {
    $this->chefId = $chefId;
  }

  public function getFirstName() {
    return $this->firstName;
  }

  public function getLastName() {
    return $this->lastName;
  }

  public function getFullName() {
    return $this->firstName . " " . $this->lastName;
  }

  public function getNameLink($isTopLevel) {
  	return "<a href='" . ($isTopLevel ? "" : "../") . "chefPage.php?chef_id=" .
    	$this->getId() . "'>" . $this->getFullName() . "</a>";
  }

  public function getHeadshotUrl() {
    return Chef::$HEADSHOT_URL . strtolower($this->firstName) . "-" . strtolower($this->lastName) .
        "-head.png";
  }

  public function getHeadshotImg($width, $height) {
  	return "<img src='" . $this->getHeadshotUrl() . "' width=$width height=$height />";
  }

  public function getBodyUrl() {
    return Chef::$BODY_URL . strtolower($this->firstName) . "-" . strtolower($this->lastName) .
        "-full.png";
  }

  public function getBodyImg($width, $height) {
    return "<img src='" . $this->getBodyUrl() . "' width=$width height=$height />";
  }

  public function getFantasyTeam() {
    return TeamDao::getTeamByChef($this);
  }

  public function getStatLine($week) {
  	if (isset($this->statLines[$week])) {
  	  return $this->statLines[$week];
  	}
  	return null;
  }

  public function setStatLine($week, $statLine) {
  	$this->statLines[$week] = $statLine;
  }
}
?>