<?php
/* 
Plugin Name: Running Time
Plugin URI:  http://labs.saruken.com/
Description: Outputs the date of the oldest post and/or the newest post. Also will output how long the your site has been running for based on the first post date.
Version: 1.1 b2
Author: Andrew Hamilton 
Author URI: http://saruken.com
Licensed under the The GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html
*/ 

function runningtime_daterange(
	$posttype = 'post',
	$dateoutput = 'both',
	$dateformat = 'd/m/y',
	$joiningword = 'to') 
	{
		 
 global $wpdb;

 //Find the newest and oldest posts		
 		if ($posttype == 'post' || $posttype == 'page'){ //Posts or Pages Only
 			$newestpost = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_date != '0000-00-00 00:00:00' AND post_status = 'publish' AND post_type = '$posttype' ORDER BY post_date DESC LIMIT 1");
 			$oldestpost = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_date != '0000-00-00 00:00:00' AND post_status = 'publish' AND post_type = '$posttype' ORDER BY post_date LIMIT 1");
 		}else{ //Both Posts and Pages
 			$newestpost = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_date != '0000-00-00 00:00:00' AND post_status = 'publish' ORDER BY post_date DESC LIMIT 1");
 			$oldestpost = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_date != '0000-00-00 00:00:00' AND post_status = 'publish' ORDER BY post_date LIMIT 1");
 		}

 		//Get the times of the newest and oldest post 		
 		$newestposttime = $wpdb->get_var("SELECT post_date FROM $wpdb->posts WHERE ID = $newestpost LIMIT 1");
 		$oldestposttime = $wpdb->get_var("SELECT post_date FROM $wpdb->posts WHERE ID = $oldestpost LIMIT 1");

 		//Convert dates to timestamps
			$oldestposttime = strtotime($oldestposttime);
			$newestposttime = strtotime($newestposttime);

			//Convert timestamp to requested date format
			$oldestdate = date($dateformat, $oldestposttime);
			$newestdate = date($dateformat, $newestposttime);
			

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
		$formatsingular = '',
		$formatplural = '')
	{

	global $wpdb;

	//Get the current time
	$now = gmdate("Y-m-d H:i:s",time());

	//Find the oldest post
	$oldestpost = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_date_gmt != '0000-00-00 00:00:00' AND post_status = 'publish' ORDER BY post_date_gmt LIMIT 1");
	$oldestposttime = $wpdb->get_var("SELECT post_date_gmt FROM $wpdb->posts WHERE ID = $oldestpost LIMIT 1");

	//Math to work out days, weeks, months and years

	$formatMath = (24*60*60);

	switch ($format) {
			case 'weeks':
				$formatMath = ($formatMath*7);
				break;
			case 'months':
				$formatMath = ($formatMath*28);
				break;
			case 'years':
				$formatMath = ($formatMath*364);
				break;
		}
	
	//Convert dates to timestamps and find the length of time between now and then
	$howold = round((strtotime($now)-strtotime($oldestposttime))/$formatMath,0);
	
	//Create output
	if ($formatsingular == '' && $formatplural == '') { //Check for manual input
		$formatsingular = substr($format, 0, -1).'&nbsp;old';
		$formatplural = $format.'&nbsp;old';
	}

	if ($howold == 1) { //Check for singular result
		$suffix = $formatsingular;
	}else{
		$suffix = $formatplural;
	}

	echo $howold.'&nbsp;'.$suffix;
	
}
?>