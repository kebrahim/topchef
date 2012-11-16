<?php
  require_once 'util/sessions.php';
  SessionUtil::checkUserIsLoggedIn();
?>

<html>
<head>
<title>Rotiss.com - Display Chef</title>
<link href='css/style.css' rel='stylesheet' type='text/css'>
</head>

<script>
//shows the chef with the specified id
function showChef(chefId) {
    // If chefid is blank, then clear the team div.
	if (chefId=="" || chefId=="0") {
		document.getElementById("chefDisplay").innerHTML="";
		return;
	}

	// Display chef information.
	getRedirectHTML(document.getElementById("chefDisplay"),
	    "displayChef.php?type=display&chef_id=" + chefId);
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
  require_once 'dao/chefDao.php';
  require_once 'util/navigation.php';

  // Display header.
  NavigationUtil::printTallHeader(true, true, NavigationUtil::MY_TEAM_BUTTON);
  echo "<div class='bodycenter'>";

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

  // Allow user to choose from list of chefs to see corresponding summary page.
  $allChefs = ChefDao::getAllChefs();
  echo "<br/><label for='chef_id'>Choose chef: </label>";
  echo "<select id='chef_id' name='chef_id' onchange='showChef(this.value)'>";
  foreach ($allChefs as $selectChef) {
    echo "<option value='" . $selectChef->getId() . "'";
    if ($selectChef->getId() == $chefId) {
      echo " selected";
    }
    echo ">" . $selectChef->getFullName() . "</option>";
  }
  echo "</select><br/>";
  echo "<div id='chefDisplay'></div><br/>";
?>

<script>
  // initialize chefDisplay with selected chef
  showChef(document.getElementById("chef_id").value);
</script>

<?php
  echo "</div>";

  // Display footer
  NavigationUtil::printFooter();
?>

</body>
</html>
