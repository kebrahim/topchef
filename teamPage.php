<?php
  require_once 'util/sessions.php';
  SessionUtil::checkUserIsLoggedIn();
?>

<html>
<head>
<title>Top Chef Rotiss - Team Summary</title>
<link href='css/style.css' rel='stylesheet' type='text/css'>
<link rel="shortcut icon" href="images/chefhat.ico" />
</head>

<script>
//shows the team with the specified id
function showTeam(teamId) {
    // If teamid is blank, then clear the team div.
	if (teamId=="" || teamId=="0") {
		document.getElementById("teamDisplay").innerHTML="";
		return;
	}

	// Display team information.
	getRedirectHTML(document.getElementById("teamDisplay"),
	    "admin/displayTeam.php?type=display&team_id="+teamId);
}

//populates the innerHTML of the specified elementId with the HTML returned by the specified
//htmlString
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
  require_once 'dao/teamDao.php';
  require_once 'util/navigation.php';

  // Display header.
  NavigationUtil::printHeader(true, true, NavigationUtil::TEAM_SUMMARY_BUTTON);

  echo "<div class='bodycenter'>";

  // Get team from REQUEST; otherwise, use logged-in user's team.
  if (isset($_REQUEST["team_id"])) {
    $teamId = $_REQUEST["team_id"];
  } else {
    $teamId = SessionUtil::getLoggedInTeam()->getId();
  }
  $team = TeamDao::getTeamById($teamId);

  // Allow user to choose from list of teams to see corresponding summary page.
  $allTeams = TeamDao::getAllTeams();
  echo "<br/><label for='team_id'>Choose team: </label>";
  echo "<select id='team_id' name='team_id' onchange='showTeam(this.value)'>";
  foreach ($allTeams as $selectTeam) {
    echo "<option value='" . $selectTeam->getId() . "'";
    if ($selectTeam->getId() == $teamId) {
      echo " selected";
    }
    echo ">" . $selectTeam->getName() . " (" . $selectTeam->getAbbreviation() . ")</option>";
  }
  echo "</select>";
  echo "<h1>Team Summary</h1>";
  echo "<a href='#summary'>Summary</a>&nbsp&nbsp
        <a href='#chefs'>Chefs</a>&nbsp&nbsp
        <a href='#picks'>Weekly Picks</a>";
  echo "<hr/><div id='teamDisplay'></div><br/>";
?>

<script>
  // initialize teamDisplay with selected team
  showTeam(document.getElementById("team_id").value);
</script>

<?php
  echo "</div>";

  // Display footer
  NavigationUtil::printFooter();
?>

</body>
</html>
