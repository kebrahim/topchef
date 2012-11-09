<?php
  require_once 'dao/chefDao.php';
  require_once 'dao/draftPickDao.php';
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

    // if admin user, show edit link
    if (SessionUtil::isLoggedInAdmin()) {
      echo "<a href='admin/manageChef.php?chef_id=" . $chef->getId() .
      	"'>Manage chef</a><br>";
    }

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

    echo "</table><br/>";

    // TODO show chef's scoring stats
    echo "</div></div>";

    echo "<div id='right_col'><div id='right_col_inner'>";
    // Headshot
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
