<?php
  require_once 'util/sessions.php';
  SessionUtil::checkUserIsLoggedIn();
?>
<html>
<head>
<title>Rotiss.com - Display Chef</title>
<link href='css/style.css' rel='stylesheet' type='text/css'>
</head>

<body>

<?php
  require_once 'dao/chefDao.php';
  require_once 'util/navigation.php';

  // Display header.
  NavigationUtil::printHeader(true, true, NavigationUtil::MY_TEAM_BUTTON);

  if (isset($_REQUEST["chef_id"])) {
    $chefId = $_REQUEST["chef_id"];
  } else {
    die("<h1>Missing chefId for chef page</h1>");
  }

  // Get chef from db.
  $chef = ChefDao::getChefById($chefId);
  if ($chef == null) {
    die("<h1>chef id " . $chefId . " does not exist!</h1>");
  }

  // Display chef attributes.
  echo "<div class='bodycenter'>";
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

  // TODO draft round

  echo "</table><br/>";

  // TODO show chef's scoring stats
  echo "</div></div>";
  
  echo "<div id='right_col'><div id='right_col_inner'>";
  // Headshot
  echo $chef->getBodyImg(400, 600);
  echo "</div></div></div>";
  
  echo "</div>";

  // Display footer
  NavigationUtil::printFooter();
?>

</body>
</html>
