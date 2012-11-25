<?php
  require_once '../dao/teamDao.php';
  require_once '../util/sessions.php';

  /**
   * Returns a Team based on the ID specified in the GET/POST.
   */
  function getTeamByParam($param) {
  	if (isset($_REQUEST[$param])) {
      $teamId = $_REQUEST[$param];
  	} else {
  	  $teamId = 0;
  	}
    $team = TeamDao::getTeamById($teamId);
    if ($team == null) {
      die("<h1>team id " . $teamId . " does not exist for param " . $param . "!</h1>");
    }
    return $team;
  }

  /**
   * Display specified team on team summary page.
   */
  function displayTeam(Team $team) {
    echo "<h2><a id='summary'>" . $team->getName() . "</a></h2>";

    // Owners, Abbreviation
    echo "<table class='center'>";
    echo "  <tr><td><strong>Owner(s):</strong></td>
                  <td>" . $team->getOwnersString() . "</td></tr>";
    echo "  <tr><td><strong>Abbreviation:</strong></td>
                  <td>" . $team->getAbbreviation() . "</td></tr>";
    echo "</table><hr/>";

    // Display chefs
    $team->displayChefs();
    echo "<hr/>";
    
    // display weekly picks
    $team->displayWeeklyPicks();
  }

  // direct to corresponding function, depending on type of display
  if (isset($_REQUEST["type"])) {
  	$displayType = $_REQUEST["type"];
  } else {
  	die("<h1>Invalid display type for team</h1>");
  }
  $team = getTeamByParam("team_id");

  if ($displayType == "display") {
    displayTeam($team);
  }
?>