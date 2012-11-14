<?php

require_once 'commonDao.php';
CommonDao::requireFileIn('/../entity/', 'pick.php');

class PickDao {

  /**
   * Returns the pick with the specified id
   */
  public static function getPickById($pickId) {
    CommonDao::connectToDb();
    $query = "select p.*
              from pick p
              where p.pick_id = " . $pickId;
    return PickDao::createPickFromQuery($query);
  }

  /**
   * Returns all of the picks for the specified team ID.
   */
  public static function getPicksByTeamId($teamId) {
    CommonDao::connectToDb();
    $query = "select p.*
    	      from pick p
              where p.team_id = $teamId
              order by week, pick_number";
    return PickDao::createPicksFromQuery($query);
  }

  /**
   * Returns all of the picks during the specified week.
   */
  public static function getPicksByWeek($week) {
  	CommonDao::connectToDb();
  	$query = "select p.*
  	          from pick p
  	          where p.week = $week
  	          order by p.pick_number";
  	return PickDao::createPicksFromQuery($query);
 }

  private static function createPickFromQuery($query) {
    $pickArray = PickDao::createPicksFromQuery($query);
    if (count($pickArray) == 1) {
      return $pickArray[0];
    }
    return null;
  }

  private static function createPicksFromQuery($query) {
    $res = mysql_query($query);
    $picksDb = array();
    while($pickDb = mysql_fetch_assoc($res)) {
      $picksDb[] = new Pick($pickDb["pick_id"], $pickDb["week"], $pickDb["pick_number"], 
          $pickDb["team_id"], $pickDb["chef_id"], $pickDb["record"], $pickDb["points"]);
    }
    return $picksDb;
  }

  /**
   * Returns the latest week that picks were made.
   */
  public static function getMaxWeek() {
  	CommonDao::connectToDb();
  	$query = "select max(week) from pick";
  	$res = mysql_query($query);
  	$row = mysql_fetch_row($res);
  	return $row[0];
  }

  public static function createPick(Pick $pick) {
  	// TODO
  }
  
  /**
   * Updates the specified pick in the 'pick' table.
   */
  public static function updatePick(Pick $pick) {
    CommonDao::connectToDb();
    $query = "update pick set chef_id = " . $pick->getChefId() . ",
                              record = '" . $pick->getRecord() . "',
                              points = " . (($pick->getPoints() == null) ? 
                              		       "null" : $pick->getPoints()) . "
                          where pick_id = " . $pick->getId();
    return mysql_query($query);
  }
}