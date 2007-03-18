=== Running Time ===

Contributors: hami
Tags: date, time, age, running-time, length, age-of-blog
Requires at least: 2.1
Tested up to: 2.1.2
Stable tag: running-time-1.0.1

A Wordpress plugin that outputs your blog's age in date range, days, weeks, months or years

== Description ==

*Running Time* is Wordpress plugin that can output details about the age of your blog using the date from your first post. 
*Running Time* can output the date of the first post, the date of the last post, the date range of your posts and the age 
of the blog measured in either days, weeks, months or years

*Running Time 1.0 has been tested in Wordpress 2.1, but it should work in Wordpress 2.0 with no problems, though untested.*

== Installation ==

This section describes how to install the plugin and get it working.

1. Download the PHP File
2. Upload *running-time.php* file into your *wp-content/plugins/* directory
3. In your *Wordpress Administration Area*, go to the *Plugins* page and click *Activate* for *Running Time*

Once you have *Running Time* installed you can add it's functions to templates in your theme. 
There are two functions in *Running Time*, `runningtime_daterange()` and `runningtime_howold()`.

== Date Range ==

To output the date range of the posts in your blog you can add the following code to your template:

	<?php runningtime_daterange()?>
	
By default this will output the following in your template:

	DD/MM/YYYY to DD/MM/YYYY
	
The first DD/MM/YYYY is the date of the first post in your blog and the second DD/MM/YYYY is the date of the most recent post in your blog.
There are some variable that can be changed in the function (defaults in bold), these and their defaults are:

	<?php runningtime_daterange(postype, dateoutput, dateformat, separator, joiningword)?>

Below is explains the settings for the functions, defaults are in **bold**
	
*	`postype` - Type of post to get the date from	
	Settings: **'post'**, 'page', 'both'

*	`dateoutput` - Which date to output	 
	Settings: **'newest'**, 'oldest', 'both'

*	`dateformat` - Format of the date output
	Settings: **'MMYY'**, 'MMYYYY', 'DDMMYY', 'DDMMYYYY', 'MMDDYY', 'MMDDYYYY'

*	`separator` - Separator between date	
	Settings: **'/'**, or anything you like

*	`joiningword` - The word between the two dates	
	Settings: **'to'**, or anything you like

== How Old? ==

To output the age of your blog, based from the age of the first post in your blog you can add the following code to your template:

	<?php runningtime_howold()?>
	
By default this will output the following in your template:

	DDD days old
	
The DDD is the number of days (rounded to the closest day) since your first blog post or page. There are some variable that can be changed in 
the function (defaults in bold), these and their defaults are:

	<?php runningtime_howold(format, suffix)?>
	
Below is explains the settings for the functions, defaults are in **bold**
	
*	`format`	- Format for the age to be measured in	
	Settings: **'days'**, 'weeks', 'months', 'years'

*	`suffix`	- The text that comes after	
	Settings: **'format old'**, or anything you like

== Known Issues ==

There is currently no distinction between singular days, weeks, months or years that the blog has been running for, so you will see '1 days' or '1 years'. This will be addressed in the next version.

If you find any bugs or want to request some additional features for future releases, please log them in this plugin's Google Code repository (both repositories are in sync with each other)
<http://code.google.com/p/wordpress-running-time/>
