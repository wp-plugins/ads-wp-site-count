<?php
/*
Plugin Name: ADS-WpSiteCount
Plugin URI: www.ad-soft.ch/wpsitecount
Author: ad-software
Author URI: http://ad-soft.ch
Description: Count the page hit from each ip and display a counter into widget or page.
Version: 1.0.9
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: Wp Site Count, ad-software, Display Site Counter
Text Domain: ads_wpsitecount
Domain path: /lang
Date : 2014/09/16

Copyright 2014  ad-software andré
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
if (!defined('ADS_HOME_URL'))	define('ADS_HOME_URL','http://www.ad-soft.ch/wpplugins');
if (!defined('ADS_PAGEFILE'))	define('ADS_PAGEFILE', 'adsPage'); 
if (!defined('ADS_PLUGIN_DIR'))	define('ADS_PLUGIN_DIR', plugin_dir_path(__FILE__)); 
if (!defined('ADS_COUNTER_DIR'))	define('ADS_COUNTER_DIR', ADS_PLUGIN_DIR.'counters/'); 
if (!defined('ADS_STYLE_DIR'))	define('ADS_STYLE_DIR', plugins_url( '/css/' , __FILE__ )); 
if (!defined('ADS_LANG_DIR'))	define('ADS_LANG_DIR', '/lang/');
if (!defined('ADS_TEXT_DOMAIN'))	define('ADS_TEXT_DOMAIN','ads_wpsitecount'); 
if (!defined('ADS_OPTIONS_SHORTCODE'))	define('ADS_OPTIONS_SHORTCODE', 'adswsc_Shortcode');
if (!defined('ADS_OPTIONS_GENERAL'))	define('ADS_OPTIONS_GENERAL', 'adswsc_General');
if (!defined('ADS_OPTIONS_RANDOM'))	define('ADS_OPTIONS_RANDOM', 'adswsc_Random');
if (!defined('ADS_PLUGIN_NAME'))	define('ADS_PLUGIN_NAME', 'ADS-WpSiteCount');
if (!defined('ADS_PLUGIN_NAMESC'))	define('ADS_PLUGIN_NAMESC', 'ads-wpsitecount');
if (!defined('ADS_PLUGIN_NAMETM'))	define('ADS_PLUGIN_NAMETM', 'adswsc_sc');
if (!defined('ADS_CSSC_PATH'))	define('ADS_CSSC_PATH', plugins_url( '/css/adswsc.css' , __FILE__ ));
if (!defined('ADS_JSSC_PATH'))	define('ADS_JSSC_PATH', plugins_url( '/includes/ads_shortcode.js.php' , __FILE__ ));

//=================================
//Load Widget
//=================================
if (!class_exists('adswsc_clsWidget') ) require_once(plugin_dir_path(__FILE__).'includes/ads_widgets.php'); 
if (!class_exists('adswsc_clsShortCode') ) require_once(plugin_dir_path(__FILE__).'includes/ads_shortcode.php'); 

//=================================
//Load Settings
//=================================
if( is_admin() ) require_once(plugin_dir_path(__FILE__).'includes/ads_options.php');
if( is_admin() ) require_once(plugin_dir_path(__FILE__).'includes/ads_dashwidget.php');

//=================================
function adswsc_GetOptions($settings, $default = false) {
	$options = get_option($settings);
	switch($settings) {
		case ADS_OPTIONS_GENERAL:
			if (!is_array( $options ) || $default==true) {
				$options = array(
					'Counter' => 0,
					'Lastreset' => time(),
					'CycleTime' => 1800, //30 min
					'DeleteTime' => (60*24*3600), // 2 month
					'CleanupTime' => (30*24*3600), // 1 month
					'Cleanup' => (time() + (30*24*3600)), // +1 month
					'Bots' => '',
					'PageOn' => 'on',
					'PageTime' => (1*60),
					'TinyTcOn' => '',
					'New' => 1
				);
			}
			break;
		case ADS_OPTIONS_SHORTCODE:
			if (!is_array( $options ) || $default==true) {
				$options = array(
					'count' => 0,
					'text' => 'off',
					'fill' => 'off',
					'align' => 'none',
					'length' => 5,
					'image' => '',
					'before' => '',
					'after' =>  '',
					'width' => 165,
					'height' => '',
					'whunit' => 'px',
					'imgmaxw' => 250,
					'block' => ''
				);
				$Directory = ADS_COUNTER_DIR.'*.jpg';
				$FILES = glob($Directory);
				if (sizeof($FILES) > 0)
					$options['image'] = basename($FILES[0]);
			}	
			break;
		case ADS_OPTIONS_RANDOM:
			if (!is_array( $options ) || $default==true) {
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
	$table_name = $wpdb->prefix.ADS_PAGEFILE;

	$general = adswsc_GetOptions( ADS_OPTIONS_GENERAL );
	$ip = ( $_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : $_SERVER['HTTP_X_FORWARD_FOR'];
	$output= "";

	if ($count != null)
		$user_count = $count;
	else
		$user_count = $wpdb->get_var("SELECT Count FROM $table_name where IP='0'");
	$align = $options['align'] == '' ? "" :  "align='".$options['align']."'";
	switch ($options['block']) {
		case 'p': $output .=  "<p id='adswsc_block' ".$align.">"; break;
		case 'div': $output .=  "<div id='adswsc_block' ".$align.">"; break;
		default: $output .=  ($options['align'] == 'center') ? '<center>' : ''; 
				 $output .= "<span id='adswsc_block' >"; break;
	}
	$output .=  adswsc_TranslatePlaceHolder($options['before'], $options, $user_count);
	$output .= (! empty($options['block']) && ! empty($options['before'])) ? '<br>' : ' ';
	$output .=  "<a style='text-decoration: none;' target='_blank' href='".ADS_HOME_URL."' >";
	switch ($options['whunit']) {
		case '%': 
			$options['width'] = ($options['width'] > 100) ? 100 : $options['width']."%"; 
			$options['height'] = ($options['height'] > 100) ? 100 : $options['height']."%"; 
			break;
		case 'pt':		
			$options['width'] = ($options['width'] > 0) ? round(( $options['width'] * 96 / 72 ),0) : 0; 
			$options['height'] = ($options['height'] > 0) ? round(( $options['height'] * 96 / 72 ),0) : 0; 
			break;
		case 'em':	
			$options['width'] = ($options['width'] > 0) ? round(( $options['width'] * 16),0) : 0; 
			$options['height'] = ($options['height'] > 0) ? round(( $options['height'] * 16),0) : 0; 
			break;
		default:
			break;
	}
	$CountData = adswsc_MakeImage($user_count, $options['length'], $options['fill'], $options['text'], $options['image'], $options['imgmaxw']);
	if ($options['text'] == "on") 
		$output .=  "<span id='adswsc_countertext'> ".$CountData." </span>";
	else {
		$w = $options['width'] ? " width='".$options['width']."'" : "";
		$h = $options['height'] ? " height='".$options['height']."'" : "";
		$output .= "<img id='adswsc_counter' src='data:image/jpeg;base64,".base64_encode($CountData)."' align='middle' ".$w." ".$h."/>";
	}
	$output .=  "</a>"; 
	$output .= (! empty($options['block']) && ! empty($options['after'])) ? '<br>' : ' ';
	$output .=  adswsc_TranslatePlaceHolder($options['after'], $options, $user_count);
	switch ($options['block']) {
		case 'p': $output .=  "</p>"; break;
		case 'div': $output .=  "</div>"; break;
		default: $output .=  ($options['align'] == 'center') ? '</span></center>' : '</span>'; break;
	}
	return $output;
}

//=================================
function adswsc_TranslatePlaceHolder($Text, $options, $user_count) {
	$Text = str_ireplace('%ip', $_SERVER['REMOTE_ADDR'], $Text);
	$Text = str_ireplace('%image', preg_replace("/\.[^.]+$/", "", $options['image']), $Text);
	$Text = str_ireplace('%count', $user_count, $Text);
	$Text = str_ireplace('\n', '<br>', $Text);
	$posS = strpos($Text, '%[');
	$posE = strpos($Text, ']%');
	$len = ($posE  - $posS - 2);
	if ($posS !== false && $len > 0){
		$temp = substr($Text, $posS + 2, $len);
		$Text = str_ireplace('%['.$temp.']%', '%*%', $Text);
		if(is_user_logged_in()) {
			global $current_user;
			get_currentuserinfo(); 
			$temp = str_ireplace('%fname', $current_user->user_firstname, $temp);
			$temp = str_ireplace('%lname', $current_user->user_lastname, $temp);
			$temp = str_ireplace('%dname', $current_user->display_name, $temp);
			$temp = str_ireplace('%sname', $current_user->user_login, $temp);
		}
		else
		 $temp = '';
		$Text = str_ireplace('%*%', $temp, $Text);
	}
	return $Text;
}

//=================================
function adswsc_MakeImage($Count, $Length, $Fill, $Text, $File, $Width) {
	$m_cnt = strval($Count);
	if ($Length != 0) {
		while (strlen($m_cnt) < $Length) {
			$m_cnt = ($Fill == "on") ? "0".$m_cnt : " ".$m_cnt;
		}
	}
	if ($Text == "on") 
		return trim($m_cnt);

	$num = $len = strlen($m_cnt);
	$NUMS = array($num); 
	while ($num >= 0) {
		$NUMS[$num] = (substr($m_cnt,$num,1) == " ") ? 10 : substr($m_cnt,$num,1);
		$num--;
	}
	$c = 0;

	// create image
	$imageFile = ADS_COUNTER_DIR.$File;
	if (!glob($imageFile) ) {
			$img = imagecreatetruecolor(150,50);
			$white = ImageColorAllocate ($img, 255, 255, 255);
			Imagettftext($img, 10, 0, 5, 25, $white, 'arial.ttf', "no file");
			ob_start();	//header('Content-type: image/jpeg');
			imagejpeg($img, "", 90);
			$image =  ob_get_contents();
			ob_end_clean();
			return $image;
	}
	$image = imagecreatefromjpeg($imageFile);
	if ($image == null){
			$img = imagecreatetruecolor(150,50);
			$white = ImageColorAllocate ($img, 255, 255, 255);
			Imagettftext($img, 10, 0, 5, 25, $white, 'arial.ttf', "error read");
			ob_start();	//header('Content-type: image/jpeg');
			imagejpeg($img, "", 90);
			$image =  ob_get_contents();
			ob_end_clean();
			return $image;
	}
	$dx = $x = round((imageSX($image) / 11),0);
	$dy = $y = imageSY($image);
	if ($Width > 0) {
		$dx = round(($Width/$len),0);
		$dy = $y / $x * $dx;
	}	
	$img = imagecreatetruecolor($len*$dx,$dy);
	while ($c < $len) {
		# ein leeres Bild erstellen
		imagecopyresampled($img, $image, $c*$dx,0,$NUMS[$c]*$x, 0, $dx, $dy, $x, $y);
		$c++;
	} 
	imagedestroy($image);
	ob_start(); //header('Content-type: image/jpeg');
	imagejpeg($img, "", 90);
	$img =  ob_get_contents();
	ob_end_clean();
	return $img;
}

//=================================
function adswsc_CheckHit() {
	global $wpdb;
	$general = adswsc_GetOptions( ADS_OPTIONS_GENERAL );

	//cleanup
	if ((time() - absint($general['Cleanup'])) > 0) {
		adswsc_CleanUp($general['DeleteTime']);
		//save settings
		$general = adswsc_GetOptions( ADS_OPTIONS_GENERAL );
		$general['Cleanup'] = time() + $general['CleanupTime'];
		update_option( ADS_OPTIONS_GENERAL, $general);
	}

	// check crawler
	if ( adswsc_IsCrawlerBot($general['Bots'])) 
		return;

	//ip hits, allways pageid = 0
	$ip = ( $_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : $_SERVER['HTTP_X_FORWARD_FOR'];
	$table_name = $wpdb->prefix.ADS_PAGEFILE;
	$format = array ('%d','%s','%d', '%d');
	$DATA = array ('PageID' => 0 ,'IP' => $ip,'Time' => time(), 'Count'=> 1 );
	$result = $wpdb->get_results("SELECT PageID,IP,Time,Count FROM $table_name where PageID=0 and IP='$ip' LIMIT 1");
	if (!$result) // insert new
		$wpdb->insert( $table_name, $DATA, $format );
	else { // Get IP Data last hit
		$DATA['Time'] = $result[0]->Time;
		$DATA['Count'] = $result[0]->Count;
	}
	// check hit
	if ((time() - $DATA['Time']) > $general['CycleTime']) {
		//update ip info
		$DATA['Time'] = time();
		$DATA['Count']++;  
		$wpdb->update($table_name, $DATA, array('PageID' => 0, 'IP' => $ip), $format, array('%d','%s'));
		
		//counter, allways ip='0', pageid=0, time=0
		$count = $wpdb->get_var("SELECT Count FROM $table_name where IP='0'");
		if ($count == 0) {
			$DATA['PageID'] = 0;
			$DATA['Time'] = 0;
			$DATA['IP'] = '0';
			$DATA['Count']  = 0;  
			$wpdb->insert( $table_name, $DATA, $format );
		}
		//count
		$wpdb->query("UPDATE ".$table_name." SET Count = ".++$count." WHERE IP = '0'");
	}
}

//=================================
function adswsc_CountPage($pageID) {
	global $wpdb;
	$general = adswsc_GetOptions( ADS_OPTIONS_GENERAL );

	if ( adswsc_IsCrawlerBot($general['Bots']) || $general['PageOn'] != 'on' || $pageID == 0)
		return;

	if ($pageID == 0)
		return;	
	$ip = ( $_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : $_SERVER['HTTP_X_FORWARD_FOR'];
	$table_name = $wpdb->prefix.ADS_PAGEFILE;
	$format = array ('%d','%s','%d','%d');
	$DATA = array ('PageID' => $pageID, 'IP' => $ip, 'Time' => time(), 'Count'=> 1 );
	$result = $wpdb->get_results("SELECT PageID,IP,Time,Count FROM $table_name where IP='$ip' AND PageID=".$pageID);
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

//=================================
function adswsc_IsCrawlerBot($crawlers) {
	if (!isset($crawlers) || empty($crawlers))
		return false;
	$crawlers = str_replace(array(' ',','),array('','|'), $crawlers);
	$pattern = '/('.$crawlers.')/i';
	$agent = $_SERVER['HTTP_USER_AGENT'];
	$result = (isset($agent) && preg_match($pattern , $agent)); 
	return ($result == 1 ? true : false);
}

//=================================
function absintminmax($whatever, $min = 1, $max = 100) {
	if(intval($whatever) < $min) return $min;
	elseif(intval($whatever) > $max) return $max;
	return absint(intval($whatever));
}

//=================================
function adswsc_CleanUp($CleanupTime = 2592000) {
	global $wpdb;
	$table_name = $wpdb->prefix.ADS_PAGEFILE;
	$wpdb->query('DELETE FROM '.$table_name.' WHERE IP <> "0" and Time < '.(time()-$CleanupTime));
}

//=================================
function adswsc_ResetCounter($Count){
	global $wpdb;
	$table_name = $wpdb->prefix.ADS_PAGEFILE;
	$format = array ('%d','%s','%d', '%d');
	$DATA = array ('PageID'=>0, 'IP' => '0','Time' => 0, 'Count'=> $Count );
	$result = $wpdb->get_results("SELECT PageID, IP,Time,Count FROM $table_name where IP = '0'");
	if (!$result)
		$wpdb->insert( $table_name, $DATA, $format );
	else
		$wpdb->update($table_name, $DATA, array('IP' => 0), $format, array('%s'));
}

//=================================
function adswsc_deletePost( $postid ){
	global $wpdb; // delete post/page id to, on adsPage table
	$table_name = $wpdb->prefix.ADS_PAGEFILE;
	$result = $wpdb->query("DELETE FROM $table_name where PageID=$postid");
}

//=================================
function adswsc_theContent( $content ) {
	global $post;
	if(is_feed()) {
		return $content;
	}
	if(strpos($content,'more-link') !== false || strpos($content,'<!--more-->') !== false) {
		return $content;
	}
	if (in_array( $post->post_type, array( 'post', 'page') ) ) 
		adswsc_CountPage($post->ID);
	return $content;
}

//=================================
//setup, install, deinstall
//=================================
function adswsc_DbInstall () {
	global $wpdb;
       
	$table_name = $wpdb->prefix . ADS_PAGEFILE;
	$Table = $wpdb->get_var("show tables like '$table_name'");
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
		$wpdb->query("INSERT INTO $table_name (PageID,IP,Time,Count) VALUE(0,'0',".time().",1)");
	} else {
		$Column = $wpdb->get_results("SHOW COLUMNS FROM $table_name LIKE 'IP'");
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

	$table_old = $wpdb->prefix."adsLog";
	$Table = $wpdb->get_var("show tables like '$table_old'");
	if($Table == $table_old) {
		// convert / delete
		$wpdb->query("DELETE FROM $table_name WHERE PageID = 0");
		$e = $wpdb->query("INSERT INTO $table_name (PageID,IP,Time,Count) ".
							"SELECT 0,IP,Time,Count FROM $table_old");
		if ($e !== false) 
			$wpdb->query("DROP TABLE IF EXISTS $table_old");
	}	
}

//=================================
function adswsc_Deactivation(){
}

//=================================
function adswsc_Uninstall(){
  global $wpdb;
  $table_name = $wpdb->prefix . ADS_PAGEFILE;
  $wpdb->query("DROP TABLE IF EXISTS $table_name");

  delete_option( ADS_OPTIONS_GENERAL );
  delete_option( 'widget_adswscwidget' );
  delete_option( ADS_OPTIONS_SHORTCODE );
}

//=================================
function adswsc_AddStylesheet() {
	wp_register_style('adswscCSS', ADS_STYLE_DIR.'styles.css');
	wp_enqueue_style( 'adswscCSS');
}

//=================================
function adswsc_Init() {
	$locale = apply_filters( 'plugin_locale', get_locale(), ADS_TEXT_DOMAIN );
	load_plugin_textdomain( ADS_TEXT_DOMAIN, FALSE, dirname( plugin_basename( __FILE__ ) ) . ADS_LANG_DIR); 
}

//=================================
function adswsc_Loaded() {
	adswsc_DbInstall();
	adswsc_CheckHit();
}

add_action('init', 'adswsc_Init');
add_action('plugins_loaded', 'adswsc_Loaded');
add_action('wp_print_styles', 'adswsc_AddStylesheet');
add_action('admin_print_styles', 'adswsc_AddStylesheet');
add_action('before_delete_post', 'adswsc_deletePost');
add_action('the_content', 'adswsc_theContent' );

register_deactivation_hook( __FILE__, 'adswsc_Deactivation' );
register_uninstall_hook( __FILE__, 'adswsc_Uninstall' );

?>