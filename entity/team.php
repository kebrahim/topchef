<?php

require_once 'commonEntity.php';
CommonEntity::requireFileIn('/../dao/', 'chefDao.php');
CommonEntity::requireFileIn('/../dao/', 'draftPickDao.php');
CommonEntity::requireFileIn('/../dao/', 'statDao.php');
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

  public function getNameLink($isTopLevel) {
    return "<a href='" . ($isTopLevel ? "" : "../") . "teamPage.php?team_id=" .
        $this->teamId . "'>" . $this->name . " (" . $this->abbreviation . ")</a>";
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
    $chefs = ChefDao::getChefsByTeam($this);
    if (count($chefs) == 0) {
      return;
    }

    echo "<h2>Chefs</h2>";
    echo "<table class='left' border><tr>";
    echo "<th colspan='2'>Chef</th><th>Drafted</th><th>Fantasy Points</th></tr>";
    foreach ($chefs as $chef) {
      $draftPick = DraftPickDao::getDraftPickByChefId($chef->getId());
      echo "<tr><td>" . $chef->getHeadshotImg(85, 56) . "</td>
                <td class='teamchefname'>" . $chef->getNameLink(true) . "</td>
                <td class='chefdraft'>";
      // draft pick
      if ($draftPick != null) {
        echo "Rd: " . $draftPick->getRound() . ", Pk: " . $draftPick->getPick();
      } else {
        echo "--";
      }
      echo "</td>";

      // total fantasy points
      $points = StatDao::getTotalPointsByChef($chef);
      echo "<td>" . ($points == null ? "0" : $points) . "</td>";
      echo "</tr>";
    }
    echo "</table>";
  }
}
?>