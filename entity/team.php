<?php

require_once 'commonEntity.php';
CommonEntity::requireFileIn('/../dao/', 'userDao.php');

/**
 * Represents a top chef fantasy team.
 */
class Team {
  private $teamId;
  private $name;
  private $abbreviation;
  private $owners;
  private $ownersLoaded;

  public function __construct($teamId, $name, $abbreviation) {
    $this->teamId = $teamId;
    $this->name = $name;
    $this->abbreviation = $abbreviation;
    $this->ownersLoaded = false;
  }

  public function getId() {
    return $this->teamId;
  }

  public function getName() {
    return $this->name;
  }

  public function getAbbreviation() {
    return $this->abbreviation;
  }

  public function getOwners() {
    if ($this->ownersLoaded != true) {
      $this->owners = UserDao::getUsersByTeamId($this->teamId);
      $this->ownersLoaded = true;
    }
    return $this->owners;
  }

  public function getOwnersString() {
    $first_owner = 1;
    $ownerString = '';
    foreach ($this->getOwners() as $owner) {
      if ($first_owner == 0) {
        $ownerString .= ', ';
      } else {
        $first_owner = 0;
      }
      $ownerString .= $owner->getFullName();
    }
    return $ownerString;
  }

  /**
   * Displays all of the chefs currently belonging to this team.
   */
  function displayChefs() {
    // TODO
  }
}
?>