<?php

require_once 'commonDao.php';
CommonDao::requireFileIn('/../entity/', 'draftPick.php');

/**
 * Manages the 'draft_pick' table.
 */
class DraftPickDao {
  /**
   * Returns all of the draft picks belonging to the specified team.
   */
  public static function getDraftPicksByTeamId($team_id) {
    CommonDao::connectToDb();
    $query = "select D.*
              from draft_pick D
              where D.team_id = $team_id
              order by D.round, D.pick";
    return DraftPickDao::createDraftPicksFromQuery($query);
  }

  /**
   * Returns all of the draft picks.
   */
  public static function getAllDraftPicks() {
    CommonDao::connectToDb();
    $query = "select D.*
              from draft_pick D
              order by D.round, D.pick";
    return DraftPickDao::createDraftPicksFromQuery($query);
  }

  /**
   * Returns the draft pick identified by the specified id.
   */
  public static function getDraftPickById($draftPickId) {
    CommonDao::connectToDb();
    $query = "select D.*
              from draft_pick D
              where D.draft_pick_id = $draftPickId";
    return DraftPickDao::createDraftPickFromQuery($query);
  }

  /**
   * Returns the draft pick associated with the specified chef id.
   */
  public static function getDraftPickByChefId($chefId) {
    CommonDao::connectToDb();
    $query = "select D.*
              from draft_pick D
              where D.chef_id = $chefId";
    return DraftPickDao::createDraftPickFromQuery($query);
  }

  private static function createDraftPickFromQuery($query) {
    $draftPickArray = DraftPickDao::createDraftPicksFromQuery($query);
    if (count($draftPickArray) == 1) {
      return $draftPickArray[0];
    }
    return null;
  }

  private static function createDraftPicksFromQuery($query) {
    $res = mysql_query($query);
    $draft_picks = array();
    while($draft_pick_db = mysql_fetch_assoc($res)) {
      $draft_picks[] = new DraftPick($draft_pick_db["draft_pick_id"], $draft_pick_db["round"],
          $draft_pick_db["pick"], $draft_pick_db["team_id"], $draft_pick_db["chef_id"]);
    }
    return $draft_picks;
  }

  public static function updateDraftPick(DraftPick $draftPick) {
    CommonDao::connectToDb();
    $query = "update draft_pick set team_id = " . $draftPick->getTeam()->getId() . ",
                                    round = " . $draftPick->getRound() . ",
                                    pick = " . $draftPick->getPick() . ",
                                    chef_id = " . $draftPick->getChefId() .
             " where draft_pick_id = " . $draftPick->getId();
    $result = mysql_query($query) or die('Invalid query: ' . mysql_error());
  }
}
?>