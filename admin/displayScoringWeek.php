<?php
  require_once '../dao/chefDao.php';
  require_once '../dao/pickDao.php';
  require_once '../dao/statDao.php';
  require_once '../util/sessions.php';

  /**
   * Display scoring info for specified week
   */
  function displayScoringWeekForManagement($week) {
    echo "<h1>Scoring for Week $week</h1>";

    echo "<table border class='center smallfonttable'>
            <tr><th>Chef</th>";
    $stats = StatDao::getAllStats();
    foreach ($stats as $stat) {
      echo "<th>" . $stat->getShortName() . "</th>";
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
  
  /**
   * Display weekly pick information for specified week
   */
  function displayWeekForPicks($week) {
  	echo "<h2>Picks for Week $week</h2>";
  	$picks = PickDao::getPicksByWeek($week);
  	echo "<form action='../picksPage.php' method='post'>";
  	echo "<table border class='center'>
  	        <tr><th>Pick</th><th>Team</th><th colspan='2'>Chef</th><th>Win/Loss</th>
  	            <th>Bonus Points</th></tr>";
  	$nextToPick = false;
  	$myTurnToPick = false;
  	foreach ($picks as $pick) {
  	  echo "<tr";
  	  if (($pick->getChef() != null) && $pick->getPoints() > 0 && $pick->getWeek() > 1) {
  	  	echo " class='winner'";
  	  }
      echo " style='height:46;'><td>" . $pick->getPickNumber() . "</td>
                <td>" . $pick->getTeam()->getNameLink(true) . "</td>";
      if ($pick->getChef() != null) {
      	echo "<td>" . $pick->getChef()->getHeadshotImg(66, 42) . "</td>
      	      <td class='chefbigname'>" . $pick->getChef()->getNameLink(true) . "</td>
      	      <td>" . ($pick->getRecord() == Pick::WIN ? "Win" : "Loss") . "</td>
      	      <td>" . $pick->getPoints() . "</td>";
      } else if ($nextToPick == false) {
      	if ($pick->getTeam()->getId() == SessionUtil::getLoggedInTeam()->getId()) {
          // if it's my turn to pick, show drop-downs
          $myTurnToPick = true;
      	  // TODO only show chefs who haven't been picked this week
      	  $chefs = ChefDao::getAllChefs();
          echo "<td colspan='2'><select name='pick_chef_id'>
                      <option value='0'>-- Choose Chef --</option>";
          foreach ($chefs as $chef) {
            echo "<option value='" . $chef->getId() . "'>" . $chef->getFullName() . "</option>";
          }
          echo "</select></td>";
        
          // dropdown for W/L
          echo "<td><select name='pick_record'>
                      <option value='0'>-- Choose W/L --</option>
                      <option value='" . Pick::WIN . "'>Win</option>
                      <option value='". Pick::LOSS . "'>Loss</option>
                    </select></td>";
        
          // points is blank
          echo "<td></td>";
          
          // save pick_id
          echo "<input type='hidden' name='pick_id' value='" . $pick->getId() . "'>";
      	} else {
      	  echo "<td colspan='2'></td><td></td><td></td>";
      	}
      	$nextToPick = true;
      } else {
      	echo "<td colspan='2'></td><td></td><td></td>";
      }
      echo "</tr>";
  	}
  	echo "</table>";
  	
  	if ($myTurnToPick) {
      echo "<input type='hidden' name='week' value='$week'>";
  	  echo "<br/><input type='submit' name='submit' value='Submit Pick'>";
  	}
  	echo "</form>";
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
  } else if ($displayType == "picks") {
  	displayWeekForPicks($week);
  }
?>