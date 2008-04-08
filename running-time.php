<?php
/* 
Plugin Name: Running Time
Plugin URI:  http://code.andrewhamilton.net/wordpress/plugins/running-time/
Description: Outputs the date of the oldest post and/or the newest post. Also will output how long the your site has been running for based on the first post date or a specified start date.
Version: 1.2
Author: Andrew Hamilton 
Author URI: http://andrewhamilton.net
Licensed under the The GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html
*/

//----------------------------------------------------------------------------
//		SETUP FUNCTIONS
//----------------------------------------------------------------------------

register_activation_hook(__FILE__,'runningtime_setup_options');

$runningtime_opt = get_option('runningtime_options');
$runningtime_version = get_option('runningtime_version');
$runningtime_this_version = '1.2';

function runningtime_add_options_page() {
    if (function_exists('add_options_page')) {
		add_options_page('Running Time', 'Running Time', 8, basename(__FILE__), 'runningtime_options_page');
    }
}

function runningtime_setup_options(){
	global $runningtime_opt, $runningtime_version, $runningtime_this_version;
	
	// Check the version of Running Time
	if (empty($runningtime_version)){
		add_option('runningtime_version', $runningtime_this_version, 'Running Time Wordpress Plugin Version');
	} elseif ($runningtime_version != $runningtime_this_version) {
		update_option('runningtime_version', $runningtime_this_version);
	}
	
	// Setup Default Options Array
		$optionarray_def = array(
			'posttype' => 'post',
			'dateoutput' => 'both',
			'dateformat' => 'F j, Y',
			'dateprefix' => 'From',
			'datejoiningword' => 'to',
			'usedateprefix' => TRUE,
			'usedatejoiningword' => TRUE,
			'cat_ID' => 'all',
			'showifdifferent' => FALSE,
			'ageformat' => 'days',
			'customwording' => FALSE,
			'ageformatsingular' => '',
			'ageformatplural' => '',
			'howoldprefix' => 'Started',
			'howoldsuffix' => 'ago',
			'prefixsuffix' => 'both',
			'posttype_howold' => 'both',
			'cat_ID_howold' => 'all',
			'specified_date_howold' => '1st April 1976'
		);
		
	if (empty($runningtime_opt)){ //If there aren't already options for Running Time
		add_option('runningtime_options', $optionarray_def, 'Running Time Wordpress Plugin Options');
	}	
	
}

//Detect WordPress version to add compatibility with 2.3 or higher
$wpversion_full = get_bloginfo('version');
$wpversion = preg_replace('/([0-9].[0-9])(.*)/', '$1', $wpversion_full); //Boil down version number to X.X

//----------------------------------------------------------------------------
//		PLUGIN FUNCTIONS
//----------------------------------------------------------------------------

//----------------------------------------------------------------------------
//	Date Range Template Function
//----------------------------------------------------------------------------

function runningtime_daterange(
	$posttype = '',
	$dateoutput = '',
	$dateformat = '',
	$usedatejoiningword = '',
	$datejoiningword = '',
	$usedateprefix = '',
	$dateprefix = '',
	$cat_ID = '',
	$specified_date_howold = ''
	){

	//Call Core Date Range Function
	$output = runningtime_daterange_core(
											$posttype, 
											$dateoutput, 
											$dateformat, 
											$usedatejoiningword, 
											$datejoiningword, 
											$usedateprefix, 
											$dateprefix,
											$cat_ID,
											$specified_date_howold
											);

	//Display Output										
	echo $output;

}

//----------------------------------------------------------------------------
//	How Old Template Function
//----------------------------------------------------------------------------

function runningtime_howold(
	$ageformat = '',
	$customwording = '',
	$ageformatsingular = '',
	$ageformatplural = '',
	$howoldprefix = '',
	$howoldsuffix = '',
	$prefixsuffix = '',		
	$posttype = '',
	$cat_ID = '',
	$showifdifferent = ''
	){

	//Call Core Date Range Function
	$output = runningtime_howold_core(
											$ageformat,
											$customwording,
											$ageformatsingular,
											$ageformatplural,
											$howoldprefix,
											$howoldsuffix,
											$prefixsuffix,		
											$posttype,
											$cat_ID,
											$showifdifferent
											);
											
	//Display Output										
	echo $output;

	}

//----------------------------------------------------------------------------
//	Running Time Text Function 
//----------------------------------------------------------------------------


function runningtime_text ($text){

	//Look for matching Date Range HTML comment
	$text = preg_replace('/\<!--runningtime_daterange\-->/', runningtime_daterange_core(), $text);
	//$text = preg_replace("#(<!--[ ]*runningtime_daterange[ ]*-->)#ismeU", "runningtime_daterange_core()", $text);
	//Look for matching How Old HTML comment
	$text = preg_replace('/\<!--runningtime_howold\-->/', runningtime_howold_core(), $text);
	//$text = preg_replace("#(<!--[ ]*runningtime_howold[ ]*\-->)#ismeU", "runningtime_howold_core()", $text);
	return $text;
	
}

//----------------------------------------------------------------------------
//	Date Range Core Function
//----------------------------------------------------------------------------

function runningtime_daterange_core(
	$posttype = '',
	$dateoutput = '',
	$dateformat = '',
	$usedatejoiningword = '',
	$datejoiningword = '',
	$usedateprefix = '',
	$dateprefix = '',
	$cat_ID = '',
	$showifdifferent = ''
	){
		 
 global $runningtime_opt, $wpdb, $wpversion;

	//Setup Variables, check for manual setting otherwise use defaults
	if (empty($cat_ID)) {$cat_ID = $runningtime_opt['cat_ID'];}
	if (empty($posttype)) {$posttype = $runningtime_opt['posttype'];}
	if (empty($dateoutput)) {$dateoutput = $runningtime_opt['dateoutput'];}
	if (empty($dateformat)) {$dateformat = $runningtime_opt['dateformat'];}
	if (empty($datejoiningword)) {$datejoiningword = $runningtime_opt['datejoiningword'];}
	if (empty($dateprefix)) {$dateprefix = $runningtime_opt['dateprefix'];}
	
	if ($runningtime_opt['usedatejoiningword'] == FALSE && $usedatejoiningword == '') 
	{
		$usedatejoiningword = 'false';
	}
	elseif ($runningtime_opt['usedatejoiningword'] == TRUE && $usedatejoiningword == '') 
	{
		$usedatejoiningword = 'true';
	}

	if ($runningtime_opt['usedateprefix'] == FALSE && $usedateprefix == '') 
	{
		$usedateprefix = 'false';
	}
	elseif ($runningtime_opt['usedateprefix'] == TRUE && $usedateprefix == '') 
	{
		$usedateprefix = 'true';
	}

	if ($runningtime_opt['showifdifferent'] == FALSE && $showifdifferent == '')
	{
		$showifdifferent = 'false';
	}
	elseif ($runningtime_opt['showifdifferent'] == TRUE && $showifdifferent == '')
	{
		$showifdifferent = 'true';
	}

 	//SQL Query Componants
	if ($cat_ID == 'all')
	{ //Check for specified category
		$sqlquery_table_prefix_posts = '';
		$sqlquery_table_prefix_cat = '';
		$sqlquery_select = "SELECT ID, post_date ";
		$sqlquery_from = "FROM $wpdb->posts ";
		$sqlquery_where = "WHERE post_date != '0000-00-00 00:00:00' AND post_status = 'publish' ";
	} 
	else 
	{ //Specific Category
		$sqlquery_table_prefix_posts = 'wposts';
		$sqlquery_table_prefix_cat = 'wpostcat';
		$sqlquery_select = "SELECT ".$sqlquery_table_prefix_posts.".ID, ".$sqlquery_table_prefix_posts.".post_date ";
		
		if ($wpversion >= 2.3)
		{ //If WordPress 2.3 or greater with new category structure
			$sqlquery_from = "FROM $wpdb->posts ".$sqlquery_table_prefix_posts.", $wpdb->term_relationships ".$sqlquery_table_prefix_cat;
			$sqlquery_where = "WHERE ".$sqlquery_table_prefix_posts.".ID = ".$sqlquery_table_prefix_cat.".object_id AND ".$sqlquery_table_prefix_cat.".term_taxonomy_id = $cat_ID AND post_date != '0000-00-00 00:00:00' AND post_status = 'publish' ";
		} 
		else 
		{ //For Versions of WordPress 2.2 and below
			$sqlquery_from = "FROM $wpdb->posts ".$sqlquery_table_prefix_posts.", $wpdb->post2cat ".$sqlquery_table_prefix_cat;
			$sqlquery_where = "WHERE ".$sqlquery_table_prefix_posts.".ID = ".$sqlquery_table_prefix_cat.".post_id AND ".$sqlquery_table_prefix_cat.".category_id = $cat_ID AND post_date != '0000-00-00 00:00:00' AND post_status = 'publish' ";
		}	
	}
		
	$sqlquery_posttype = "AND ".$sqlquery_table_prefix_posts."post_type = '$posttype' ";
	$sqlquery_order_postdate = "ORDER BY ".$sqlquery_table_prefix_posts."post_date LIMIT 1";
	$sqlquery_order_postdate_desc = "ORDER BY ".$sqlquery_table_prefix_posts."post_date DESC LIMIT 1";

	//Construct SQL Query Start
	$sqlquery_start = $sqlquery_select.$sqlquery_from.$sqlquery_where;

	//Find the newest and oldest posts		
	if ($posttype == 'post' || $posttype == 'page')
	{ //Posts or Pages Only
		$oldestpost = $wpdb->get_results("$sqlquery_start"."$sqlquery_posttype"."$sqlquery_order_postdate", OBJECT);
		$newestpost = $wpdb->get_results("$sqlquery_start"."$sqlquery_posttype"."$sqlquery_order_postdate_desc", OBJECT);
		 
	}
	else
	{ //Both Posts and Pages
		$oldestpost = $wpdb->get_results("$sqlquery_start"."$sqlquery_order_postdate", OBJECT);
		$newestpost = $wpdb->get_results("$sqlquery_start"."$sqlquery_order_postdate_desc", OBJECT);
	}

	//Convert dates to timestamps
	$oldestposttime = strtotime($oldestpost[0]->post_date);
	$newestposttime = strtotime($newestpost[0]->post_date);

	//Convert timestamp to requested date format
	$oldestdate = date($dateformat, $oldestposttime);
	$newestdate = date($dateformat, $newestposttime);			

	//Construct output
	
	if ($showifdifferent == 'true' && $oldestdate == $newestdate)
	{ //Check to see if 'Show if Different' is set and the dates are the same	
		$output = $newestdate;			
	} 
	else 
	{
		
		if ($dateoutput == 'oldest' || $dateoutput == 'both')
		{
			$output = $oldestdate;
		} 
		elseif ($dateoutput == 'newest') 
		{
			$output = $newestdate;		
		}
			
		if ($usedatejoiningword == 'true' && $dateoutput == 'both') 
		{
			$output .= '&nbsp;'.$datejoiningword.'&nbsp;'.$newestdate;
		} 
		elseif ($usedatejoiningword == 'false' && $dateoutput == 'both') 
		{
			$output .= '&nbsp;'.$newestdate;
		}	
		
	}
	
	if ($usedateprefix == 'true' && $dateoutput == 'both') 
	{
		$output = $dateprefix.'&nbsp;'.$output;
	}
		
	return $output;

}

//----------------------------------------------------------------------------
//	How Old Core Function
//----------------------------------------------------------------------------

function runningtime_howold_core(
		$ageformat = '',
		$customwording = '',
		$ageformatsingular = '',
		$ageformatplural = '',
		$howoldprefix = '',
		$howoldsuffix = '',
		$prefixsuffix = '',
		$posttype = '',
		$cat_ID = '',
		$specified_date_howold = ''
){

	global $runningtime_opt, $wpdb;

	//Setup Variables, check for manual setting otherwise use defaults
	if (empty($cat_ID)) {$cat_ID = $runningtime_opt['cat_ID_howold'];}
	if (empty($posttype)) {$posttype = $runningtime_opt['posttype_howold'];}
	if (empty($ageformat)) {$howoldprefix = $runningtime_opt['howoldprefix'];}
	if (empty($prefixsuffix)) {$prefixsuffix = $runningtime_opt['prefixsuffix'];}

	if ($runningtime_opt['customwording'] == FALSE && $customwording == '')
	{
		$customwording = 'false';
	}
	elseif ($runningtime_opt['customwording'] == TRUE && $customwording == '')
	{
		$usedatejoiningword = 'true';
		$ageformatsingular = $runningtime_opt['ageformatsingular'];
		$ageformatplural = $runningtime_opt['ageformatplural'];
	}

	//Get the current time
	$now = gmdate("Y-m-d H:i:s",time());

 	//Check for user specifed date
 	if ($runningtime_opt['posttype_howold'] == 'date'){
 		$oldestpost = $runningtime_opt['specified_date_howold'];
	} else { //Setup SQL Query Componants and find oldest post
		if ($cat_ID == 'all'){//Check for specified category
				$sqlquery_table_prefix_posts = '';
				$sqlquery_table_prefix_cat = '';
				$sqlquery_select = "SELECT ID, post_date ";
				$sqlquery_from = "FROM $wpdb->posts ";
				$sqlquery_where = "WHERE post_date != '0000-00-00 00:00:00' AND post_status = 'publish' ";
			} else {//Specific Category
				$sqlquery_table_prefix_posts = 'wposts.';
				$sqlquery_table_prefix_cat = 'wpostcat.';
				$sqlquery_select = "SELECT ".$sqlquery_table_prefix_posts."ID, ".$sqlquery_table_prefix_posts."post_date ";
				$sqlquery_from = "FROM $wpdb->posts wposts, $wpdb->post2cat wpostcat ";
				$sqlquery_where = "WHERE ".$sqlquery_table_prefix_posts."ID = ".$sqlquery_table_prefix_cat."post_id AND ".$sqlquery_table_prefix_cat."category_id = $cat_ID AND ".$sqlquery_table_prefix_posts."post_date != '0000-00-00 00:00:00' AND ".$sqlquery_table_prefix_posts."post_status = 'publish' ";	
			}
				
			$sqlquery_posttype = "AND ".$sqlquery_table_prefix_posts."post_type = '$posttype' ";
			$sqlquery_order_postdate = "ORDER BY ".$sqlquery_table_prefix_posts."post_date LIMIT 1";
		
			//Construct SQL Query Start
			$sqlquery_start = $sqlquery_select.$sqlquery_from.$sqlquery_where;
			
			//Find oldest post		
			if ($posttype == 'post' || $posttype == 'page'){ //Posts or Pages Only
				$oldestpost = $wpdb->get_results("$sqlquery_start"."$sqlquery_posttype"."$sqlquery_order_postdate", OBJECT);		 
			}else{ //Both Posts and Pages
				$oldestpost = $wpdb->get_results("$sqlquery_start"."$sqlquery_order_postdate", OBJECT);
			}
		
	}

	//Math to work out days, weeks, months and years
	$formatMath = (24*60*60);//One day is 86400 seconds

	switch ($ageformat) {
			case 'weeks':
				$formatMath = ($formatMath*7);//One week is 604800 seconds
				break;
			case 'months':
				$formatMath = ($formatMath*28);//One month is 2419200 seconds
				break;
			case 'years':
				$formatMath = ($formatMath*364);//One year is 31449600 seconds
				break;
		}
	
	//Check for specified date to measure from, then convert dates to timestamps and find the length of time between now and then
	
	if ($runningtime_opt['posttype_howold'] == 'date'){
		$howold = round((strtotime($now)-strtotime($oldestpost))/$formatMath,0);
	} else {
		$howold = round((strtotime($now)-strtotime($oldestpost[0]->post_date))/$formatMath,0);
	}

	//Create output
	$output = $howold.'&nbsp;';
	
	if ($customwording == 'false') {
		$ageformatsingular = substr($ageformat, 0, -1);
		$ageformatplural = $ageformat;
	}
	
	if ($howold <= 1) { //Check for result of 1 or 0
		$output .= $ageformatsingular;
	}else{
		$output .= $ageformatplural;
	}

	if ($prefixsuffix == 'both') {
		$output = $howoldprefix.'&nbsp;'.$output.'&nbsp;'.$howoldsuffix;
	} elseif ($prefixsuffix == 'prefix') {
		$output = $howoldprefix.'&nbsp;'.$output;
	} elseif ($prefixsuffix == 'suffix') {
		$output .= '&nbsp;'.$howoldsuffix;	
	}
	
	return $output;
	
}

//----------------------------------------------------------------------------
//		ADMIN OPTION PAGE FUNCTIONS
//----------------------------------------------------------------------------

function runningtime_options_page() {
	global $wpversion_full, $wpversion, $wpdb, $runningtime_version, $runningtime_this_version;

		if (isset($_POST['submit']) ) {
			
		// Options Array Update
		$optionarray_update = array (
			'posttype' => $_POST['posttype'],
			'dateoutput' => $_POST['dateoutput'],
			'dateformat' => $_POST['dateformat'],
			'dateprefix' => $_POST['dateprefix'],
			'datejoiningword' => $_POST['datejoiningword'],
			'usedateprefix' => $_POST['usedateprefix'],
			'usedatejoiningword' => $_POST['usedatejoiningword'],
			'cat_ID' => $_POST['cat_ID'],
			'showifdifferent' => $_POST['showifdifferent'],
			'ageformat' => $_POST['ageformat'],
			'customwording' => $_POST['customwording'],
			'ageformatsingular' => $_POST['ageformatsingular'],
			'ageformatplural' => $_POST['ageformatplural'],
			'howoldprefix' => $_POST['howoldprefix'],
			'howoldsuffix' => $_POST['howoldsuffix'],
			'prefixsuffix' => $_POST['prefixsuffix'],
			'posttype_howold' => $_POST['posttype_howold'],
			'cat_ID_howold' => $_POST['cat_ID_howold'],
			'specified_date_howold' => $_POST['specified_date_howold']
		);
		
		update_option('runningtime_options', $optionarray_update);
		
		if ($runningtime_version != $runningtime_this_version) {
			update_option('runningtime_version', $runningtime_this_version);
		}
		
		}
		
	// Get Options
		$optionarray_def = get_option('runningtime_options');

	// Setup Default Post Listing Options - Which category to calculate from
		$cat_options = '';
	
		if ($wpversion >= 2.3){ //If WordPress 2.3 or greater with new category structure
			$terms = $wpdb->get_results("SELECT * FROM $wpdb->terms ORDER BY name");
			
			foreach ($terms as $term) {
			if ($term->term_ID == $optionarray_def['cat_ID']) {
					$selected = 'selected="selected"';
			} elseif ($term->term_ID == $optionarray_def['cat_ID_howold']) {
					$selected_howold = 'selected="selected"';
			} else {
					$selected = '';
					$selected_howold = '';
			}
			
			$category_list .= "\n\t<option value='$term->term_ID' $selected>$term->name</option>";
			$category_list_howold .= "\n\t<option value='$term->term_ID' $selected_howold>$term->name</option>";
			
			}
		
		} else { //For Versions of WordPress 2.2 and below
			$categories = $wpdb->get_results("SELECT * FROM $wpdb->categories ORDER BY cat_name");
			
			foreach ($categories as $category) {
			if ($category->cat_ID == $optionarray_def['cat_ID']) {
					$selected = 'selected="selected"';
			} elseif ($category->cat_ID == $optionarray_def['cat_ID_howold']) {
					$selected_howold = 'selected="selected"';
			} else {
					$selected = '';
					$selected_howold = '';
			}
			
			$category_list .= "\n\t<option value='$category->cat_ID' $selected>$category->cat_name</option>";
			$category_list_howold .= "\n\t<option value='$category->cat_ID' $selected_howold>$category->cat_name</option>";

			}
		
		}

	 if ($optionarray_def['cat_ID'] == 'all'){
					$selected_all = 'selected="selected"';
		} elseif ($optionarray_def['cat_ID_howold'] == 'all') {
				$selected_all_howold = 'selected="selected"';
		} else {
				$selected_all = '';
				$selected_all_howold = '';
		}
			
		$cat_options = "\n\t<option value='all'$selected_all>All Categories</option>".$category_list;
		$cat_options_howold = "\n\t<option value='all'$selected_all>All Categories</option>".$category_list_howold;

	// Setup Post Type Options
		$posttypes = array(
		'Posts Only' => 'post',
		'Pages Only' => 'page',
		'Pages and Posts' => 'both'
		);
		
		$posttypes_howold = $posttypes;
		$posttypes_howold['Specified Date'] = 'date';
		
		foreach ($posttypes as $option => $value) {
			if ($value == $optionarray_def['posttype']) {
					$selected = 'selected="selected"';
			} else {
					$selected = '';
			}
			
			$postpage_options .= "\n\t<option value='$value' $selected>$option</option>";
		}
		
		foreach ($posttypes_howold as $option => $value) {
			if ($value == $optionarray_def['posttype_howold']) {
					$selected_howold = 'selected="selected"';
			} else {
					$selected_howold = '';
			}

			$postpage_options_howold .= "\n\t<option value='$value' $selected_howold>$option</option>";
		}

	// Setup Date Output Options
		$dateoutput_types = array(
		'Oldest and Newest' => 'both',
		'Oldest Date Only' => 'oldest',
		'Newest Date Only' => 'newest'
		);

		foreach ($dateoutput_types as $option => $value) {
			if ($value == $optionarray_def['dateoutput']) {
					$selected = 'selected="selected"';
			} else {
					$selected = '';
			}
			$dateoutput_options .= "\n\t<option value='$value' $selected>$option</option>";;
		}

	// Setup Age Format Options
		$ageformat_types = array(
		'Days' => 'days',
		'Weeks' => 'weeks',
		'Months' => 'months',
		'Years' => 'years'
		);

		if ($optionarray_def['customwording'] == FALSE) {
			$customwording_disabled = 'disabled="disabled"';
		}else{
			$customwording_disabled = NULL;
		}

		foreach ($ageformat_types as $option => $value) {
			if ($value == $optionarray_def['ageformat']) {
					$selected = 'selected="selected"';
			} else {
					$selected = '';
			}
			$ageformat_options .= "\n\t<option value='$value' $selected>$option</option>";;
		}

	// Setup Prefix and Suffix Options
		$prefixsuffix_types = array(
		'Prefix and Suffix' => 'both',
		'Prefix Only' => 'prefix',
		'Suffix Only' => 'suffix',
		'Use Neither' => 'none'
		);

		foreach ($prefixsuffix_types as $option => $value) {
			if ($value == $optionarray_def['prefixsuffix']) {
					$selected = 'selected="selected"';
			} else {
					$selected = '';
			}
			$prefixsuffix_options .= "\n\t<option value='$value' $selected>$option</option>";;
		}

	// Admin Options Page
	
?>
<div class="wrap">
	<h2>Running Time Options</h2>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF'] . '?page=' . basename(__FILE__); ?>&updated=true">
	<fieldset class="options" style="border: none">
	<h3>Date Range</h3>
	<p>
	Settings for controlling the output for the date range of your blog, from the first page or post to the last page or post. To use this functionn, 
	call the <code>runningtime_daterange();</code> function in a page template or place <code>&lt;!--runningtime_daterange--&gt;</code> in the content of your page or post.
	</p>
	<table width="100%" class="form-table">
			<tr valign="center"> 
				<th width="150px" scope="row">Calculate From:</th> 
				<td width="150px"><select name="posttype" id="posttype_inp"><?php echo $postpage_options ?></select></td>
				<td style="color: #555; font-size: .85em;">Calculate date range from posts, pages or both</td> 
			</tr>
			<tr valign="center"> 
				<th width="150px" scope="row">Post Category:</th> 
				<td width="150px"><select name="cat_ID" id="cat_ID_inp" ><?php echo $cat_options ?></select></td>
				<td style="color: #555; font-size: .85em;">Default category to calculate date range from</td> 
			</tr>
			<tr valign="center"> 
				<th width="150px" scope="row">Date Format:</th> 
				<td><input type="text" id="dateformat_inp" name="dateformat" value="<?php echo $optionarray_def['dateformat']; ?>" size="15" /></td>
				<td><span style="color: #555; font-size: .85em;">Format for the output of the date using <a href="http://www.php.net/date">PHP date syntax</a></span></td> 
			</tr>
			<tr valign="center"> 
				<th width="150px" scope="row">Dates to Use:</th> 
				<td width="150px"><select name="dateoutput" id="dateoutput_inp" ><?php echo $dateoutput_options ?></select></td>
				<td style="color: #555; font-size: .85em;">Dates to output either newest, oldest or both</td> 
			</tr>
			<tr valign="center"> 
				<th width="150px" scope="row">Show If Different: </th> 
				<td width="150px"><input name="showifdifferent" type="checkbox" id="showifdifferent_inp" value="1" <?php checked('1', $optionarray_def['showifdifferent']); ?>"  /></td>
				<td><span style="color: #555; font-size: .85em;">Only show boths dates if they are different from each other <em>(format sensitive)</em></span></td> 
			</tr>	
			<tr valign="center"> 
				<th width="150px" scope="row">Use Prefix:</th> 
				<td width="150px"><input name="usedateprefix" type="checkbox" id="usedateprefix_inp" value="1" <?php checked('1', $optionarray_def['usedateprefix']); ?>"  /></td>
				<td><span style="color: #555; font-size: .85em;">Use prefix before the date range</span></td> 
			</tr>
			<tr valign="center"> 
				<th width="150px" scope="row">Prefix:</th> 
				<td><input type="text" id="dateprefix_inp" name="dateprefix" value="<?php echo $optionarray_def['dateprefix']; ?>" size="15" /></td>
				<td><span style="color: #555; font-size: .85em;">The wording or symbol before the dates</span></td> 
			</tr>
			<tr valign="center"> 
				<th width="150px" scope="row">Use Joining Word:</th> 
				<td width="150px"><input name="usedatejoiningword" type="checkbox" id="usedatejoiningword_inp" value="1" <?php checked('1', $optionarray_def['usedatejoiningword']); ?>"  /></td>
				<td><span style="color: #555; font-size: .85em;">Use joining word between the dates</span></td> 
			</tr>
			<tr valign="center"> 
				<th width="150px" scope="row">Joining Word:</th> 
				<td><input type="text" id="datejoiningword_inp" name="datejoiningword" value="<?php echo $optionarray_def['datejoiningword']; ?>" size="15" /></td>
				<td><span style="color: #555; font-size: .85em;">The wording or symbol between the dates</span></td> 
			</tr>		
	</table>
	<p />
	You can override these settings by calling the function using the following arguments in your template:<br />
	<small><code>runningtime_daterange($posttype, $dateoutput, $dateformat, $usedatejoiningword, $datejoiningword, $usedateprefix, $dateprefix, $cat_ID, $showifdifferent);</code></small>
	</p>
	For example uses and function calls, please visit the plugin's page on <a href="http://wordpress.org/extend/plugins/running-time/other_notes/">Wordpress.org</a>.
	</p>
	</fieldset>	
	<fieldset class="options" style="border: none">
	<h3>How Old</h3>
	<p>
	Settings for controlling the output for the age of your blog, measured in either days, weeks, months or years. To use this functionn, 
	call the <code>runningtime_howold();</code> function in a page template or place <code>&lt;!--runningtime_howold--&gt;</code> in the content of your page or post.
	</p>
	<table width="100%" class="form-table">
			<tr valign="center"> 
				<th width="150px" scope="row">Calculate From:</th> 
				<td width="150px"><select name="posttype_howold" id="posttype_howold_inp"><?php echo $postpage_options_howold ?></select></td>
				<td style="color: #555; font-size: .85em;">Calculate age from posts, pages, both or a specified date</td> 
			</tr>
			<tr valign="center"> 
				<th width="150px" scope="row">Specified Date: </th> 
				<td><input type="text" id="specified_date_howold_inp" name="specified_date_howold" value="<?php echo $optionarray_def['specified_date_howold']; ?>" size="15" /></td>
				<td><span style="color: #555; font-size: .85em;">Specified date to measure age from <em>(e.g. 1st April 1976)</em></span></td>
			</tr>
			<tr valign="center"> 
				<th width="150px" scope="row">Post Category:</th> 
				<td width="150px"><select name="cat_ID_howold" id="cat_ID_howold_inp" ><?php echo $cat_options_howold ?></select></td>
				<td style="color: #555; font-size: .85em;">Default category to calculate age from</td> 
			</tr>
			<tr valign="center"> 
				<th width="150px" scope="row">Measure Age In:</th> 
				<td width="150px"><select name="ageformat" id="ageformat_inp"><?php echo $ageformat_options ?></select></td>
				<td style="color: #555; font-size: .85em;">Age measurement in days, weeks, months or years</td> 
			</tr>
			<tr valign="center"> 
				<th width="150px" scope="row">Use Custom Wording:</th> 
				<td width="150px"><input name="customwording" type="checkbox" id="customwording_inp" value="1" <?php checked('1', $optionarray_def['customwording']); ?>"  /></td>
				<td><span style="color: #555; font-size: .85em;">Use custom wording instead of <?php echo $optionarray_def['ageformat'] ?></span></td> 
			</tr>
			<tr valign="center"> 
				<th width="150px" scope="row">Singular Word:</th> 
				<td><input type="text" id="ageformatsingular_inp" name="ageformatsingular" value="<?php echo $optionarray_def['ageformatsingular']; ?>" size="15" /></td>
				<td><span style="color: #555; font-size: .85em;">Custom word to use for singular measurement</span></td> 
			</tr>
			<tr valign="center"> 
				<th width="150px" scope="row">Plural Word:</th> 
				<td><input type="text" id="ageformatplural_inp" name="ageformatplural" value="<?php echo $optionarray_def['ageformatplural']; ?>" size="15" /></td>
				<td><span style="color: #555; font-size: .85em;">Custom word to use for plural measurement</span></td> 
			</tr>
			<tr valign="center"> 
				<th width="150px" scope="row">Prefix/Suffix:</th> 
				<td width="150px"><select name="prefixsuffix" id="prefixsuffix_inp" ><?php echo $prefixsuffix_options; ?></select></td>
				<td style="color: #555; font-size: .85em;">Choose whether to use a prefix, suffix, both or neither</td> 
			</tr>
			<tr valign="center"> 
				<th width="150px" scope="row">Prefix:</th> 
				<td><input type="text"  name="howoldprefix" id="howoldprefix_inp" value="<?php echo $optionarray_def['howoldprefix']; ?>" size="15" /></td>
				<td><span style="color: #555; font-size: .85em;">Wording before age measurement.</span></td> 
			</tr>
			<tr valign="center"> 
				<th width="150px" scope="row">Suffix:</th> 
				<td><input type="text" name="howoldsuffix" id="howoldsuffix_inp" value="<?php echo $optionarray_def['howoldsuffix']; ?>" size="15" /></td>
				<td><span style="color: #555; font-size: .85em;">Wording after age measurement.</span></td> 
			</tr>
	</table>
	<p />
	You can override these settings by calling the function using the following arguments in your template:<br />
	<small><code>runningtime_howold($ageformat, $customwording, $ageformatsingular, $ageformatplural, $howoldprefix, $howoldsuffix, $prefixsuffix, $posttype_howold, $cat_ID_howold, $specified_date_howold);</code></small>
	</p>
	For example uses and function calls, please visit the plugin's page on <a href="http://wordpress.org/extend/plugins/running-time/other_notes/">Wordpress.org</a>.
	</fieldset>
	<p />
	<div class="submit">
		<input type="submit" name="submit" value="<?php _e('Update Options') ?> &raquo;" />
	</div>
	</form>
	</div> <!--/#wrap-->
<?php
}

//----------------------------------------------------------------------------
//		WORDPRESS FILTERS AND ACTIONS
//----------------------------------------------------------------------------

add_filter('the_content','runningtime_text');
add_action('admin_menu', 'runningtime_add_options_page');

?>