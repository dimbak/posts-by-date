# posts-by-date


A WP plugin for sorting posts by category and date]<br/>
Contributors: dimitrisbakalidis]<br/>
Tags: shortcode, posts]<br/>
Requires at least: 6.0.2]<br/>
Tested up to: 6.0.2]<br/>

Posts-by-date plugin allows you to display posts in your posts and pages per category and date using a shortcode.

== Description ==

With Posts-by-date you can add a shortcode in pages and posts and show posts selected by date and category. You can even set a limit for the number of posts. 

== Installation ==

1. Login to your WordPress site and go to 'Plugins'
2. Click upload and select the 'posts-by-date.zip' 
3. Activate the plugin 

You can set default values for the shortcode in the 'Posts by date settings' menu found in the left hand menu bar. 

You can set the number of posts. By default 5 posts will be displayed, shortcode [number_of_posts].<br />
The category of the posts. By default no category is selected, shortcode [category].<br />
The date of the posts. By default no date is selected, shortcode [date].<br />
Add a 'Load more' button if you want to show more posts than the limit set. By default the button is not selected, shortcode [load_more].<br />

Example shortcodes

[posts-by-date number_of_posts="5" category="decoration" date="2022-08-04"]<br/>
[posts-by-date number_of_posts="5" category="decoration" date="2022-08-04" load_more="1"]<br/>
[posts-by-date number_of_posts="5" category="decoration"]
