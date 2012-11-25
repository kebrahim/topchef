<?php
  require_once 'util/sessions.php';
  SessionUtil::checkUserIsLoggedIn();
?>

<html>
<head>
<title>Top Chef Rotiss - Scoreboard</title>
<link href='css/style.css' rel='stylesheet' type='text/css'>
<link rel="shortcut icon" href="images/chefhat.ico" />
</head>

<body>

<?php
  require_once 'dao/chefDao.php';
  require_once 'dao/pickDao.php';
  require_once 'dao/statDao.php';
  require_once 'dao/teamDao.php';
  require_once 'util/navigation.php';
  
  /**
   * Displays the winning/losing chef pick row for the specified team for all of the weeks.
   */
  function displayPickRow($teamId, $pickResult, $maxWeek) {
  	echo "<tr><td colspan='2'><strong>" . (($pickResult == Pick::WIN) ? "Winning" : "Losing") . 
  	    " Chef Bonus</strong></td>";
  	$picks = PickDao::getPicksByTeamResult($teamId, $pickResult);
  	$pickPoints = 0;
  	for ($wk=1; $wk<=$maxWeek; $wk++) {
  	  if (count($picks) < $wk) {
  	  	echo "<td colspan='2'></td>";
  	  	break;
  	  }
  	  $chef = $picks[$wk - 1]->getChef();
  	  $points = $picks[$wk - 1]->getPoints();
  	  $correct = ($points > 0) && ($wk > 1);
  	  echo "<td";
  	  if ($correct) {
  	  	echo " class='winner'";
  	  }
  	  echo "><a href='chefPage.php?chef_id=" . $chef->getId() . "'>" . $chef->getFirstName()
  	         . "</a></td>
  		    <td";
  	  if ($correct) {
  	  	echo " class='winner'";
  	  }
      echo ">" . $points . "</td>";
  	  $pickPoints += $points;
  	}
  	echo "<td><strong>" . $pickPoints . "</strong></td>";
  	echo "</tr>";
  	return $pickPoints;
  }

  /**
   * displays a list of fantasy points for all weeks, broken down by chef, for the specified team,
   * including the total number of points scored by this team.
   */
  function displayTeamScores($teamId, $totalPoints) {
    $team = TeamDao::getTeamById($teamId);
    echo "<h3>" . $team->getNameLink(true) . " ( " . ($totalPoints != null ? $totalPoints : 0) .
       " )</h3>";
    echo "<table class='smallfonttable center' border>
                <tr><th colspan='2'>Chef</th>";
    $maxWeek = StatDao::getMaxWeek();
    for ($i=1; $i<=$maxWeek; $i++) {
      echo "<th colspan='2' class='weekheader'>Week $i</th>";
    }
    echo "<th>Total Points</th></tr>";

    $chefs = ChefDao::getChefsByTeam($team);
    $teamPoints = 0;
    foreach ($chefs as $chef) {
      echo "<tr><td>" . $chef->getHeadshotImg(44, 28) . "</td>
                <td class='chefname";
      if (StatDao::isEliminated($chef)) {
      	echo " eliminated";
      }
      echo "'>" . $chef->getNameLink(true) . "</td>";

      // weekly points
      $totalPoints = 0;
      for($wk=1; $wk<=$maxWeek; $wk++) {
        $statLine = StatDao::getStatLineForChefWeek($chef, $wk);
        // icons
        echo "<td class='weekscorefirst";
        if (($statLine != null) && $statLine->isWinner()) {
          echo " winner";
        } else if (($statLine != null) && $statLine->isEliminated()) {
          echo " eliminated";
        }
        echo "'>";
        if ($statLine != null) {
          $firstStat = true;
          foreach ($statLine->getStats() as $stat) {
          	if (!$firstStat) {
          	  echo ",";
          	} else {
          	  $firstStat = false;
          	}
            echo $stat->getAbbreviation();
          }
        }
        echo "</td>";

        // points
        echo "<td class='weekscore";
        if (($statLine != null) && $statLine->isWinner()) {
          echo " winner";
        } else if (($statLine != null) && $statLine->isEliminated()) {
          echo " eliminated";
        }
        echo "'>";
        if ($statLine != null) {
          $weeklyPoints = 0;
          foreach ($statLine->getStats() as $stat) {
            $weeklyPoints += $stat->getPoints();
          }
          echo $weeklyPoints;
          $totalPoints += $weeklyPoints;
        } else {
          echo "0";
        }
        echo "</td>";
      }

      // total points
      echo "<td><strong>" . $totalPoints . "</strong</td>";
      $teamPoints += $totalPoints;
      echo "</tr>";
    }

    // winner/loser predictions
    $teamPoints += displayPickRow($teamId, Pick::WIN, $maxWeek);
    $teamPoints += displayPickRow($teamId, Pick::LOSS, $maxWeek);

    // total team points, including weekly breakdown
    echo "<tr><td colspan='2'><strong>Total</strong></td>";
    for($wk=1; $wk<=$maxWeek; $wk++) {
      echo "<td colspan='2'><strong>";
      $weeklyStatPoints = StatDao::getWeeklyPointsByTeam($team, $wk);
      if ($weeklyStatPoints == null) {
      	$weeklyStatPoints = 0;
      }
      $weeklyPickPoints = PickDao::getWeeklyPointsByTeam($team, $wk);
      if ($weeklyPickPoints == null) {
      	$weeklyPickPoints = 0;
      }
      echo ($weeklyStatPoints + $weeklyPickPoints) . "</td>";
    }
    echo "<td><strong>$teamPoints<strong></td></tr>";
    echo "</table><br/>";
  }
  
  /**
   * Returns true if it's currently the logged-in user's turn to make their weekly pick.
   */
  function isMyTurnToPick() {
  	$maxPickWeek = PickDao::getMaxWeek();
  	$picks = PickDao::getPicksByWeek($maxPickWeek);
  	foreach ($picks as $pick) {
  	  if ($pick->getChef() == null) {
  	  	return ($pick->getTeamId() == SessionUtil::getLoggedInTeam()->getId());
  	  }
  	}
  	return false;
  }

  // Display header.
  NavigationUtil::printHeader(true, true, NavigationUtil::SCOREBOARD_BUTTON);
  echo "<div class='bodycenter'>";
  
  // if it's my turn to make my weekly pick, show alert!
  if (isMyTurnToPick()) {
  	echo "<div class='alert_msg'>Hey! It's YOUR TURN to make your <a href='picksPage.php'>weekly pick</a>!</div>";
  }
  
  echo "<h1>Scoreboard</h1>";
  $teams = TeamDao::getAllTeams();

  // Sort teams by total points [including chef stats & weekly picks] in descending order
  $teamToPoints = array();
  foreach ($teams as $team) {
    $statPoints = StatDao::getTotalPointsByTeam($team);
    if ($statPoints == null) {
      $statPoints = 0;
    }
    $pickPoints = PickDao::getTotalPointsByTeam($team);
    if ($pickPoints == null) {
      $pickPoints = 0;
    }
    $teamToPoints[$team->getId()] = $statPoints + $pickPoints;
  }
  arsort($teamToPoints);

  // display overall team scores
  echo "<h2>Overall Scores</h2>";
  echo "<table border class='center'><tr><th>Rank</th><th>Team</th><th>Points</th></tr>";
  $rank = 0;
  $lastScore = 1000;
  foreach ($teamToPoints as $teamId => $points) {
  	if ($points < $lastScore) {
  	  $rank++;
  	  $lastScore = $points;
  	}
  	echo "<tr";
  	if ($teamId == SessionUtil::getLoggedInTeam()->getId()) {
  	  echo " class='selected_team_row'";
  	}
  	echo "><td>" . $rank . "</td>
  	          <td>" . TeamDao::getTeamById($teamId)->getNameLink(true) . "</td>
  	          <td>" . $points . "</td></tr>";
  }
  echo "</table>";
  
  // display individual team scores
  echo "<h2>Scoring Breakdown</h2>";
  foreach ($teamToPoints as $teamId => $points) {
    displayTeamScores($teamId, $points);
  }
  
  // legend
  echo "<h3>Legend</h3>";
  $stats = StatDao::getAllStats();
  echo "<table border class='center'>
          <tr><th>Code</th><th>Scoring Metric</th><th>Points</th></tr>";
  foreach ($stats as $stat) {
    echo "<tr><td>" . $stat->getAbbreviation() . "</td>
              <td>" . $stat->getName() . "</td>
              <td>" . $stat->getPoints() . "</td
          </tr>";
  }
  echo "</table>";
  echo "</div>";

  // Display footer
  NavigationUtil::printFooter();
?>

</body>
</html>
