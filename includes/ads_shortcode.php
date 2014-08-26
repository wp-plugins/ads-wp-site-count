<?php
/*
 Shortcode for the WpSiteCount plugin
 Date : 2014/08/26
 Author: ad-software, André
*/
defined('ABSPATH') OR exit;
if ( WP_DEBUG ) error_reporting(-1);

class adswsc_clsShortCode  {
 
	protected static $instance = NULL;

	public static function get_instance() {
        // create an object
        NULL === self::$instance and self::$instance = new self;
        return self::$instance; // return the object
    }

	//=================================
	public function post_handler($args) {
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
			'whunit' => $options['whunit'],
			'imgmaxw' => $options['imgmaxw'],
			'block' => $options['block'],
			'image' => $options['image'],
			'nocount' => '',
			'reset' => '',
			'save' => ''
		), $args);
		//send back text to replace shortcode in post
		return $this->post_function($args);
	}
	
	//=================================
	public function post_function($args) {
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
		$args['align'] = (in_array( $args['align'], array( 'left', 'right', 'center', '' ) ) ? $args['align'] : '' );
		$args['length'] = absint($args['length']);
		$args['before'] = wp_specialchars_decode($args['before']);
		$args['after'] = wp_specialchars_decode($args['after']);
		$args['width'] = absint($args['width']);
		$args['height'] = absint($args['height']); 
		$args['whunit'] = wp_specialchars_decode($args['whunit']); 
		$args['whunit'] = (in_array( $args['whunit'], array( '%', 'px', 'pt', 'em' ) ) ? $args['whunit'] : 'px' );
		$args['imgmaxw'] = absint($args['imgmaxw']);
		$args['block'] = wp_specialchars_decode($args['block']); 
		$args['block'] = (in_array( $args['block'], array( 'p', 'div', '' ) ) ? $args['block'] : '' );
		if ($args['reset'])
			$options = adswsc_GetOptions( ADS_OPTIONS_SHORTCODE , true );
		if ($args['save']) 
			update_option( ADS_OPTIONS_SHORTCODE , $args);

		//send back text to calling function
		return adswsc_GetViewCounter(absint($args['count']), $args);
	}

	public function add_plugin( $arPl ) {
		$arPl[ ADS_PLUGIN_NAMETM ] = ADS_JSSC_PATH;
		return $arPl;
	}
	
	public function register_button( $buttons ) {
		array_push( $buttons, "|", ADS_PLUGIN_NAMETM );
		return $buttons;
	}		
		
	public function tcbutton() {
		if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) 
			return;
		add_filter( 'mce_external_plugins', array($this, 'add_plugin') );
		add_filter( 'mce_buttons', array($this, 'register_button') );
	}	

	public function load_mcecss() { 
		wp_enqueue_style(ADS_PLUGIN_NAMETM, ADS_CSSC_PATH);
	}

}	

//=================================
//SHORTCODE - register 
//=================================
add_shortcode( ADS_PLUGIN_NAMESC , array( adswsc_clsShortCode::get_instance(), 'post_handler') );
if (is_admin() ) {
	$general = adswsc_GetOptions( ADS_OPTIONS_GENERAL );
	if ( isset($general['TinyTcOn']) && $general['TinyTcOn'] == 'on') { // only register if active
		add_action('admin_enqueue_scripts', array(adswsc_clsShortCode::get_instance(), 'load_mcecss' )); 
		add_action('init', array(adswsc_clsShortCode::get_instance(), 'tcbutton'));
	}
}

?>
