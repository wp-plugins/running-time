=== Running Time ===

Contributors: hami
Tags: date, time, age, running-time, length, age-of-blog, stats, statistics,
Requires at least: 2.1
Tested up to: 2.5
Stable tag: 1.2

A Wordpress plugin that outputs your blog's age in date range, days, weeks, months or years

== Description ==

*Running Time* is Wordpress plugin that can output details about the age of your blog using the date from your first post. 
*Running Time* can output the date of the first post, the date of the last post, the date range of your posts and the age 
of the blog measured in either days, weeks, months or years

*Running Time 1.2 has been tested in Wordpress 2.5, but still works with older versions of WordPress with the old category structure. It should also work in Wordpress 2.0 with no problems, though untested.*

== Installation ==

This section describes how to install the plugin and get it working.

1. Download the PHP File
2. Upload *running-time.php* file into your *wp-content/plugins/* directory
3. In your *Wordpress Administration Area*, go to the *Plugins* page and click *Activate* for *Running Time*

Once you have *Running Time* installed you can add it's functions to templates in your theme. 
There are two functions in *Running Time*, `runningtime_daterange()` and `runningtime_howold()`.

== Changes ==

**1.2**

Version 1.2 finally adds support for the new category structure in WordPress 2.3 and higher, whilst still maintaining support for older versions of WordPress.

1.2 also fixes the problem by correctly adding the default options to your WordPress database on activation of the plugin. This fixes a problem where you could get an error on your blog until you saved the settings for the first time.

Updated admins pages to match the new admin theme in WordPress 2.5

**1.1**

The main change in 1.1, from previous versions, is the addition of an option page in the Wordpress administration pages. Here you can set the default options for the plugin and they will be saved to the database. This allows easier access to the options for the two functions if you want to alter the output. You can override the defaults by setting the options in the function call in your page template. This is covered in the function descriptions.

Some new options have been added in 1.1, these are:

1. Measuring from a specified category for Date Range and How Old functions
2. Choosing whether to measure from posts, pages or both
3. Specify a date to measure the How Old function from
4. Prefix for Date Range and How Old functions
5. Options to use Prefix and/or Suffix
6. Option to only show both dates of the Date Range function if they are different (this is date format sensative)
7. Custom Wording for measurement of How Old function, singular and plural words

In 1.1 you can also call the Running Time functions in a post or page, as well as calling them in the page template.

*Please Note:* The How Old function has had it's arguments extensively changed, any calls specifing the arguments will need to be changed in order to display the desired output. This is not the case with the Date Range function.

== Date Range ==

To output the date range of the posts in your blog you can add the following code to your template:

	`<?php runningtime_daterange()?>`
	
By default this will output something similar to the following in your template (based on the dates of your post):

	`From August 10, 2004 to January 20, 2007`
	
To change the output, you have full control over the arguments in the function in the Running Time Options. You can also override the default output by calling the function in a template and setting the agruments manually. The arguments are ordered follows:

	`<?php runningtime_daterange($posttype, $dateoutput, $dateformat, 
$usedatejoiningword, $datejoiningword, $usedateprefix, $dateprefix, 
$cat_ID, $showdifferent)?>`

Below is explains the options for the function, defaults are in **bold**
	
*	`postype` - Calculate date range from posts, pages or both
	Options: **Posts Only**, Pages Only, Posts and Pages	
	Settings: **'post'**, 'page', 'both'

*	`dateoutput` - Dates to output either newest, oldest or both
	Options: **Oldest and Newest**, Oldest Post Only, Newest Post Only
	Settings: **'both'**, 'oldest', 'newest'

*	`dateformat` - Format for the output of the date using PHP date syntax
	Options: **F j, Y**, or PHP date strings <http://www.php.net/date>
	Settings: **'F j, Y'**, or PHP date strings

*	`usedatejoiningword` - Use joining word between the dates
	Options: **Checked**, Unchecked
	Settings: **'true'**, 'false'

*	`datejoiningword` - The wording or symbol between the dates
	Options: **to**, or anything you want
	Settings: **'to'**, or anything you want

*	`usedateprefix` - Use prefix before the date range
	Options: **Checked**, Unchecked
	Settings: **'true'**, 'false'

*	`dateprefix` - The wording or symbol before the dates
	Options: **From**, or anything you want
	Settings: **'From'**, or anything you want

*	`cat_ID` - Default category to calculate date range from
	Options: **All Categories**, or any of your categories
	Settings: **'all'**, or any of your category IDs

*	`showdifferent` - Only show dates if they are different
	Options: **Checked**, Unchecked
	Settings: **'true'**, 'false'

You can also call the function by placing `<!--runningtime_daterange-->` in the content of your page or post. This will output the default output of the date range function, currently there is no way to override the default options when placing the function in your page or post.

== How Old? ==

To output the age of your blog, based from the age of the first post in your blog you can add the following code to your template:

	`<?php runningtime_howold()?>`
	
By default this will output something similar to the following in your template:

	`Started 124 days ago`
	
The measurement of the number of days is the amount of days (rounded down to the closest day) since your first blog post, page or specified date. There are some variables that can be changed in the function (defaults in bold), these and their defaults are:

	`<?php runningtime_howold($ageformat, $customwording, $ageformatsingular, 
$ageformatplural, $howoldprefix, $howoldsuffix, $prefixsuffix, $posttype_howold, 
$cat_ID_howold, $specified_date_howold)?>`
	
Below is explains the settings for the functions, defaults are in **bold**
	
*	`ageformat` - Age measurement in days, weeks, months or years
	Options: **Days**, Weeks, Months, Years
	Settings: **'days'**, 'weeks', 'months', 'years'

*	`customwording` - Use custom wording instead of `ageformat`
	Options: **Checked**, Unchecked
	Settings: **'true'**, 'false'

*	`ageformatsingular `	- Custom word to use for singular measurement
	Options: **NULL**, or anything you like
	Settings: **NULL**, or anything you like

*	`ageformatplural ` - Custom word to use for plural measurement
	Options: **NULL**, or anything you like
	Settings: **NULL**, or anything you like

*	`howoldprefix `	- Wording before age measurement
	Options: **Started**, or anything you like
	Settings: **'Started'**, or anything you like

*	`howoldsuffix `	- Wording before age measurement
	Options: **ago**, or anything you like
	Settings: **'ago'**, or anything you like

*	`prefixsuffix `	- Choose whether to use the prefix, suffix, both or none
	Options: **Prefix and Suffix**, Prefix Only, Suffix Only, Use Neither
	Settings: **'both'**,  'prefix', 'suffix', 'none'

*	`posttype_howold `	- Choose whether to measure age of posts, pages, both or a specified date
	Options: **Post**, Page, Both, Specified Date
	Settings: **'post'**,  'page', 'both', 'date'

*	`cat_ID_howold` - Default category to calculate age from
	Options: **All Categories**, or any of your categories
	Settings: **'all'**, or any of your category IDs

*	`specified_date_howold` - Date to measure age of blog from
	Options: **1st January 2007**, or any date of your choice
	Settings: **'1st January 2007'**, or any date of your choice

You can also call the function by placing `<!--runningtime_howold-->` in the content of your page or post. This will output the default output of the how old function, currently there is no way to override the default options when placing the function in your page or post.

== Example Uses ==

Here are a couple of examples of how to use Running Time away from the defaults

*Road Trip or Holiday*

You could use Running Time as a dynamic header for a category archive of your blog chronicling a Road Trip or a Holiday. This would involve all of the posts for your Road Trip to have the own category. In the category template for your Road Trip use the follow function call:

	`<?php runningtime_daterange('post', 'both' 'd/m/y', 'true', 'thru', 'true', 'My Road Trip', 'n', 'true')?>`

n is the ID category that contains your Road Trip posts. This function call will output the following based on the dates of your post:

	`My Road Trip 01/02/07 thru 01/03/07`

That function call will also only show the first date if it is different on to the first date with the output beinging `My Road Trip 01/02/07` and will never output `My Road Trip 01/02/07 thru 01/02/07`.

*Footer Copyright*

Another use for Running Time is to have a dynamic copyright in the footer of your blogs template. This can be achieved by using the following function call in your footer.php

	`<?php runningtime_daterange('post', 'both', 'Y', 'true', '-', 'true', '&copy;', '', 'true'); ?>`

This will output the following based on the dates of your posts:

	`© 2006 - 2007`

If the two years are the same the output will output the following:

	`© 2007`

If you have any other examples you would like to share with other users of the plugin, please email them to labs@saruken.com

== Screenshots ==

1. Options for Date Range Function
2. Options for How Old Function

== Known Issues ==

Calling the functions in a page or post will result in the default output, there is currently no options that can be overridden. The ability to override the defaults in a page or post is being looked into.

If you find any bugs or want to request some additional features for future releases, please log them in this plugin's Google Code repository (both repositories are in sync with each other)
<http://code.google.com/p/wordpress-running-time/>
