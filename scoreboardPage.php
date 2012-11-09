<?php
  require_once 'util/sessions.php';
  SessionUtil::checkUserIsLoggedIn();
?>

<html>
<head>
<title>Rotiss.com - Scoreboard</title>
<link href='css/style.css' rel='stylesheet' type='text/css'>
</head>

<body>

<?php
  require_once 'dao/chefDao.php';
  require_once 'dao/statDao.php';
  require_once 'dao/teamDao.php';
  require_once 'util/navigation.php';

  // Display header.
  NavigationUtil::printHeader(true, true, NavigationUtil::SCOREBOARD_BUTTON);
  echo "<div class='bodyleft'>";
  echo "<h1>Scoreboard</h1>";
  $teams = TeamDao::getAllTeams();
  foreach ($teams as $team) {
    // TODO sort by total points
    $dbPoints = StatDao::getTotalPointsByTeam($team);
    echo "<h3>" . $team->getNameLink(true) . " ( " . ($dbPoints != null ? $dbPoints : 0) ." )</h3>";
    echo "<table class='smallfonttable internalcenter' border>
            <tr><th></th><th>Chef</th>";
    $maxWeek = StatDao::getMaxWeek();
    for ($i=1; $i<=$maxWeek; $i++) {
      echo "<th colspan='2' class='weekheader'>Week $i</th>";
    }
    echo "<th>Total Points</th></tr>";

    $chefs = ChefDao::getChefsByTeam($team);
    $teamPoints = 0;
    foreach ($chefs as $chef) {
      echo "<tr><td>" . $chef->getHeadshotImg(44, 28) . "</td>
                <td class='chefname'>" . $chef->getNameLink(true) . "</td>";

      // weekly points
      $totalPoints = 0;
      for($wk=1; $wk<=$maxWeek; $wk++) {
        $statLine = StatDao::getStatLineForChefWeek($chef, $wk);
        // icons
        echo "<td class='weekscore";
        if (($statLine != null) && $statLine->isWinner()) {
          echo " winner";
        } else if (($statLine != null) && $statLine->isEliminated()) {
          echo " eliminated";
        }
        echo "'>";
        if ($statLine != null) {
          foreach ($statLine->getStats() as $stat) {
            // TODO icon?
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

    // TODO winner/loser predictions

    // total team points
    $cols = 2 + ($maxWeek * 2);
    echo "<tr><td colspan='" . $cols . "'><strong>Total</strong></td>
              <td><strong>$teamPoints<strong></td></tr>";
    echo "</table>";
  }

  // legend
  echo "<h3>Legend</h3>";
  $stats = StatDao::getAllStats();
  echo "<table class='internalcenter'>
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
