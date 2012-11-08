<?php

function requireFileIn($path, $file) {
  $now_at_dir = getcwd();
  chdir(realpath(dirname(__FILE__).$path));
  require_once $file;
  chdir($now_at_dir);
}

requireFileIn('/../dao/', 'teamDao.php');
requireFileIn('/../entity/', 'user.php');

class SessionUtil {

  /**
   * Login with the specified user, after clearing out the session, and redirect to the navigation
   * page.
   */
  public static function loginAndRedirect(User $user) {
    if (!isset($_SESSION)) {
      session_start();
    }
    session_unset();
    $_SESSION["loggedinuserid"] = $user->getId();
    $_SESSION["loggedinteamid"] = $user->getTeam()->getId();
    $_SESSION["loggedinadmin"] = $user->isAdmin();


    // redirect to team page
    SessionUtil::redirectToUrl("teamPage.php");
  }

  /**
   * Determine if a user is logged in & if not, redirect the user back to the login page.
   */
  public static function checkUserIsLoggedIn() {
    SessionUtil::checkTimeout();
    if (!SessionUtil::isLoggedIn()) {
      SessionUtil::logOut();
    }
  }

  /**
   * Returns true if a user is currently logged in.
   */
  public static function isLoggedIn() {
    if (!isset($_SESSION)) {
      session_start();
    }

    if (empty($_SESSION["loggedinuserid"])) {
      return false;
    }
    return true;
  }

  /**
   * Determine if a user is logged in & an admin, & if not, redirect the user back to the login
   * page.
   */
  public static function checkUserIsLoggedInAdmin() {
    SessionUtil::checkTimeout();
    if (!SessionUtil::isLoggedInAdmin()) {
      SessionUtil::logOut();
    }
  }

  /**
   * Returns the logged-in user.
   */
  public static function getLoggedInUser() {
    return SessionUtil::isLoggedIn() ? UserDao::getUserById($_SESSION["loggedinuserid"]) : null;
  }

  /**
   * Returns the fantasy team of the currently logged-in user.
   */
  public static function getLoggedInTeam() {
    return SessionUtil::isLoggedIn() ? TeamDao::getTeamById($_SESSION["loggedinteamid"]) : null;
  }

  /**
   * Returns true if the logged-in user is an admin.
   */
  public static function isLoggedInAdmin() {
    return SessionUtil::isLoggedIn() ? $_SESSION["loggedinadmin"] : false;
  }

  /**
   * Logs out the currently logged-in user.
   */
  public static function logOut() {
    if (!isset($_SESSION)) {
      session_start();
    }
    SessionUtil::unsetSessionVariable("loggedinuserid");
    SessionUtil::unsetSessionVariable("loggedinteamid");
    SessionUtil::unsetSessionVariable("loggedinadmin");

    // clear out the rest of the session.
    session_unset();

    // redirect to home page.
    SessionUtil::redirectHome();
  }

  /**
   * Redirects the user to the specified URL
   */
  public static function redirectToUrl($url) {
    header("Location: $url");
    exit;
  }

  public static function redirectHome() {
    // TODO Change to http://topchef.rotiss.com
    SessionUtil::redirectToUrl("http://localhost/topchef/");
  }

  /**
   * Checks to see if the logged-in user has generated any activity in the past 20 minutes; if not,
   * the user is logged out.
   */
  public static function checkTimeout() {
    session_cache_expire(20);
    session_start();
    $inactive = 1200;
    if (isset($_SESSION['start']) ) {
      $session_life = time() - $_SESSION['start'];
      if ($session_life > $inactive) {
        SessionUtil::logOut();
      }
    }
    $_SESSION['start'] = time();
  }

  private static function unsetSessionVariable($sessionVar) {
    $_SESSION[$sessionVar] = null;
    unset($_SESSION[$sessionVar]);
  }
}
?>