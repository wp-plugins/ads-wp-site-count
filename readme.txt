=== Plugin Name ===
Plugin Name: ADS-WP SITE COUNT Plugin
Version: 1.0.5
URI: http://www.ad-soft.ch/wpplugins
Tags: site counter, counter widget, hit counter, graphic counter, short code site counter
Requires at least: 3.1.0
Tested up to: 4.0.0 
Stable tag: 1.0.5
PHP Version: 5.2.9
MySql Version: 5.0.91-community
Author: adespont
Donate link: http://www.ad-soft.ch/wpplugins
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display a site counter with graphic or text on widget or on blog.

== Description ==
This plugin shows a graphic counter of hits from users. 
Display the counter as dynamic image or text on widget or blog.
You can set many options for hit and cleanup. 
This plu-in support the German and/or English language.

Dieses Plugin zeige einen graphischen Z�hler mit der anzahl zugriffe auf Ihre Seite. 
Der Z�hler erscheint als dynamische Bild oder als Text in Ihrer Seitenbar oder Blog.
Sie k�nnen diverse Optionen einstellen.
Das Plug-In ist auf Deutsch und/oder Englisch verf�gbar

== Installation ==
1. Upload Folder `ads-wp-site-count` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the Settings for cleanup IP db entrys when you need.
4. Place Widget ADS-WpSiteCount on your sidebar
5. or add [ads-wpsitecount <args, ...>] in your templates, set params on <args, ...> 

== Frequently Asked Questions ==
= How can i add a shortcode into my blog page? =
You can add [ads-wpsitecount] on your text area on blog editor. use paramters to setup you counter.
for the filename look on the plugin directory ./counter to see all your counter images.

= How can i add my one counter? =
yes, you can. put the graphic file mit a fix size per digit to the ./counter folder. 
The image must have all digits from 0 to 9 and a empty digit inside. 

= What args can i put on shotcode? =
* count=2014 -> Counter value from 0-999999999
* image='file.jpg' -> Image filename.jpg, look at the ./counter folder
* width=165 -> Counter width, 0=empty=automatic. It is recommended to set one of these parameters width or height
* height= -> Counter height, 0=empty=automatic. It is recommended to set one of these parameters width or height
* block='p' -> Issue with p, div or none
* fill='on' -> Previous vacancy with zero filled
* length=7 -> Minimum points of the counter. If the counter is greater than this will be
 adjusted automatically.   
* align='center' -> Align: left, right, center
* text='on' -> Show counter as text only
* before='since 2001' -> Before text
* after='My counter' -> After text
* docount='on' -> Turn on/off counting
* save='on' -> Save Settings as standard

= How to use the placeholder? =
On after or before text you can place placeholder.
%ip -> ip number
%image -> imagename
%count -> counter value
%[...]% -> when user logedin 
inside %[]% -> %sname, %dname, %fname, %lname
change style on css please.

== Screenshots ==
1. Main screen of WP
2. Admin panel whit settings menu
3. wpsitecount options page
4. Widget page
5. wpsitecount widget options
6. small statistic on dashboard

== Changelog ==
= 1.0.0 - 2014-07-23 = 
* Start plug-in Wp Site Count 1.0.0
* more new counters, translated en_EN

= 1.0.1 - 2014-08-08 =
* div. corrections, only count from one displayed counte, settings correted

= 1.0.2 - 2014-08-09 =
* Small statistic included on dashboard widget, 
* On widget you can display random counter image on each hour/day/month.

= 1.0.3 - 2014-08-13 =
* added new counters

= 1.0.4 - 2014-08-16 =
* correction on readme text
* added startrek counter, klingon, romulan, vulcan, added counter flipping-numbers 
* placeholder for after and before text, %ip, %image, %[.%sname,%dname,%fname,%lname.]%
* language updated

= 1.0.5 - 2014-08-16 =
* correction on function after text, missing parameter
* new placeholder %count

== Translations ==
* English: - default, always included
* German: Deutsch - immer mit dabei!
