<?php
  require_once 'util/sessions.php';
  SessionUtil::checkUserIsLoggedIn();
?>

<html>
<head>
<title>Top Chef Rotiss - The Chefs</title>
<link href='css/style.css' rel='stylesheet' type='text/css'>
<link rel="shortcut icon" href="images/chefhat.ico" />
</head>
<body>

<?php
  require_once 'dao/chefDao.php';
  require_once 'dao/draftPickDao.php';
  require_once 'dao/statDao.php';
  require_once 'util/navigation.php';

  // Display header.
  NavigationUtil::printHeader(true, true, NavigationUtil::THE_CHEFS_BUTTON);

  // Use the logged-in user's team to highlight rows.
  $teamId = SessionUtil::getLoggedInTeam()->getId();

  // Display chef info
  echo "<div class='bodycenter'><h1>The Chefs</h1>";

  // display table of chefs, highlighting row for specified team
  echo "<table border class='center'>
        <th colspan='2'>Chef</th>
        <th>Team</th>
        <th>Drafted</th>
        <th>Fantasy Points</th>
        <th>Week Eliminated</th></tr>";

  // sort chefs by fantasy points
  // TODO allow sorting by draft order, name, elimination week, team
  $chefs = ChefDao::getAllChefs();
  $chefToPoints = array();
  foreach ($chefs as $chef) {
  	$chefPoints = StatDao::getTotalPointsByChef($chef);
  	if ($chefPoints == null) {
  	  $chefPoints = 0;
  	}
  	$chefToPoints[$chef->getId()] = $chefPoints;
  }
  arsort($chefToPoints);
  
  foreach ($chefToPoints as $chefId => $points) {
  	$chef = ChefDao::getChefById($chefId);
    echo "<tr class='";
    if ($chef->getFantasyTeam() != null && $chef->getFantasyTeam()->getId() == $teamId) {
      echo "selected_team_row";
    }
    if (StatDao::isEliminated($chef)) {
      echo " eliminated";
    }
    echo "'>";
    
    // name and headshot
    echo "<td>" . $chef->getHeadshotImg(85, 56) . "</td>
          <td class='teamchefname'>" . $chef->getNameLink(true) . "</td>";
    
    // fantasy team
    echo "<td>";
    if ($chef->getFantasyTeam() != null) {
      echo $chef->getFantasyTeam()->getNameLink(true);
    } else {
      echo "--";
    }
    echo "</td>";
    
    // draft pick
    $draftPick = DraftPickDao::getDraftPickByChefId($chef->getId());
    echo "<td class='chefdraft'>";
    if ($draftPick != null) {
    	echo "Rd: " . $draftPick->getRound() . ", Pk: " . $draftPick->getPick();
    } else {
    	echo "--";
    }
    echo "</td>";
    
    // total fantasy points
    echo "<td>" . ($points == null ? "0" : $points) . "</td>";
    
    // elimination week
    $eliminationWeek = StatDao::getEliminationWeek($chef);
    echo "<td>" . (($eliminationWeek == null) ? "--" : $eliminationWeek) . "</td>";
    echo "</tr>";
  }
  echo "</table>";
  echo "</div>";

  // Display footer
  NavigationUtil::printFooter();
?>

</body>
</html>