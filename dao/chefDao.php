<?php

require_once 'commonDao.php';
CommonDao::requireFileIn('/../entity/', 'chef.php');

class ChefDao {

  /**
   * Returns chef information for the specified chef ID and null if the chef ID is not found.
   */
  static function getChefById($chefId) {
    CommonDao::connectToDb();
    $query = "select c.*
              from chef c
              where c.chef_id = $chefId";
    return ChefDao::createChefFromQuery($query);
  }

  /**
   * Returns an array of all chefs.
   */
  public static function getAllChefs() {
    CommonDao::connectToDb();
    $query = "select c.*
              from chef c
              order by c.last_name, c.first_name";
    return ChefDao::createChefsFromQuery($query);
  }

  /**
   * Returns an array of players belonging to the specified fantasy team.
   */
  public static function getChefsByTeam(Team $team) {
    CommonDao::connectToDb();
    $query = "select c.*
        	  from chef c, team_chef tc
        	  where c.chef_id = tc.chef_id and tc.team_id = " . $team->getId() .
        	" order by c.last_name, c.first_name";
    return ChefDao::createChefsFromQuery($query);
  }

  private static function createChefFromQuery($query) {
    $chefArray = ChefDao::createChefsFromQuery($query);
    if (count($chefArray) == 1) {
      return $chefArray[0];
    }
    return null;
  }

  private static function createChefsFromQuery($query) {
    $res = mysql_query($query);
    $chefsDb = array();
    while($chefDb = mysql_fetch_assoc($res)) {
      $chefsDb[] = ChefDao::populateChef($chefDb);
    }
    return $chefsDb;
  }

  /**
   * Creates and returns a Chef with data from the specified db result, which contains
   * references to all of the fields in the 'chef' table.
   */
  public static function populateChef($chefDb) {
  	return new Chef($chefDb["chef_id"], $chefDb["first_name"], $chefDb["last_name"]);
  }
}
?>