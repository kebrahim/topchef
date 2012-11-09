<?php
  require_once '../util/sessions.php';
  SessionUtil::checkUserIsLoggedInAdmin();
?>

<html>
<head>
<title>Rotiss.com - Manage Scores</title>
<link href='../css/style.css' rel='stylesheet' type='text/css'>
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
  require_once '../dao/statDao.php';
  require_once '../dao/teamDao.php';
  require_once '../entity/team.php';
  require_once '../util/navigation.php';

  // Display header.
  NavigationUtil::printNoWidthHeader(true, false, NavigationUtil::MANAGE_SCORES_BUTTON);
  echo "<div class='bodycenter'>";

  if (isset($_REQUEST['submit'])) {
    // first, clear all stats for week.
    $week = $_REQUEST["week"];
    StatDao::deleteForWeek($week);

    // for each chef/stat combo, if box is checked, create and save statline.
    $chefs = ChefDao::getAllChefs();
    $stats = StatDao::getAllStats();
    foreach ($chefs as $chef) {
      foreach ($stats as $stat) {
        $checkbox = "c" . $chef->getId() . "s" . $stat->getId();
        if (isset($_REQUEST[$checkbox])) {
          $chefStat = new ChefStat(-1, $week, $chef->getId(), $stat->getId());
          StatDao::createChefStat($chefStat);

          // TODO if stat is winner, then also update picks results
        }
      }
    }
    echo "<div class='alert_msg'>Scoring updated!</div>";
  } else if (isset($_REQUEST["week"])) {
    $week = $_REQUEST["week"];
  } else {
    $week = 0;
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
