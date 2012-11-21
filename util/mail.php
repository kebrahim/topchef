<?php

require_once 'commonUtil.php';
CommonUtil::requireFileIn('/../dao/', 'pickDao.php');
CommonUtil::requireFileIn('/../dao/', 'userDao.php');

/**
 * Handles automatic e-mailing functionality
 */
class MailUtil {

  /**
   * Sends an email to all users when the specified weekly pick has been made.
   */
  public static function sendWeeklyPickEmail(Pick $pick) {
  	$users = UserDao::getAllUsers();
  	$to = MailUtil::getEmailAddresses($users);
  	$subject = "Top Chef Rotiss - Week " . $pick->getWeek() . " picks";
  	$nextPick = PickDao::getPickByWeekPickNumber($pick->getWeek(), $pick->getPickNumber() + 1);
  	$message = "<strong>" . $pick->getTeam()->getName() . " (" .
  		$pick->getTeam()->getAbbreviation() . ")</strong> has" .
  		" made their pick for week " . $pick->getWeek() . ":<br/><br/>
  		<strong>Chef:</strong> " . $pick->getChef()->getFullName() . "<br/>
  		<strong>Result:</strong> " . (($pick->getRecord() == Pick::WIN) ? "Win" : "Loss") . 
  	    "<br/><br/>";
  	if ($nextPick != null) {
  	  $message .= "<strong>Next pick:</strong> " . $nextPick->getTeam()->getName() . " (" .
  	      $nextPick->getTeam()->getAbbreviation() . ")";
  	} else {
  	  $message .= "Thus concludes the weekly picks for week " . $pick->getWeek();
  	}
  	 
  	// set headers
  	$headers  = "From: Top Chef Rotiss<noreply@rotiss.com>\r\n";
  	$headers .= 'MIME-Version: 1.0' . "\n";
  	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
  	
  	mail($to, $subject, $message, $headers);
  }
  
  /**
   * Returns a comma-separated list of email addresses from the array of users.
   */
  private static function getEmailAddresses($users) {
  	$emails = "";
  	$firstEmail = true;
  	foreach ($users as $user) {
  	  if ($firstEmail) {
  	  	$firstEmail = false;
  	  } else {
  	  	$emails .= ",";
  	  }
  	  $emails .= $user->getEmail();
  	}
  	return $emails;
  }
}