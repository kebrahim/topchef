<?php
  require_once 'util/sessions.php';
  SessionUtil::checkUserIsLoggedIn();
?>

<html>
<head>
<title>Top Chef Rotiss - Draft</title>
<link href='css/style.css' rel='stylesheet' type='text/css'>
<link rel="shortcut icon" href="images/chefhat.ico" />
</head>
<body>

<?php
  require_once 'dao/draftPickDao.php';
  require_once 'util/navigation.php';

  function displayChefLink($chef) {
    if ($chef != null) {
      return "<td>" . $chef->getHeadshotImg(85, 56) . "</td>
              <td>" . $chef->getNameLink(true) . "</td>";
    } else {
      return "<td colspan='2'>--</td>";
    }
  }

  // display entire draft
  function displayDraft($teamId) {
    echo "<div class='bodycenter'><h1>Top Chef Draft 2012</h1>";

    // display table of draft picks, highlighting row for specified team
    echo "<table border class='center'>
          <th>Round</th><th>Pick</th><th>Team</th><th colspan='2'>Chef</th>
          <th>Fantasy Points</th></tr>";

    $draftPicks = DraftPickDao::getAllDraftPicks();
    foreach ($draftPicks as $draftPick) {
      echo "<tr";
      if ($draftPick->getTeam()->getId() == $teamId) {
        echo " class='selected_team_row'";
      }
      $points = StatDao::getTotalPointsByChef($draftPick->getChef());
      if ($points == null) {
      	$points = 0;
      }
      echo "><td>" . $draftPick->getRound() . "</td>
             <td>" . $draftPick->getPick() . "</td>
             <td>" . $draftPick->getTeam()->getNameLink(true) . "</td>
             " . displayChefLink($draftPick->getChef()) . "
             <td>" . $points . "</td></tr>";
    }
    echo "</table><br>";
  }

  // Display header.
  NavigationUtil::printHeader(true, true, NavigationUtil::DRAFT_BUTTON);

  // Use the logged-in user's team to highlight rows.
  $teamId = SessionUtil::getLoggedInTeam()->getId();

  // Display draft results.
  echo "<form action='draftPage.php' method=post>";
  displayDraft($teamId);

  echo "</form></div>";

  // Display footer
  NavigationUtil::printFooter();
?>

</body>
</html>