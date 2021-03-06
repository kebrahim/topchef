<?php
require_once "sessions.php";

class NavigationUtil {
  const SCOREBOARD_BUTTON = 1;
  const WEEKLY_PICKS_BUTTON = 2;
  const TEAM_SUMMARY_BUTTON = 3;
  const THE_CHEFS_BUTTON = 4;
  const DRAFT_BUTTON = 5;
  const ADMIN_BUTTON = 6;
  const MANAGE_SCORES_BUTTON = 7;

  public static function printHeader($showNavigationLinks, $isTopLevel, $selectedButton) {
    NavigationUtil::displayHeader($showNavigationLinks, $isTopLevel, $selectedButton, 'wrapper');
  }

  public static function printTallHeader($showNavigationLinks, $isTopLevel, $selectedButton) {
  	NavigationUtil::displayHeader($showNavigationLinks, $isTopLevel, $selectedButton, 'tallwrapper');
  }
  
  public static function printNoWidthHeader($showNavigationLinks, $isTopLevel, $selectedButton) {
    NavigationUtil::displayHeader(
        $showNavigationLinks, $isTopLevel, $selectedButton, 'nowidthwrapper');
  }

  /**
   * Displays the header banner with navigation links.
   */
  private static function displayHeader($showNavigationLinks, $isTopLevel, $selectedButton,
      $wrapperId) {
    echo "<div id='container'>";
    echo "<header>";
    if ($showNavigationLinks) {
      echo "<div id='banner'>";
    }
    echo "    <div id='logo'>
                <img src='";
    if (!$isTopLevel) {
      echo "../";
    }
    echo "images/tc_logo.png' width='240'>
              </div>";
    if ($showNavigationLinks) {
      echo "  <nav id='menu'>
                <ul>";

      // if admin user, show admin button/menu
      if (SessionUtil::isLoggedInAdmin()) {
      	NavigationUtil::printAdminMenu($isTopLevel, $selectedButton);
      }

      // Scoreboard page
      NavigationUtil::printListItem("scoreboardPage.php", "Scoreboard", $isTopLevel,
          $selectedButton, self::SCOREBOARD_BUTTON);
      
      // Picks page
      NavigationUtil::printListItem("picksPage.php", "Weekly Picks", $isTopLevel,
      	  $selectedButton, self::WEEKLY_PICKS_BUTTON);
      
      // My team page
      NavigationUtil::printListItem("teamPage.php", "Team Summary", $isTopLevel, $selectedButton,
          self::TEAM_SUMMARY_BUTTON);
      
      // Chefs page
      NavigationUtil::printListItem("allChefsPage.php", "The Chefs", $isTopLevel, $selectedButton,
      		self::THE_CHEFS_BUTTON);
      
      // Draft page
      NavigationUtil::printListItem("draftPage.php", "Draft", $isTopLevel, $selectedButton,
          self::DRAFT_BUTTON);

      echo "</ul></nav>";

      // show logged-in user name with links for editing profile & signing out
      NavigationUtil::printProfileInfo($isTopLevel);

      echo "</div>"; // banner
    }
    echo "</header>";
    echo "<div id='$wrapperId'>";
  }

  private static function printProfileInfo($isTopLevel) {
  	$user = SessionUtil::getLoggedInUser();
  	echo "<div id='profileinfo'>";
  	echo "Hi " . $user->getFirstName() . "!
  	      <a href='" . ($isTopLevel ? "" : "../") . "editProfilePage.php'>Edit profile</a>
  	      <a href='" . ($isTopLevel ? "" : "../") . "logoutPage.php'>Sign out</a>";
  	echo "</div>";
  }

  private static function printListItem($url, $caption, $isTopLevel, $selectedButton, $listButton) {
    echo "<li>";
    NavigationUtil::printLink($url, $caption, $isTopLevel, ($selectedButton == $listButton));
    echo "</li>";
  }

  private static function printLink($url, $caption, $isTopLevel, $isSelected) {
  	echo "<a";
  	if ($isSelected) {
  	  echo " id='navselected'";
  	}
  	echo " href='" . ($isTopLevel ? "" : "../") . $url . "'>" . $caption . "</a>";
  }

  private static function printAdminMenu($isTopLevel, $selectedButton) {
  	// top-level button directs to manage teams page
  	echo "<li class='dropdown'>";
  	$adminSelected = ($selectedButton >= self::ADMIN_BUTTON);
  	NavigationUtil::printLink(
  			"admin/manageScores.php", "Admin", $isTopLevel, $adminSelected);

  	// sub-menu includes all admin options
  	echo "<ul class='dropdown'>";

    // Manage results
  	NavigationUtil::printListItem("admin/manageScores.php", "Manage Scores", $isTopLevel,
  	    $selectedButton, self::MANAGE_SCORES_BUTTON);

  	echo "</ul></li>";
  }

  /**
   * Displays the footer, attached to the bottom of the page.
   */
  public static function printFooter() {
    echo "</div>";  // wrapper
    echo "<div class='push'></div>";
    echo "</div>"; // container
    echo "<footer>
            <p>Top Chef Rotiss 1.1</p>
          </footer>";
  }
}