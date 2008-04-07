<?php
/* 
Plugin Name: Running Time
Plugin URI:  http://labs.saruken.com/
Description: Outputs the date of the oldest post and/or the newest post. Also will output how long the your site has been running for based on the first post date.
Version: 1.0.2
Author: Andrew Hamilton 
Author URI: http://saruken.com
Licensed under the The GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html
*/ 

function runningtime_daterange(
	$posttype = 'post',
	$dateoutput = 'both', 
	$dateformat = 'DDMMYYYY',
	$separator = '/',
	$joiningword = 'to') 
	{
		 
 global $wpdb;

 //Find the newest and oldest posts		
 		if ($posttype == 'post'){ //Posts Only
 			$newestpost = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_date != '0000-00-00 00:00:00' AND post_status = 'publish' AND post_type = 'post' ORDER BY post_date DESC LIMIT 1");
 			$oldestpost = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_date != '0000-00-00 00:00:00' AND post_status = 'publish' AND post_type = 'post' ORDER BY post_date LIMIT 1");
 		}elseif ($posttype == 'page'){ //Pages Only
 			$newestpost = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_date != '0000-00-00 00:00:00' AND post_status = 'publish' AND post_type = 'page' ORDER BY post_date DESC LIMIT 1");
 			$oldestpost = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_date != '0000-00-00 00:00:00' AND post_status = 'publish' AND post_type = 'page' ORDER BY post_date LIMIT 1");
 		}else{ //Both Posts and Pages
 			$newestpost = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_date != '0000-00-00 00:00:00' AND post_status = 'publish' ORDER BY post_date DESC LIMIT 1");
 			$oldestpost = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_date != '0000-00-00 00:00:00' AND post_status = 'publish' ORDER BY post_date LIMIT 1");
 		}

 		//Get the times of the newest and oldest post 		
 		$newestposttime = $wpdb->get_var("SELECT post_date FROM $wpdb->posts WHERE ID = $newestpost LIMIT 1");
 		$oldestposttime = $wpdb->get_var("SELECT post_date FROM $wpdb->posts WHERE ID = $oldestpost LIMIT 1");

			//Date format pattern YYYY-MM-DD HH:MM:SS
			$pattern = '/(19|20)(\d{2})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/';

			//Replacement date formats
			switch ($dateformat) {
			case 'MMYYYY':
				$replace = '\3'.$separator.'\1\2';
				break;
			case 'MMYY':
				$replace = '\3'.$separator.'\2';
				break;
			case 'DDMMYYYY':
				$replace = '\4'.$separator.'\3'.$separator.'\1\2';
				break;
			case 'MMDDYYYY':
				$replace = '\3'.$separator.'\4'.$separator.'\1\2';
				break;
			case 'DDMMYY':
				$replace = '\4'.$separator.'\3'.$separator.'\2';
				break;
			case 'MMDDYY':
				$replace = '\3'.$separator.'\4'.$separator.'\2';
				break;
			}

			//Reformat post dates
 		$oldestdate = preg_replace($pattern, $replace, $oldestposttime);
 		$newestdate = preg_replace($pattern, $replace, $newestposttime);

			//Dates to output
			switch ($dateoutput) {
			case 'newest':
   	echo $newestdate;
   	break;
   case 'oldest':
   	echo $oldestdate;
   	break;
	  case 'both':
   	echo $oldestdate.'&nbsp;'.$joiningword.'&nbsp;'.$newestdate;
   	break; 
			}

}

function runningtime_howold(
		$format = 'days',
		$suffix = 'old')
	{

	global $wpdb;

	//Get the current time
	$now = gmdate("Y-m-d H:i:s",time());

	//Find the oldest post
	$oldestpost = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_date_gmt != '0000-00-00 00:00:00' AND post_status = 'publish' ORDER BY post_date_gmt LIMIT 1");
	$oldestposttime = $wpdb->get_var("SELECT post_date_gmt FROM $wpdb->posts WHERE ID = $oldestpost LIMIT 1");

	//Convert dates to timestamps
	$oldestposttime = strtotime($oldestposttime);
	$now = strtotime($now);

	//Find the length of time
	$runningtime = $now - $oldestposttime;

	switch ($format) {
			case 'days':
				$howold = $runningtime / (24*3600); //Divide by number of seconds in day
				break;
			case 'weeks':
				$howold = $runningtime / (7*86400); //Divide by number of seconds in week
				break;
			case 'months':
				$howold = $runningtime / (4*604800); //Divide by number of seconds in month
				break;
			case 'years':
				$howold = $runningtime / (52*604800); //Divide by number of seconds in year
				break;
		}
		
	//Add suffix and round number
	if ($suffix == 'old') {
			$suffix = '&nbsp;'.$format.'&nbsp;'.$suffix;
	}

	echo round($howold).'&nbsp;'.$suffix;
	
}
?>