<?php

require_once 'commonEntity.php';
CommonEntity::requireFileIn('/../dao/', 'teamDao.php');
CommonEntity::requireFileIn('/../dao/', 'chefDao.php');

/**
 * Draft pick
 */
class DraftPick {
  private $draftPickId;
  private $teamId;
  private $teamLoaded;
  private $team;
  private $round;
  private $pick;
  private $chefId;
  private $chefLoaded;
  private $chef;

  public function __construct($draftPickId, $round, $pick, $teamId, $chefId) {
    $this->draftPickId = $draftPickId;
    $this->round = $round;
    $this->pick = $pick;
    $this->teamId = $teamId;
    $this->chefId = $chefId;
    $this->teamLoaded = false;
    $this->chefLoaded = false;
  }

  public function getId() {
    return $this->draftPickId;
  }

  public function getTeam() {
    if ($this->teamLoaded != true) {
      $this->team = TeamDao::getTeamById($this->teamId);
      $this->teamLoaded = true;
    }
    return $this->team;
  }

  public function setTeam(Team $team) {
    $this->team = $team;
    $this->teamId = $team->getId();
    $this->teamLoaded = true;
  }

  public function getRound() {
    return $this->round;
  }

  public function getPick() {
    return $this->pick;
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

  public function setChef(Chef $chef) {
    $this->chef = $chef;
    $this->chefId = $chef->getId();
    $this->chefLoaded = true;
  }

  public function getChefId() {
    if ($this->chefId == null) {
      return "null";
    }
    return $this->geChef()->getId();
  }

  public function getChefName() {
    if ($this->chefId == null) {
      return "--";
    }
    return $this->getChef()->getFullName();
  }
}
?>