<?php
  require_once 'dao/chefDao.php';
  require_once 'dao/draftPickDao.php';
  require_once 'dao/statDao.php';
  require_once 'util/sessions.php';

  /**
   * Returns a Chef based on the ID specified in the GET/POST.
   */
  function getChefByParam($param) {
    if (isset($_REQUEST[$param])) {
      $chefId = $_REQUEST[$param];
    } else {
      $chefId = 0;
    }
    $chef = ChefDao::getChefById($chefId);
    if ($chef == null) {
      die("<h1>chef id " . $chefId . " does not exist for param " . $param . "!</h1>");
    }
    return $chef;
  }

  /**
   * Display selected chef on chef summary page.
   */
  function displayChef(Chef $chef) {
    // Display chef attributes.
    echo "<h1>" . $chef->getFullName() . "</h1>";

    echo "<div id='column_container'>";
    echo "<div id='left_col'><div id='left_col_inner'>";
    echo "<table class='center'>";

    // Fantasy team
    echo "<tr><td><strong>Fantasy Team:</strong></td><td>";
    $fantasyTeam = $chef->getFantasyTeam();
    if ($fantasyTeam == null) {
      echo "--";
    } else {
      echo $fantasyTeam->getNameLink(true);
    }
    echo "</td></tr>";

    // draft round
    echo "<tr><td><strong>Drafted:</strong></td>";
    $draftPick = DraftPickDao::getDraftPickByChefId($chef->getId());
    echo "<td>";
    if ($draftPick != null) {
      echo "Round: " . $draftPick->getRound() . ", Pick: " . $draftPick->getPick();
    } else {
      echo "--";
    }
    echo "</td></tr>";
    
    // elimination week
    $eliminationWeek = StatDao::getEliminationWeek($chef);
    echo "<tr><td><strong>Elimination Week:</strong></td>
              <td>" . (($eliminationWeek == null) ? "--" : $eliminationWeek) . "</td>";
    
    echo "</table><br/>";

    // show chef's scoring stats
    echo "<h2>Stats</h2>
          <table border class='center'><tr>
            <th>Week</th><th class='weekheader'>Stats</th><th>Fantasy Points</th></tr>";
    $maxWeek = StatDao::getMaxWeek();
    $totalPoints = 0;
    for ($wk = 1; $wk <= $maxWeek; $wk++) {
      $statLine = StatDao::getStatLineForChefWeek($chef, $wk);
      $weeklyPoints = 0;
      if ($statLine != null) {
      	echo "<tr";
      	if ($statLine->isWinner()) {
      	  echo " class = 'winner'";
      	} else if ($statLine->isEliminated()) {
      	  echo " class = 'eliminated'";
      	}
      	echo "><td>" . $statLine->getWeek() . "</td>
      	          <td>";
      	$firstStat = true;
      	foreach ($statLine->getStats() as $stat) {
      	  if ($firstStat) {
      	  	$firstStat = false;
      	  } else {
      	  	echo ", ";
      	  }
      	  echo $stat->getAbbreviation();
      	  $weeklyPoints += $stat->getPoints();
      	}
      	echo "</td>
      	      <td>" . $weeklyPoints . "</td></tr>";
      	$totalPoints += $weeklyPoints;
      }
    }
    // show cumulative points
    echo "<tr><td colspan='2'><strong>Total</strong></td>
              <td><strong>" . $totalPoints . "</strong></td></tr>";
    echo "</table>";
    
    echo "</div></div>";

    echo "<div id='right_col'><div id='right_col_inner'>";
    // full body pic
    echo $chef->getBodyImg(400, 600);
    echo "</div></div></div>";
  }

  // direct to corresponding function, depending on type of display
  if (isset($_REQUEST["type"])) {
    $displayType = $_REQUEST["type"];
  } else {
    die("<h1>Invalid display type for chef</h1>");
  }
  $chef = getChefByParam("chef_id");

  if ($displayType == "display") {
    displayChef($chef);
  }

?>
