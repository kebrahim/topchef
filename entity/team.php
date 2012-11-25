<?php

require_once 'commonEntity.php';
CommonEntity::requireFileIn('/../dao/', 'chefDao.php');
CommonEntity::requireFileIn('/../dao/', 'draftPickDao.php');
CommonEntity::requireFileIn('/../dao/', 'pickDao.php');
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

  public function setName($name) {
  	$this->name = $name;
  }
  
  public function getAbbreviation() {
    return $this->abbreviation;
  }
  
  public function setAbbreviation($abbreviation) {
  	$this->abbreviation = $abbreviation;
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

    echo "<h3><a id='chefs'>Chefs</a></h3>";
    echo "<table class='center' border><tr>";
    echo "<th colspan='2'>Chef</th><th>Drafted</th><th>Fantasy Points</th>
          <th>Eliminated in Week</th></tr>";
    foreach ($chefs as $chef) {
      $draftPick = DraftPickDao::getDraftPickByChefId($chef->getId());
      echo "<tr><td>" . $chef->getHeadshotImg(85, 56) . "</td>
                <td class='teamchefname";
      if (StatDao::isEliminated($chef)) {
      	echo " eliminated";
      }
      echo "'>" . $chef->getNameLink(true) . "</td>
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
      
      // elimination week
      $eliminationWeek = StatDao::getEliminationWeek($chef);
      echo "<td>" . (($eliminationWeek == null) ? "--" : $eliminationWeek) . "</td>";
      echo "</tr>";
    }
    echo "</table>";
  }
  
  /**
   * Displays all of the weekly picks made by this team.
   */
  function displayWeeklyPicks() {
  	$picks = PickDao::getPicksByTeamId($this->teamId);
  	
  	echo "<h3><a id='picks'>Weekly Picks</a></h3>";
  	echo "<table class='center' border><tr>";
  	echo "<th>Week</th><th colspan='2'>Chef</th><th>Win/Loss</th><th>Bonus Points</th></tr>";
  	$firstPick = true;
  	foreach ($picks as $pick) {
      echo "<tr style='height:32;'>";
      if ($firstPick) {
        echo "<td rowspan='2'>" . $pick->getWeek() . "</td>";
      	$firstPick = false;
      } else {
      	$firstPick = true;
      }
      if ($pick->getChef() == null) {
      	echo "<td colspan=2></td><td></td><td></td>";
      } else {
      	echo "<td>" . $pick->getChef()->getHeadshotImg(44, 28) . "</td>
      	      <td class='chefbigname'>" . $pick->getChef()->getNameLink(true) . "</td>
      	      <td>" . ($pick->getRecord() == Pick::WIN ? "Win" : "Loss") . "</td>
      	      <td>" . $pick->getPoints() . "</td>";
      }
      echo "</tr>";
  	}
  	echo "</table>";
  }
}
?>