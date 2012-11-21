<?php

require_once 'commonDao.php';
CommonDao::requireFileIn('/../entity/', 'user.php');

class UserDao {

  /**
   * Returns the user with the specified id
   */
  public static function getUserById($userId) {
    CommonDao::connectToDb();
    $query = "select u.*
              from user u
              where u.user_id = " . $userId;
    return UserDao::createUserFromQuery($query);
  }
  
  /**
   * Returns all of the users.
   */
  public static function getAllUsers() {
  	CommonDao::connectToDb();
  	$query = "select u.* from user u";
  	return UserDao::createUsersFromQuery($query);
  }

  /**
   * Returns all of the owners for the specified team ID.
   */
  public static function getUsersByTeamId($teamId) {
    CommonDao::connectToDb();
    $query = "select u.*
    	      from user u
              where u.team_id = $teamId";
    return UserDao::createUsersFromQuery($query);
  }

  /**
   * Returns the user with the specified username and password.
   */
  public static function getUserByUsernamePassword($username, $password) {
    CommonDao::connectToDb();
    $query = "select u.*
    	      from user u
              where u.username = '" . $username . "'
              and u.password = '" . $password . "'";
    return UserDao::createUserFromQuery($query);
  }

  private static function createUserFromQuery($query) {
    $userArray = UserDao::createUsersFromQuery($query);
    if (count($userArray) == 1) {
      return $userArray[0];
    }
    return null;
  }

  private static function createUsersFromQuery($query) {
    $res = mysql_query($query);
    $usersDb = array();
    while($userDb = mysql_fetch_assoc($res)) {
      $usersDb[] = new User($userDb["user_id"], $userDb["username"], $userDb["password"],
          $userDb["first_name"], $userDb["last_name"], $userDb["email"], $userDb["team_id"], 
      	  $userDb["is_admin"]);
    }
    return $usersDb;
  }

  /**
   * Updates the specified user in the 'user' table.
   */
  public static function updateUser(User $user) {
    CommonDao::connectToDb();
    $query = "update user set username = '" . $user->getUsername() . "',
                              password = '" . $user->getPassword() . "',
                              email = '" . $user->getEmail() . "',
                              first_name = '" . $user->getFirstName() . "',
                              last_name = '" . $user->getLastName() . "'
                          where user_id = " . $user->getId();
    $result = mysql_query($query) or die('Invalid query: ' . mysql_error());
  }
}