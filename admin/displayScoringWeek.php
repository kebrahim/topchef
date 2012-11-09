<?php
  require_once '../dao/chefDao.php';
  require_once '../dao/statDao.php';
  require_once '../util/sessions.php';

  /**
   * Display scoring info for specified week
   */
  function displayScoringWeekForManagement($week) {
    echo "<h1>Scoring for Week $week</h1>";

    echo "<table border class='center'>
            <tr><th>Chef</th>";
    $stats = StatDao::getAllStats();
    foreach ($stats as $stat) {
      echo "<th>" . $stat->getName() . "</th>";
    }
    echo "</tr>";

    $chefs = ChefDao::getAllChefs();
    foreach ($chefs as $chef) {
      echo "<tr><td>" . $chef->getNameLink(false) . "</td>";
      $statLine = StatDao::getStatLineForChefWeek($chef, $week);
      foreach ($stats as $stat) {
        echo "<td><input type='checkbox' name='c" . $chef->getId() . "s" . $stat->getId() . "'";
        if (($statLine != null) && $statLine->hasStat($stat)) {
          echo " checked='true'";
        }
        echo "></td>";
      }
      echo "</tr>";
    }
    echo "</table>";

    // buttons
    echo "<br/><input type='submit' value='Submit changes' name='submit'>
               <input type='hidden' value='$week' name='week'>";
  }

  // direct to corresponding function, depending on type of display
  if (isset($_REQUEST["type"])) {
  	$displayType = $_REQUEST["type"];
  } else {
  	die("<h1>Invalid display type for week</h1>");
  }
  if (isset($_REQUEST["week"])) {
    $week = $_REQUEST["week"];
  } else {
    die("<h1>Invalid week param for week</h1>");
  }

  if ($displayType == "manage") {
    displayScoringWeekForManagement($week);
  }
?>