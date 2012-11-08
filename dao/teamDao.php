<?php

require_once 'commonDao.php';
CommonDao::requireFileIn('/../entity/', 'team.php');

class TeamDao {
  /**
   * Returns team information for specified team ID and null if none is found.
   */
  public static function getTeamById($team_id) {
    CommonDao::connectToDb();
    $query = "select t.*
              from team t
              where t.team_id = $team_id";
  	return TeamDao::createTeamFromQuery($query);
  }

  /**
   * Returns all teams.
   */
  public static function getAllTeams() {
    CommonDao::connectToDb();
    $query = "select t.*
              from team t
              order by lower(t.team_name)";
    return TeamDao::createTeamsFromQuery($query);
  }

//   /**
//    * Returns the team to which the specified chef belongs, and null if the chef does not belong
//    * to any team.
//    */
//   public static function getTeamByChef(Chef $chef) {
//   	CommonDao::connectToDb();
//   	$query = "select t.*
//   	          from team t, team_chef tc
//   	          where t.team_id = tc.team_id and tc.chef_id = " . $chef->getId();
//   	return TeamDao::createTeamFromQuery($query);
//   }

  private static function createTeamFromQuery($query) {
  	$teamArray = TeamDao::createTeamsFromQuery($query);
  	if (count($teamArray) == 1) {
  	  return $teamArray[0];
  	}
  	return null;
  }

  private static function createTeamsFromQuery($query) {
    $res = mysql_query($query);
    $teamsDb = array();
    if (mysql_num_rows($res) > 0) {
      while($teamDb = mysql_fetch_assoc($res)) {
        $teamsDb[] = new Team($teamDb["team_id"], $teamDb["team_name"], $teamDb["abbreviation"]);
      }
    }
    return $teamsDb;
  }

 /**
  * Updates the specified team in the 'team' table.
  */
  public static function updateTeam($team) {
    CommonDao::connectToDb();
    $query = "update team set team_name = '" . $team->getName() . "',
                              abbreviation = '" . $team->getAbbreviation() .
                              "' where team_id = " . $team->getId();
    $result = mysql_query($query) or die('Invalid query: ' . mysql_error());
  }
}
?>