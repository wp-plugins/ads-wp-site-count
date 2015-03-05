<?php
/*
 handles the settings for the WpSiteCount plugin
 Date : 2015/03/05
 Author: ad-software, André
*/

defined('ABSPATH') OR exit;

if ( WP_DEBUG ) error_reporting(-1);

if (!defined('ADS_ID_GROUP')) 	define('ADS_ID_GROUP', 'adswsc-Group');
if (!defined('ADS_ID_PAGE')) 	define('ADS_ID_PAGE', 'adswsc-settings');
if (!defined('ADS_ID_SEC1')) 	define('ADS_ID_SEC1', 'adswsc-Section1');
if (!defined('ADS_ID_SEC2')) 	define('ADS_ID_SEC2', 'adswsc-Section2');
if (!defined('ADS_ID_SEC3')) 	define('ADS_ID_SEC3', 'adswsc-Section3');

require_once(ABSPATH . 'wp-admin/includes/plugin.php');

class adswsc_clsSettingsPage
{
	// Holds the values to be used in the fields callbacks
	private $mGeneral;
	private $mDoReset;

    // Start up
	public function __construct()
	{
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	// Add options page
	public function add_plugin_page()
	{
		// This page will be under "Settings"
		add_options_page(
			'Settings Admin',
			ADS_PLUGIN_NAME,
			'manage_options', 
			ADS_ID_PAGE, 
			array( $this, 'create_admin_page' )
		);
	}

	// Options page callback
	public function create_admin_page()
	{
		// Set class property
		$this->mGeneral = adswsc_GetOptions( ADS_OPTIONS_GENERAL );
		if ($this->mGeneral['New']=1) {
			$this->mGeneral['New'] = '';
			update_option( ADS_OPTIONS_GENERAL , $this->mGeneral );
		}
		?>
			<div class="wrap">
				<h2><?php _e('Wp Site Counter settings', ADS_TEXT_DOMAIN)?></h2>
					<form method="post" action="options.php" enctype="multipart/form-data">
				<?php
                // This prints out all hidden setting fields
                settings_fields( ADS_ID_GROUP );   
                do_settings_sections( ADS_ID_PAGE );
                submit_button(); 
				print '<address>';
				print __('Translated by: <a target="_blank" href="http://www.ad-soft.ch">ad-software</a>', ADS_TEXT_DOMAIN);
				print '</address>';
				?>
					</form>
					<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="7PW6RUUGFRL92">
					<input type="image" src="https://www.paypalobjects.com/de_DE/CH/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="Jetzt einfach, schnell und sicher online bezahlen – mit PayPal.">
					<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form>
			</div>
		<?php	
	}

	//===============================
	//general
	//===============================
	// Register and add settings
	public function page_init()
	{        
		register_setting(
			ADS_ID_GROUP, // Option group
			ADS_OPTIONS_GENERAL, // Option name
			array( $this, 'validate_general')  // Sanitize
		);

		add_settings_section(
			ADS_ID_SEC1, // ID
			__('Counter Settings', ADS_TEXT_DOMAIN), // Title
			array( $this, 'print_info_counter' ), // Callback
			ADS_ID_PAGE // Page
		);  

		add_settings_section(
			ADS_ID_SEC2, // ID
			__('Cleanup Settings', ADS_TEXT_DOMAIN), // Title
			array( $this, 'print_info_cleanup' ), // Callback
			ADS_ID_PAGE // Page
			);  

		add_settings_section(
			ADS_ID_SEC3, // ID
			__('Short dashboard statistics', ADS_TEXT_DOMAIN), // Title
			array( $this, 'print_info_statistics' ), // Callback
			ADS_ID_PAGE // Page
		);  


		add_settings_field(
			'Counter', // ID
			__('Default Counter Value', ADS_TEXT_DOMAIN), // Title 
			array( $this, 'callback_counter' ), // Callback
			ADS_ID_PAGE, // Page
			ADS_ID_SEC1, // Section           
			array( 'label_for' => 'Counter' )
		);      

		add_settings_field(
			'Reset', // ID
			__('Set Counter to Default Value', ADS_TEXT_DOMAIN), // Title 
			array( $this, 'callback_reset' ), // Callback
			ADS_ID_PAGE, // Page
			ADS_ID_SEC1, // Section    
			array( 'label_for' => 'Reset' )
		); 

		add_settings_field(
			'TinyTcOn', // ID
			__('Shortcode TinyMce Button', ADS_TEXT_DOMAIN), // Title 
			array( $this, 'callback_tinymce' ), // Callback
			ADS_ID_PAGE, // Page
			ADS_ID_SEC1, // Section    
			array( 'label_for' => 'TinyTcOn' )
		); 
		
		add_settings_field(
			'CycleTime', // ID
			__('Visitor-hit cooldown', ADS_TEXT_DOMAIN), // Title 
			array( $this, 'callback_Cycletime' ), // Callback
			ADS_ID_PAGE, // Page
			ADS_ID_SEC1, // Section    
			array( 'label_for' => 'CycleTime' )
		);      
        
		add_settings_field(
			'Cleanup', // ID
			__('Cleanup intervall', ADS_TEXT_DOMAIN), // Title 
			array( $this, 'callback_Cleanup' ), // Callback
			ADS_ID_PAGE, // Page
			ADS_ID_SEC2, // Section    
			array( 'label_for' => 'Cleanup' )
		);   
        
		add_settings_field(
			'DeleteTime', // ID
			__('Max age of the IP to remove', ADS_TEXT_DOMAIN), // Title 
			array( $this, 'callback_Deletetime' ), // Callback
			ADS_ID_PAGE, // Page
			ADS_ID_SEC2, // Section    
			array( 'label_for' => 'DeleteTime' )
		);   
        
		add_settings_field(
			'Bots', // ID
			__('Defines a comma-separated list of Robots, which prevents a count', ADS_TEXT_DOMAIN), // Title 
			array( $this, 'callback_bots' ), // Callback
			ADS_ID_PAGE, // Page
			ADS_ID_SEC2, // Section    
			array( 'label_for' => 'Bots' )
		);   
    
		add_settings_field(
			'PageTime', // ID
			__('Page-hit cooldown', ADS_TEXT_DOMAIN), // Title 
			array( $this, 'callback_Pagetime' ), // Callback
			ADS_ID_PAGE, // Page
			ADS_ID_SEC3, // Section    
			array( 'label_for' => 'PageTime' )
		);      
	}

	// Sanitize each setting field as needed
	// @param array $input Contains all settings fields as array keys
	public function validate_general( $input )
	{
		$temp = adswsc_GetOptions( ADS_OPTIONS_GENERAL );
		if ( isset( $input['Counter'] ) ){
			$temp['Counter'] = absint( $input['Counter'] );
		}

		if ( isset( $input['Reset'] ) )
			$DoReset = ($input['Reset'] == 'on' ? 'on' : '' );
		else
			$DoReset = '';

		// TinyTcOn
		$temp['TinyTcOn'] = ($input['TinyTcOn'] == 'on' ? 'on' : '' );

		//cycle
		$t = 0;
		if ( isset( $input['CycleSec'] ) ){
			$t += (absintminmax($input['CycleSec'],0,59));
		}
		if ( isset( $input['CycleMin'] ) ){
			$t += (absintminmax($input['CycleMin'], 0, 59) * 60 );
		}
		if ( isset( $input['CycleHou'] ) ){
			$t += (absintminmax($input['CycleHou'], 0, 24) * 3600);
		}
		if ($t >= 30 && $t <= (24*3600))
			$temp['CycleTime'] = $t;
		else
			add_settings_error('validate_general', esc_attr( '' ), 
				__('only 30 sec. to 24 hour supported on cyle time', ADS_TEXT_DOMAIN), 'error');
		
		//page
		$temp['PageOn'] = ($input['PageOn'] == 'on' ? 'on' : '');
		$t = 0;
		if ( isset( $input['PageSec'] ) ){
			$t += (absintminmax($input['PageSec'],0,59));
		}
		if ( isset( $input['PageMin'] ) ){
			$t += (absintminmax($input['PageMin'], 0, 60)*60);
		}
		if ($t >= 30 && $t <= (3600))
			$temp['PageTime'] = $t;
		else
			add_settings_error('validate_general', esc_attr( '' ), 
				__('only 30 sec. to 1 hour supported on cyle time', ADS_TEXT_DOMAIN), 'error');
				
		//cleanup
		if ( isset( $input['CleanupNow'] ) )
			$DoCleanup = ($input['CleanupNow'] == 'on' ? 'on' : '' );
		else
			$DoCleanup = '';
		$t = 0;		
		if( isset( $input['CleanupD'] ) ){
			$t += absint($input['CleanupD'])*24*3600;
		}
		if( isset( $input['CleanupM'] ) ){
			$t += absint($input['CleanupM'])*30*24*3600;
		}

		if ($t >= (24*3600) && $t <= (6*30*24*3600) ) {
			$temp['CleanupTime'] = $t;
		} else {
			add_settings_error('main',  ' ' , 
				sprintf(__('( Enter cleanup interval from 1 days to 6 months )', ADS_TEXT_DOMAIN).'<br>' ));
		}
		
		//delete IP
		$t = 0;
		if( isset( $input['DeleteDay'] ) ){
			$t += absint($input['DeleteDay'])*24*3600;
		}
		if( isset( $input['DeleteMon'] ) ){
			$t += absint($input['DeleteMon'])*30*24*3600;
		}
		
		if ($t >= (6*24*3600) && $t <= (9*30*24*3600) )
			$temp['DeleteTime'] = $t;
		else
			add_settings_error('validate_general', esc_attr( '' ), 
				__('only 6 day to 9 months supported on cleanup', ADS_TEXT_DOMAIN), 'error');
			
		// bots
		if( isset( $input['Bots'] ) ){
			$temp['Bots'] = htmlspecialchars($input['Bots']);
		}

		//
		if ( $DoReset == 'on' ) {
			adswsc_ResetCounter($temp['Counter']);
			$temp['LastReset'] = time();
			add_settings_error('main',  ' ' , 
				sprintf(__('( Counter reseted to %s )', ADS_TEXT_DOMAIN).'<br>', $temp['Counter']),
				'updated');
		}

		if ( $DoCleanup == 'on' ) {
			adswsc_CleanUp($temp['DeleteTime']);
			$temp['Cleanup'] = time() + $temp['CleanupTime'];
			add_settings_error('main',  ' ' , __(' ( outdated hits were cleaned )', ADS_TEXT_DOMAIN).'<br>', 'updated');
		}
	
		if ($temp['CleanupTime'] > $temp['DeleteTime'])
			add_settings_error('main',  ' ' , __('WARNING: Cleanup time should be less than IP age', ADS_TEXT_DOMAIN).'<br>', 'updated');

		return $temp;
	}

	// Print the Section text
	public function print_info_counter()
	{
	//printf
	}

	public function print_info_cleanup()
	{
	//printf
	}

	public function print_info_statistics()
	{
		//printf( __('Settings for the short statistic.', ADS_TEXT_DOMAIN));
	}
    
	// Get the settings option array and print one of its values
	public function callback_bots()
	{ 
		printf('<textarea maxlength="400" cols="50" rows="5" id="Bots" name="%s[Bots]" />%s</textarea>', 
			ADS_OPTIONS_GENERAL, 
			isset( $this->mGeneral['Bots'] ) ? esc_attr( $this->mGeneral['Bots']) : '');
		printf('<br>'.__('Example (copy & paste), white-space are removed on search:',ADS_TEXT_DOMAIN).'<br>'.
			'Google, msnbot, Rambler, Yahoo, AbachoBOT, Accoona, AcoiRobot, ASPSeek, CrocCrawler,'.
			' Dumbot, FAST-WebCrawler, GeonaBot, Gigabot, Lycos, MSRBOT, Scooter, Altavista, IDBot,'.
			' eStyle, Scrubby, facebookexternalhit, bot, robot');
	}

	public function callback_counter()
	{ 
		printf('<input type="text" id="Counter" name="%s[Counter]"  value="%s" />', 
			ADS_OPTIONS_GENERAL, 
			isset( $this->mGeneral['Counter'] ) ? esc_attr( $this->mGeneral['Counter']) : '0');
		global $wpdb;
		$table_name = $wpdb->prefix.ADS_PAGEFILE;
		$count = $wpdb->get_var("SELECT Count FROM $table_name where IP='0'");
		printf(" ".__('( actual count: %s )', ADS_TEXT_DOMAIN), $count);
	}

	public function callback_reset()
	{ 
		printf('<input type="checkbox" id="Reset" name="%s[Reset]" value="on" />', 
			ADS_OPTIONS_GENERAL);
		printf(" ".__('( Last Reset on: %s )', 
			ADS_TEXT_DOMAIN), 
			date('Y.m.d H:i:s', $this->mGeneral['LastReset']));
		print '<br>'.__('HINT: The counter will be reset on save settings', 
			ADS_TEXT_DOMAIN);
	}

	public function callback_tinymce()
	{ 	
		printf('<input type="checkbox" id="TinyTcOn" name="%s[TinyTcOn]" value="on" %s/>&nbsp( %s )', 
			ADS_OPTIONS_GENERAL,
			($this->mGeneral['TinyTcOn'] == 'on' ? 'checked' : ''),
			__('active', ADS_TEXT_DOMAIN)
		);
	}

	public function callback_Cycletime() {
		$t = absint( $this->mGeneral['CycleTime'] );
		$h = absint($t / 3600);
		$m = absint(($t - ($h*3600)) / 60);
		$s = absint(($t - ($h*3600) - ($m*60)));
		
    printf('<input maxlength="2" size="2" type="text" id="CycleHou" name="%s[CycleHou]" value="%s" />:', 
			ADS_OPTIONS_GENERAL, $h);
		printf('<input maxlength="2" size="2" type="text" id="CycleMin" name="%s[CycleMin]" value="%s" />:',
			ADS_OPTIONS_GENERAL, $m);
        printf('<input maxlength="2" size="2" type="text" id="CycleSec" name="%s[CycleSec]" value="%s" /> '.__('( Time, H:M:S )', ADS_TEXT_DOMAIN),
			ADS_OPTIONS_GENERAL, $s);
		printf("<br>".__('supports 30 sec. to 24 hours on ip cycle', ADS_TEXT_DOMAIN));
	}

	public function callback_Pagetime() {
		$t = absint( $this->mGeneral['PageTime'] );
		$m = absint($t / 60);
		$s = absint($t % 60);

		printf('<input type="checkbox" id="PageOn" name="%s[PageOn]" value="on" %s/>%s<br>', 
			ADS_OPTIONS_GENERAL, $this->mGeneral['PageOn'] == 'on' ? 'checked' : '', __('Statistic ON',ADS_TEXT_DOMAIN));
		printf('<input maxlength="2" size="2" type="text" id="PageMin" name="%s[PageMin]" value="%s" />:', ADS_OPTIONS_GENERAL, $m);
        printf('<input maxlength="2" size="2" type="text" id="PageSec" name="%s[PageSec]" value="%s" /> '.__('( Time, M:S )', ADS_TEXT_DOMAIN),
			ADS_OPTIONS_GENERAL, $s);
		printf('<br>'. __('supports 30 sec. to 1 Hour on page cycle', ADS_TEXT_DOMAIN));
	}

	public function callback_Deletetime() {
		$t = absint( $this->mGeneral['DeleteTime'] );
		$m = absint($t / (30*24*3600));
		$d = absint(($t - ($m*30*24*3600)) / (24*3600) );

		printf('<input maxlength="2" size="2" type="text" id="DeleteMon" name="%s[DeleteMon]" value="%s" />:', 
			ADS_OPTIONS_GENERAL, $m);
		printf('<input maxlength="2" size="2" type="text" id="DeleteDay" name="%s[DeleteDay]" value="%s" /> '.__('( Month / Day )',ADS_TEXT_DOMAIN), 
			ADS_OPTIONS_GENERAL, $d);
		printf('<br>'. __('supports 6 day to 9 months to start cleanup', ADS_TEXT_DOMAIN));
	}

	public function callback_Cleanup() {
		$t = absint( $this->mGeneral['CleanupTime'] );
		$m = absint($t / (30*24*3600));
		$d = absint(($t - ($m*30*24*3600)) / (24*3600) );
		
		printf('<input maxlength="2" size="2" type="text" id="CleanupM" name="%s[CleanupM]" value="%s" />:', 
			ADS_OPTIONS_GENERAL, $m);
		printf('<input maxlength="2" size="2" type="text" id="CleanupD" name="%s[CleanupD]" value="%s" /> '.__('( Month / Day )',ADS_TEXT_DOMAIN), 
			ADS_OPTIONS_GENERAL, $d);
		printf('&nbsp&nbsp<input type="checkbox" id="CleanupNow" name="%s[CleanupNow]" value="on" /> '.__('cleanup !now! ( next at %s )', ADS_TEXT_DOMAIN), 
			ADS_OPTIONS_GENERAL, date_i18n( get_option( 'date_format' ), $this->mGeneral['Cleanup'])); 
		printf('<br>'. __('supports 1 day to 6 months on cleanup', ADS_TEXT_DOMAIN));
		printf('<br>'. __('cleanup time should be less than IP age', ADS_TEXT_DOMAIN));
	}
}

if( is_admin() ) 
    $ads_SettingsPage = new adswsc_clsSettingsPage();
?>