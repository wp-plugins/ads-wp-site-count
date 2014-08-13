<?php
/*
Plugin Name: ADS-WpSiteCount
Plugin URI: www.ad-soft.ch/wpsitecount
Author: ad-software
Author URI: http://ad-soft.ch
Description: Count the page hit from each ip and display a counter into widget or page.
Version: 1.0.3
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: Wp Site Count, ad-software, Display Site Counter
Text Domain: ads_wpsitecount
Domain path: /lang
Date : 2014/08/13 19:47


Copyright 2014  ad-software 
contact me at www.ad-soft.ch/support

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

defined('ABSPATH') OR exit;

if ( WP_DEBUG ) error_reporting(-1); else error_reporting(0);

//=================================
//Constants
//=================================
if (!defined('ADS_HOME_URL')) 			define('ADS_HOME_URL','http://www.ad-soft.ch/wpplugins');
if (!defined('ADS_LOGFILE')) 			define('ADS_LOGFILE', 'adsLog'); 
if (!defined('ADS_PAGEFILE')) 			define('ADS_PAGEFILE', 'adsPage'); 
if (!defined('ADS_PLUGIN_DIR')) 		define('ADS_PLUGIN_DIR', plugin_dir_path(__FILE__)); 
if (!defined('ADS_COUNTER_DIR')) 		define('ADS_COUNTER_DIR', ADS_PLUGIN_DIR.'counters/'); 
if (!defined('ADS_STYLE_DIR')) 			define('ADS_STYLE_DIR', plugins_url( '/css/' , __FILE__ )); 
if (!defined('ADS_LANG_DIR')) 			define('ADS_LANG_DIR', '/lang/');
if (!defined('ADS_TEXT_DOMAIN')) 		define('ADS_TEXT_DOMAIN','ads_wpsitecount'); // CodeStyling Localization has bug, Text Doman not used, take always the plugin folder name :(
if (!defined('ADS_OPTIONS_SHORTCODE')) 	define('ADS_OPTIONS_SHORTCODE', 'adswsc_Shortcode');
if (!defined('ADS_OPTIONS_GENERAL')) 	define('ADS_OPTIONS_GENERAL', 'adswsc_General');
if (!defined('ADS_OPTIONS_RANDOM')) 	define('ADS_OPTIONS_RANDOM', 'adswsc_Random');
if (!defined('ADS_PLUGIN_NAME')) 		define('ADS_PLUGIN_NAME', 'ADS-WpSiteCount');
if (!defined('ADS_PLUGIN_NAMESC')) 		define('ADS_PLUGIN_NAMESC', 'ads-wpsitecount');

//=================================
//Load Widget
//=================================
if (!class_exists('adswsc_clsWidget') ) require_once(plugin_dir_path(__FILE__).'includes/ads_widgets.php'); 

//=================================
//Load Settings
//=================================
if( is_admin() ) require_once(plugin_dir_path(__FILE__).'includes/ads_options.php');
if( is_admin() ) require_once(plugin_dir_path(__FILE__).'includes/ads_dashwidget.php');
	
//=================================
//SHORTCODE - tell wordpress to register the demolistposts shortcode
//=================================
add_shortcode(ADS_PLUGIN_NAMESC, "adswscposts_handler");

function adswscposts_handler($args) {
   //run function that actually does the work of the plugin
	$options = adswsc_GetOptions( ADS_OPTIONS_SHORTCODE );
	$args = shortcode_atts( array (
		'count' => $options['count'],
		'text' => $options['text'],
		'fill' => $options['fill'],
		'align' => $options['align'],
		'length' => $options['length'],
		'before' => $options['before'],
		'after' => $options['after'],
		'width' => $options['width'],
		'height' => $options['height'],
		'block' => $options['block'],
		'image' => $options['image'],
		'docount' => '',
		'nocount' => '',
		'save' => ''
	), $args);
 
   //send back text to replace shortcode in post
   return adswscposts_function($args);
}

function adswscposts_function($args) {
	// Filter chars and data
	if ($args['nocount'])
		return '&#91;'.ADS_PLUGIN_NAMESC.' &lt;args&gt; &#93;';
  	$Directory = ADS_COUNTER_DIR.'*.jpg';
	$FILES = glob($Directory);
	$args['image'] = array_search(ADS_COUNTER_DIR.basename($args['image']) , $FILES) ? basename($args['image']) : ($FILES ? basename($FILES[0]) : "counter.jpg");
	$args['count'] = absint($args['count']);
	$args['text'] = wp_specialchars_decode($args['text']);
	$args['fill'] = wp_specialchars_decode($args['fill']);
	$args['align'] = wp_specialchars_decode($args['align']);
	$args['length'] = absint($args['length']);
	$args['before'] = wp_specialchars_decode($args['before']);
	$args['after'] = wp_specialchars_decode($args['after']);
	$args['width'] = absint($args['width']);
	$args['height'] = absint($args['height']); 
	$args['block'] = wp_specialchars_decode($args['block']); 
	$args['docount'] = wp_specialchars_decode($args['docount']);
	if ($args['save'])
	   update_option( ADS_OPTIONS_SHORTCODE , $args);
	
	//send back text to calling function
	return adswsc_GetViewCounter(absint($args['count']), $args);
}

function adswsc_GetOptions($settings) {
	$options = get_option($settings);
	switch($settings) {
		case ADS_OPTIONS_GENERAL:
			if (!is_array( $options )) {
				$options = array(
					'Counter' => 0,
					'Lastreset' => time(),
					'CycleOn' => 'on',
					'CycleTime' => 1800, //30 min
					'DeleteTime' => (60*24*3600), // 2 month
					'CleanupOn' => 'on',
					'CleanupTime' => (30*24*3600), // 1 month
					'Cleanup' => (time() + (30*24*3600)), // +1 month
					'Bots' => '',
					'PageOn' => 'on',
					'PageTime' => (1*60),
					'New' => 1
				);
			}
			break;
		case ADS_OPTIONS_SHORTCODE:
			if (!is_array( $options )) {
				$options = array(
					'count' => 2014,
					'text' => 'off',
					'fill' => 'off',
					'align' => 'none',
					'length' => 5,
					'image' => '',
					'before' => '',
					'after' =>  '',
					'width' => 165,
					'height' => '',
					'block' => '',
					'docount' => ''
				);
				$Directory = ADS_COUNTER_DIR.'*.jpg';
				$FILES = glob($Directory);
				if (sizeof($FILES) > 0)
					$options['image'] = basename($FILES[0]);
			}	
			break;
		case ADS_OPTIONS_RANDOM:
			if (!is_array( $options )) {
				$options = array(
					'image' => '',
					'time' => 0
				);
				$Directory = ADS_COUNTER_DIR.'*.jpg';
				$FILES = glob($Directory);
				if (sizeof($FILES) > 0)
					$options['image'] = basename($FILES[0]);
			}	
			break;
		default: 
			return null;
	}
	return $options;
}

//=================================
//functions
//=================================
function adswsc_GetViewCounter($count, $options) {
	global $wpdb;
	$general = adswsc_GetOptions( ADS_OPTIONS_GENERAL );
	$table_name = $wpdb->prefix.ADS_LOGFILE;
	$ip = "";
	$Output= "";

	if ($general['CleanupOn'] == 'on' && (time() - absint($general['Cleanup'])) > 0) {
		$table_name = $wpdb->prefix.ADS_PAGEFILE;
		$wpdb->query('DELETE FROM '.$table_name.' WHERE Time < '.(time()-$general['DeleteTime']));
		
		if ($general['PageOn'] == 'on') {
			$table_name = $wpdb->prefix.ADS_LOGFILE;
			$wpdb->query('DELETE FROM '.$table_name.' WHERE IP <> "0" and Time < '.(time()-$general['DeleteTime']));
		}
		
		$general = adswsc_GetOptions( ADS_OPTIONS_GENERAL );
		$general['Cleanup'] = time() + $general['CleanupTime'];
		update_option( ADS_OPTIONS_GENERAL, $general);
	}
	if ( $_SERVER['REMOTE_ADDR'])
		$ip = $_SERVER['REMOTE_ADDR'];
	else
		$ip = $_SERVER['HTTP_X_FORWARD_FOR'];
	if ($count != null)
		$user_count = $count;
	else
		$user_count = $wpdb->get_var("SELECT Count FROM $table_name where IP='0'");
	if ($options['docount'] == "on" && adswsc_CheckHit($ip, $general['Bots'])) {
		$data = array (
                 'IP' => '0',
                 'Time' => 0,
                 'Count'=> $user_count
                );
		$format = array ('%s','%d', '%d');
		if ($user_count == 0) 
			$wpdb->insert( $table_name, $data, $format );
		$wpdb->query("UPDATE ".$table_name." SET Count = ". ++$user_count .", Time=0 WHERE IP = '0'");
	}
	
	$CountData = adswsc_MakeImage($user_count, $options['length'], $options['fill'], $options['text'], $options['image']);
	$align = $options['align'] == '' ? "" :  "align='".$options['align']."'";
	switch ($options['block']) {
		case 'p': $output .=  "<p id='adswsc_block' ".$align.">"; break;
		case 'div': $output .=  "<div id='adswsc_block' ".$align.">"; break;
		default: 
			if ($options['align'] == 'center')
				$output .= '<center>';
			break;	
	}
	$output .=  adswsc_TranslatePlaceHolder($options['before']);
	$output .= (! empty($options['block']) && ! empty($options['before'])) ? '<br>' : '';
	if ($options['text'] == "on") {
		$output .=  "<a style='text-decoration: none;' target='_blank' href='".ADS_HOME_URL."' >";
		$output .=  "<span id='adswsc_countertext'> ".$CountData." </span>";
		$output .=  "</a>"; 
	} else {
		$w = $options['width'] ? "width='".$options['width']."'" : "";
		$h = $options['height'] ? "height='".$options['height']."'" : "";
		//$title = " title='"._e('copyright © by ad-software', ADS_TEXT_DOMAIN)."'";
		//$title = " ";
		$output .=  "<a style='text-decoration: none;' target='_blank' href='".ADS_HOME_URL."' >";
		$output .= "<img id='adswsc_counter' src='data:image/jpeg;base64,".base64_encode($CountData)."' align='middle' ".$w." ".$h."/></a>";
	}
	$output .= (! empty($options['block']) && ! empty($options['after'])) ? '<br>' : '';
	$output .=  adswsc_TranslatePlaceHolder($options['after']);
	switch ($options['block']) {
		case 'p': $output .=  "</p>"; break;
		case 'div': $output .=  "</div>"; break;
		default: 
			if ($options['align'] == 'center')
				$output .= '</center>';
			break;	
	}
	return $output;
}

function adswsc_TranslatePlaceHolder($Text) {
	$Text = str_ireplace('%ip%', $_SERVER['REMOTE_ADDR'], $Text);
	return $Text;
}

function adswsc_MakeImage($Count, $Length, $Fill, $Text, $File) {
  $m_cnt = $Count;
  if ($Length != 0) {
	while (strlen($m_cnt) < $Length) {
		if ($Fill == "on") 
			$m_cnt = "0".$m_cnt;
		else 
			$m_cnt = " ".$m_cnt;
	}
  }

  if ($Text == "on") {
    return trim($m_cnt);
  }

  $num = $len = strlen($m_cnt);
  while ($num >= 0) {
	if ($m_cnt[$num] == " ")
		$NUMS[$num] = 10;
	else
		$NUMS[$num] = trim(substr($m_cnt,$num,1));
    $num--;
  }
  $c = 0;

  # Bildausgabe
  $imageFile = ADS_COUNTER_DIR.$File;
    if (!glob($imageFile) ) {
		$img = imagecreatetruecolor(150,50);
		$white = ImageColorAllocate ($img, 255, 255, 255);
		Imagettftext($img, 10, 0, 5, 25, $white, 'arial.ttf', "Load error");
		ob_start();	//header('Content-type: image/jpeg');
		imagejpeg($img, "", 90);
		$image =  ob_get_contents();
		ob_end_clean();
		return $image;
	}

  $image = imagecreatefromjpeg($imageFile);
  if ($image == null){
    echo "error file";
    return "";
  }
  $x = round((imageSX($image) / 11),0);
  $y = imageSY($image);
  $img = imagecreatetruecolor($len*$x,$y);
  while ($c < $len) {
    # ein leeres Bild erstellen
	imagecopy($img, $image, $c*$x,0,$NUMS[$c]*$x, 0, $x, $y);
    $c++;
  } 
  imagedestroy($image);
  ob_start(); //header('Content-type: image/jpeg');
  imagejpeg($img, "", 90);
  $image =  ob_get_contents();
  ob_end_clean();
  return $image;
}

function adswsc_CheckHit($ip, $crawlers) {
	global $wpdb;
	$general = adswsc_GetOptions( ADS_OPTIONS_GENERAL );
	$table_name = $wpdb->prefix.ADS_LOGFILE;
	$format = array ('%s','%d', '%d');
	$DATA = array ('IP' => $ip,'Time' => time(), 'Count'=> 1 );
	$result = $wpdb->get_results("SELECT IP,Time,Count FROM $table_name where IP = '".$ip."'");
	if (!$result)
		$wpdb->insert( $table_name, $DATA, $format );
	else { // Get IP Data last hit
		$DATA['Time'] = $result[0]->Time;
		$DATA['Count'] = $result[0]->Count;
	}
	// check crawler
	if ( adswsc_IsCrawlerBot($crawlers) )
		return false;
	//Check Page
	if ($general['PageOn'] == 'on')
		adswsc_CountPage($ip);
	// check hit
	$Hit = $general['CycleOn'] != 'on' || ((time() - $DATA['Time']) > $general['CycleTime']);
	if ($Hit) { // yupi, save new hit state
		$DATA['Time'] = time();
		$DATA['Count']++;  
		$wpdb->update($table_name, $DATA, array('IP' => $ip), $format, array('%s'));
	}
	return $Hit;
}

function adswsc_CountPage($ip) {
	global $wpdb;
	$general = adswsc_GetOptions( ADS_OPTIONS_GENERAL );
	$table_name = $wpdb->prefix.ADS_PAGEFILE;
	$format = array ('%d','%s','%d','%d');
	$pageID = get_the_ID();
	$DATA = array ('PageID' => $pageID, 'IP' => $ip, 'Time' => time(), 'Count'=> 1 );
	$result = $wpdb->get_results("SELECT PageID,IP,Time,Count FROM $table_name where IP='".$ip."' AND PageID=".$pageID);
	if (!$result)
		$wpdb->insert( $table_name, $DATA, $format );
	else { // Get IP Data last hit
		$DATA['IP'] = $result[0]->IP;
		$DATA['Time'] = $result[0]->Time;
		$DATA['Count'] = $result[0]->Count;
	}
	//Update only after 30 sec again. 
	if ((time() - $DATA['Time']) > $general['PageTime'])
	{
		$DATA['Time'] = time();
		$DATA['Count']++;  
		$wpdb->update($table_name, $DATA, array('PageID' => $pageID, 'IP' => $ip), $format, array('%d','%s'));
	}
}

function adswsc_IsCrawlerBot($crawlers) {
	if (!isset($crawlers) || empty($crawlers))
		return false;
	$crawlers = str_replace(array(' ',','),array('','|'), $crawlers);
	$pattern = '/('.$crawlers.')/i';
	$agent = $_SERVER['HTTP_USER_AGENT'];
	$result = (isset($agent) && preg_match($pattern , $agent)); 
	return ($result == 1 ? true : false);
}

function adswsc_ResetCounter($Count){
	global $wpdb;
	$table_name = $wpdb->prefix.ADS_LOGFILE;
	$format = array ('%s','%d', '%d');
	$DATA = array ('IP' => '0','Time' => 0, 'Count'=> $Count );
	$result = $wpdb->get_results("SELECT IP,Time,Count FROM $table_name where IP = '0'");
	if (!$result)
		$wpdb->insert( $table_name, $DATA, $format );
	else
		$wpdb->update($table_name, $DATA, array('IP' => 0), $format, array('%s'));
}

//=================================
//setup, install, deinstall
//=================================
function adswsc_DbInstall () {
	global $wpdb;

	$table_name = $wpdb->prefix . ADS_LOGFILE;
	$Table = $wpdb->get_var("show tables like '$table_name'");
	//$Column = $wpdb->get_results("SHOW COLUMNS FROM $table_name LIKE 'Count'");

	if($Table != $table_name) {
	// No table create
      $sql = "CREATE TABLE $table_name (
           IP VARCHAR( 17 ) NOT NULL ,
           Time INT( 11 ) NOT NULL ,
           Count INT( 11 ) NOT NULL,
           PRIMARY KEY ( IP )
           );";
      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);
	} else {// Update check
		;
	}

	$table_name = $wpdb->prefix . ADS_PAGEFILE;
	$Table = $wpdb->get_var("show tables like '$table_name'");
	$Column = $wpdb->get_results("SHOW COLUMNS FROM $table_name LIKE 'IP'");
	if($Table != $table_name) {	// No table create
      $sql = "CREATE TABLE $table_name (
           PageID INT( 11 ) NOT NULL ,
           IP VARCHAR( 17 ) NOT NULL ,
           Time INT( 11 ) NOT NULL ,
           Count INT( 11 ) NOT NULL,
           PRIMARY KEY ( PageID, IP )
           );";
      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);
	} else {// Update check
		if (empty($Column)) {  //old table version, insert count
			$sql = "ALTER TABLE $table_name DROP PRIMARY KEY";
			$wpdb->query($sql);
			$sql = "ALTER TABLE $table_name ADD IP VARCHAR( 17 ) NOT NULL";
			$wpdb->query($sql);
			$sql = "UPDATE $table_name SET IP='0.0.0.0'";
			$wpdb->query($sql);
			$sql = "ALTER TABLE $table_name ADD PRIMARY KEY (PageID, IP)";
			$wpdb->query($sql);
		}
	}

}
function adswsc_Deactivation(){
}

function adswsc_Uninstall(){
  global $wpdb;
  $table_name = $wpdb->prefix . ADS_LOGFILE;
  $wpdb->query("DROP TABLE IF EXISTS $table_name");
  
  $table_name = $wpdb->prefix . ADS_PAGEFILE;
  $wpdb->query("DROP TABLE IF EXISTS $table_name");

  delete_option( ADS_OPTIONS_GENERAL );
  delete_option( 'widget_adswscwidget' );
  delete_option( ADS_OPTIONS_SHORTCODE );
}

function adswsc_AddStylesheet() {
	wp_register_style('adswscCSS', ADS_STYLE_DIR.'styles.css');
	wp_enqueue_style( 'adswscCSS');
}

function adswsc_Init() {
	$locale = apply_filters( 'plugin_locale', get_locale(), ADS_TEXT_DOMAIN );
	load_plugin_textdomain( ADS_TEXT_DOMAIN, FALSE, dirname( plugin_basename( __FILE__ ) ) . ADS_LANG_DIR); 
}

function adswsc_Loaded() {
	adswsc_DbInstall ();
	//
}

add_action("init", "adswsc_Init");
add_action("plugins_loaded", "adswsc_Loaded");
add_action('wp_print_styles', 'adswsc_AddStylesheet');
add_action('admin_print_styles', 'adswsc_AddStylesheet');

register_deactivation_hook( __FILE__, 'adswsc_Deactivation' );
register_uninstall_hook( __FILE__, 'adswsc_Uninstall' );

?>