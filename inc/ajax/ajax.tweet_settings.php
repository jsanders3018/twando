<?php
/*
Twando.com Free PHP Twitter Application
http://www.twando.com/
*/
require "../../vendor/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;
include('../include_top.php');

if (mainFuncs::is_logged_in() != true) {
 include('../content/' . TWANDO_LANG . '/ajax.not_logged_in.php');
} else {

 //Define response
 $response_msg = "";
 $ap_creds = $db->get_ap_creds();
 $q1a = $db->get_user_data($_REQUEST['twitter_id']);

 if ($_REQUEST['update_type'] == 'update_data') {
  //Updates to be done here
  switch ($_REQUEST['tab_id']) {
   case 'tab1':
   
   if ($_REQUEST['tweet_image'] != "http://")
	{
	$imagefile  = curl($_REQUEST['tweet_image']);
   $connection = new TwitterOAuth($ap_creds['consumer_key'], $ap_creds['consumer_secret'], $q1a['oauth_token'], $q1a['oauth_token_secret']);
   
   $media1 = $connection->upload('media/upload', array('media' => $imagefile ,));
	$parameters = array(
    'status' => $_REQUEST['tweet_content'],
    'media_ids' => implode(',', array($media1->media_id_string)),
);
	$connection->post('statuses/update', $parameters);
   if ($connection->getLastHttpCode() == 200) {
     $response_msg = mainFuncs::push_response(13);
	 exit();
    } else {
     $response_msg = mainFuncs::push_response(14);
	 exit();
    }
   }
   
   
   
   
 
   
   if ($_REQUEST['tweet_content'] || $_REQUEST['tweet_image'] == "http://") {
    $connection = new TwitterOAuth($ap_creds['consumer_key'], $ap_creds['consumer_secret'], $q1a['oauth_token'], $q1a['oauth_token_secret']);
    $connection->post('statuses/update', array('status' => $_REQUEST['tweet_content']));

    if ($connection->getLastHttpCode() == 200) {
     $response_msg = mainFuncs::push_response(13);
    } else {
     $response_msg = mainFuncs::push_response(14);
	 $myfile = fopen("../../error.txt", "w") or die("Unable to open file!");
	 $results = print_r($connection, true);
	 fwrite($myfile, $results);
	 fclose($myfile);
    }
   }


   
  break;

  case 'tab2':
	if (empty($_REQUEST['a'])) { $_REQUEST['a'] = NULL;}
   if ( ($_REQUEST['a'] == 'deletetweet') and ($_REQUEST['deltweet_id']) ) {
    $db->query("DELETE FROM " . DB_PREFIX . "scheduled_tweets WHERE owner_id='" . $db->prep($q1a['id']) . "' AND id='" .  $db->prep($_REQUEST['deltweet_id']) . "'");
    $response_msg = mainFuncs::push_response(16);
   }
   if (empty($_REQUEST['a'])) { $_REQUEST['a'] = NULL;}
   if ( ($_REQUEST['a'] == 'edittweetsave') and ($_REQUEST['edittweetsave_id']) ) {
    
	$db->query("UPDATE " . DB_PREFIX . "scheduled_tweets SET tweet_content='".$db->prep($_REQUEST['tweet_content'])."',  time_to_post='".$db->prep($_REQUEST['time_to_post'])."',everyday='".$_REQUEST['everyday']."'WHERE owner_id='" . $db->prep($q1a['id']) . "' AND id='" .  $db->prep($_REQUEST['edittweetsave_id']) . "'");
	
	
    $response_msg = mainFuncs::push_response(17);
   }
  break;

  case 'tab3':
   if ($_REQUEST['tweet_content']) 
   {
	  $db->query("INSERT INTO " . DB_PREFIX . "scheduled_tweets (owner_id, tweet_content, time_to_post, everyday)
    		 VALUES ('" . $db->prep($q1a['id']) . "','" . $db->prep($_REQUEST['tweet_content']) . "','" . $db->prep($_REQUEST['time_to_post']) . "','" . $db->prep($_REQUEST['everyday']) . "')");
    $response_msg = mainFuncs::push_response(18);
   }
 
   
  break;
  
  case 'tab5':
   if ($_REQUEST['tweet_content']) {
	$imagefile  = curl($_REQUEST['tweet_image']);
   }
   if ($imagefile === NULL)
   {
	   echo "URL failed please check the url works";
	   
   } else {
  
    $db->query("INSERT INTO " . DB_PREFIX . "scheduled_tweets (owner_id, tweet_content, tweet_image, time_to_post, everyday)
    		 VALUES ('" . $db->prep($q1a['id']) . "','" . $db->prep($_REQUEST['tweet_content']) . "','" . $db->prep($imagefile) . "','" . $db->prep($_REQUEST['time_to_post']) . "','" . $db->prep($_REQUEST['everyday']) . "')");
    $response_msg = mainFuncs::push_response(18);
  }
  break;
  //End of tab switch
  }
 }
 //End of data update POST
 }


 include('../content/' . TWANDO_LANG . '/ajax.tweet_settings_inc.php');


//End of is logged in



include('../include_bottom.php');
?>
