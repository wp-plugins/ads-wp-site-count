=== Plugin Name ===
Plugin Name: ADS-WP SITE COUNT Plugin
Version: 1.0.2
URI: http://www.ad-soft.ch/wpplugins
Tags: site counter, counter widget, hit counter, graphic counter, short code site counter
Requires at least: 3.1.0
Tested up to: 4.0.0 beta
Stable tag: 1.0.2
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

Dieses Plugin zeige einen graphischen Zähler mit der anzahl zugriffe auf Ihre Seite. 
Der Zähler erscheint als dynamische Bild oder als Text in Ihrer Seitenbar oder Blog.
Sie können diverse Optionen einstellen.
Das Plug-In ist auf Deutsch und/oder Englisch verfügbar

== Installation ==
1. Upload Folder `ads_wpsitecount` to the `/wp-content/plugins/` directory
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

== Screenshots ==
1. Main screen of WP
2. Admin panel whit settings menu
3. wpsitecount options page
4. Widget page
5. wpsitecount widget options
6. small statistic on dashboard

== Changelog ==
= 1.0.0 =
* Start of the plugin

= 1.0.1 = 
* many corrections, counter, text

= 1.0.2 = 
* add small statistic to display page count

= 1.0.3 = 
* added new counters

== Translations ==
* English: - default, always included
* German: Deutsch - immer mit dabei!
