<?php
  require_once '../util/sessions.php';
  SessionUtil::checkUserIsLoggedInAdmin();
?>

<html>
<head>
<title>Top Chef Rotiss - Manage Scores</title>
<link href='../css/style.css' rel='stylesheet' type='text/css'>
<link rel="shortcut icon" href="../images/chefhat.ico" />
</head>

<script>
// shows the scoring info for the specified week
function showWeek(week) {
    // If week is blank, then clear the team div.
	if (week=="" || week=="0") {
		document.getElementById("weekDisplay").innerHTML="";
		return;
	}

	// Display team information.
	getRedirectHTML(document.getElementById("weekDisplay"),
	    "displayScoringWeek.php?type=manage&week=" + week);
}

// populates the innerHTML of the specified elementId with the HTML returned by the specified
// htmlString
function getRedirectHTML(element, htmlString) {
	var xmlhttp;
	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	} else {// code for IE6, IE5
	    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			element.innerHTML=xmlhttp.responseText;
		}
	};
	xmlhttp.open("GET", htmlString, true);
	xmlhttp.send();
}
</script>

<body>

<?php
  require_once '../dao/chefDao.php';
  require_once '../dao/pickDao.php';
  require_once '../dao/statDao.php';
  require_once '../dao/teamDao.php';
  require_once '../entity/team.php';
  require_once '../util/mail.php';
  require_once '../util/navigation.php';

  /**
   * Returns true if any picks have been made [a chef has been selected] for the given week.
   */
  function anyPicksBeenMade($week) {
  	$picks = PickDao::getPicksByWeek($week);
  	$numTeams = count(TeamDao::getAllTeams());
  	if (count($picks) != ($numTeams * 2)) {
      return false;
  	}
  	$firstPick = $picks[0];
  	return ($firstPick->getChef() != null);
  }
  
  /**
   * Updates the picks corresponding to the specified pick result [W/L] and awards
   * bonus points if the specified chef is selected.
   */
  function updatePickForWeekChefResult($week, $chef, $result) {
  	$points = 5 - floor(($week - 1) / 3);
  	$picks = PickDao::getPicksByWeekResult($week, $result);
  	foreach ($picks as $pick) {
  	  $pick->setPoints(($pick->getChefId() == $chef->getId()) ? $points : 0);
      PickDao::updatePick($pick);
  	}
  }
  
  /**
   * Updates the picks corresponding to the specified abbreviation [W/L] and zeroes
   * out their bonus points.
   */
  function zeroPicksForWeekResult($week, $result) {
  	$picks = PickDao::getPicksByWeekResult($week, $result);
  	foreach ($picks as $pick) {
  	  $pick->setPoints(0);
  	  PickDao::updatePick($pick);
  	}
  }
  
  // Display header.
  NavigationUtil::printNoWidthHeader(true, false, NavigationUtil::MANAGE_SCORES_BUTTON);
  echo "<div class='bodycenter'>";

  if (isset($_REQUEST['submit'])) {
    // first, clear all stats and pick bonuses for week.
    $week = $_REQUEST["week"];
    StatDao::deleteForWeek($week);    
    PickDao::clearPointsForWeek($week);

    // for each chef/stat combo, if box is checked, create and save statline.
    $chefs = ChefDao::getAllChefs();
    $stats = StatDao::getAllStats();
    $foundWinner = false;
    $foundEliminated = false;
    foreach ($chefs as $chef) {
      foreach ($stats as $stat) {
        $checkbox = "c" . $chef->getId() . "s" . $stat->getId();
        if (isset($_REQUEST[$checkbox])) {
          $chefStat = new ChefStat(-1, $week, $chef->getId(), $stat->getId());
          StatDao::createChefStat($chefStat);
          
          // if stat is winner or eliminated, then also update picks results
          if ($stat->getAbbreviation() == Stat::WINNER) {
          	updatePickForWeekChefResult($week, $chef, Pick::WIN);
            $foundWinner = true;
          } else if ($stat->getAbbreviation() == Stat::ELIMINATED) {
          	updatePickForWeekChefResult($week, $chef, Pick::LOSS);
          	$foundEliminated = true;
          }
        }
      }
    }
    
    // if stats do not contain any individual winners or eliminations, then zero out the
    // corresponding picks.
    if (!$foundWinner) {
      zeroPicksForWeekResult($week, Pick::WIN);
    }
    if (!$foundEliminated) {
      zeroPicksForWeekResult($week, Pick::LOSS);
    }
    
    // delete next week's weekly picks if none have been made yet, then insert them in reverse
    // order of standings.
    $nextWeek = $week + 1;
    if (!anyPicksBeenMade($nextWeek)) {
      PickDao::deleteForWeek($nextWeek);
      
      $teams = TeamDao::getAllTeams();
      // Sort teams by total points, ascending
      $pointsToTeam = array();
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
      asort($teamToPoints);
      
      $pickNumber = 1;
      foreach ($teamToPoints as $teamId => $points) {
      	// create first round of picks
      	$pick = new Pick(-1, $nextWeek, $pickNumber, $teamId, null, null, null);
      	PickDao::createPick($pick);
      	
      	// create reverse round of picks
      	$totalPicks = count($teams) * 2;
      	$reversePick = new Pick(-1, $nextWeek, (($totalPicks + 1) - $pickNumber), $teamId, 
      	    null, null, null);
      	PickDao::createPick($reversePick);
      	$pickNumber++;
      }
    }
    echo "<div class='alert_msg'>Scoring updated!</div>";
    MailUtil::sendScoreUpdateEmail($week);
  } else if (isset($_REQUEST["week"])) {
    $week = $_REQUEST["week"];
  } else {
    $week = StatDao::getMaxWeek() + 1;
  }

  // Allow user to choose from list of weeks to see corresponding scoring breakdown.
  $maxWeek = StatDao::getMaxWeek() + 1;
  echo "<FORM ACTION='manageScores.php' METHOD=POST>";
  echo "<br/><label for='week'>Choose week: </label>";
  echo "<select id='week' name='week' onchange='showWeek(this.value)'>
          <option value='0'></option>";
  for ($wk = 1; $wk <= $maxWeek; $wk++) {
    echo "<option value='" . $wk . "'";
    if ($wk == $week) {
      echo " selected";
    }
    echo ">Week $wk</option>";
  }
  echo "</select><br/>";
  echo "<div id='weekDisplay'></div><br/>";
?>

<script>
  // initialize weekDisplay with selected week
  showWeek(document.getElementById("week").value);
</script>

<?php
  echo "</form></div>";

  // Footer
  NavigationUtil::printFooter();
?>

</body>
</html>
