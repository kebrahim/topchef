<?php
  require_once 'util/sessions.php';
  SessionUtil::checkUserIsLoggedIn();
?>

<html>
<head>
<title>Top Chef Rotiss - Weekly Picks</title>
<link href='css/style.css' rel='stylesheet' type='text/css'>
<link rel="shortcut icon" href="images/chefhat.ico" />
</head>

<script>
// shows the picks page for the specified week
function showWeek(week) {
    // If week is blank, then clear the team div.
	if (week=="" || week=="0") {
		document.getElementById("weekDisplay").innerHTML="";
		return;
	}

	// Display team information.
	getRedirectHTML(document.getElementById("weekDisplay"),
	    "admin/displayScoringWeek.php?type=picks&week=" + week);
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
  require_once 'dao/pickDao.php';
  require_once 'util/mail.php';
  require_once 'util/navigation.php';
  
  // Display header.
  NavigationUtil::printTallHeader(true, true, NavigationUtil::WEEKLY_PICKS_BUTTON);
  echo "<div class='bodycenter'>";

  if (isset($_REQUEST['submit'])) {
    if (isset($_REQUEST['pick_chef_id']) && $_REQUEST['pick_chef_id'] != '0') {
      if (isset($_REQUEST['pick_record']) && $_REQUEST['pick_record'] != '0') {
      	$pickId = $_REQUEST['pick_id'];
      	$pick = PickDao::getPickById($pickId);
      	$pick->setChef(ChefDao::getChefById($_REQUEST['pick_chef_id']));
      	$pick->setRecord($_REQUEST['pick_record']);
      	if (!PickDao::updatePick($pick)) {
      	  echo "<div class='error_msg_pad'>Error: Chef + Win/Loss already selected!</div>";
        } else {
          echo "<div class='alert_msg_pad_top'>Pick accepted! Thank you!</div>";
          MailUtil::sendWeeklyPickEmail($pick);
      	}
      } else {
      	echo "<div class='error_msg_pad'>Error: Please choose a Win/Loss!</div>";
      }
    } else {
      echo "<div class='error_msg_pad'>Error: Please choose a Chef!</div>";
    }
    $week = $_REQUEST['week'];
  } else {
    $week = PickDao::getMaxWeek();
  }
  
  // Allow user to choose from list of weeks to see corresponding scoring breakdown.
  $maxWeek = PickDao::getMaxWeek();
  echo "<FORM ACTION='picksPage.php' METHOD=POST>";
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
  echo "</select>";
  echo "<h1>Weekly Picks</h1><hr/>";
  echo "<div id='weekDisplay'></div><br/>";
?>
  
<script>
  // initialize weekDisplay with selected week
  showWeek(document.getElementById("week").value);
</script>
  
<?php
  echo "</form></div>";
    
  // Display footer
  NavigationUtil::printFooter();
?>

</body>
</html>
