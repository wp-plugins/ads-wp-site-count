<?php
/*
 display widget on dashboard to show a small statistic
 Date : 2014/08/15
 //char(äöü)
*/
defined('ABSPATH') OR exit;

if ( WP_DEBUG ) error_reporting(-1);

if (!defined('ADS_DASHBOARD')) 		define('ADS_DASHBOARD', 'ads-dashboardwidget');

// Class
class adswsc_clsDashboardWidgets {

	//Constructor
    function __construct() {
        add_action( 'wp_dashboard_setup', array( $this, 'remove_dashboard_widgets' ) );
        add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widgets' ) );
    }
 
	// Remove
	function remove_dashboard_widgets() {
		remove_meta_box( ADS_DASHBOARD, 'home', 'side' );
	}	

	// Add
    function add_dashboard_widgets() {
		wp_add_dashboard_widget( ADS_DASHBOARD,
			__('ADS-WpSiteCount - Small statistic',ADS_TEXT_DOMAIN),
			array($this,'content_dashboard_widget' ));
	}	

	// Remove
    function content_dashboard_widget() {
		global $wpdb;
		$pageRows = 5;
		$table_name = $wpdb->prefix.ADS_PAGEFILE;
		$general = adswsc_GetOptions( ADS_OPTIONS_GENERAL );

		if (isset($_POST['rows']))  
			$pageRows = (absint($_POST['rows']));
		if (isset($_POST['button_clean']))  {
			$results = $wpdb->get_results("SELECT PageID FROM $table_name GROUP BY PageID");
			foreach($results as $result) {
				$link = get_permalink( $result->PageID, false ); 
				$title = get_the_title( $result->PageID );
				if ((empty($link) && empty($title)) || $result->PageID == 0)
					$wpdb->query("DELETE FROM $table_name WHERE PageID=$result->PageID");
			}
			if (isset($_POST['clean_yes']) == true)
				$wpdb->query("DELETE FROM $table_name");
		}
	
		$x =  $wpdb->get_results( "SELECT PageID FROM $table_name WHERE PageID > 0 GROUP BY PageID");
		$pageMaxx = sizeof($x);
		$pageMax = absint($pageMaxx / $pageRows) + ( ($pageMaxx % $pageRows) > 0 ? 1 : 0);
		$pageStart = 0;
		if (isset($_POST['page']))  
			$pageStart = (absint($_POST['page'])-1);
		if (isset($_POST['button_next']))
			$pageStart += $pageStart < ($pageMax-1) ? 1 : 0;
		if (isset($_POST['button_prev']))
			$pageStart -= $pageStart > 0 ? 1 : 0;
		if (isset($_POST['button_top']))
			$pageStart = 0;
		if (isset($_POST['button_last']))
			$pageStart = $pageMax-1;

		echo "<p>".__('The table shows the number of calls for each page.', ADS_TEXT_DOMAIN).' ';
		echo __('Not referring page addresses will not be displayed.' , ADS_TEXT_DOMAIN)."</p>"; 
		$format = array ('%d','%s','%d','%d');
		$DATA = array ('PageID' => 0, 'Time' => (time()-30000), 'Count'=> 0 );
		$results = $wpdb->get_results("SELECT PageID,IP,Time,sum(Count) as Total FROM $table_name".
									  " WHERE PageID > 0".
									  " GROUP BY PageID ORDER BY Total Desc, Time Asc".
									  " LIMIT ".($pageStart * $pageRows).", ".$pageRows);
		echo '<form class="update" method="post" action="#" enctype="multipart/form-data">';
		echo '<table width="100%"><tr><td>';
		echo '<input style="width:30px;" class="button" type="submit" name="button_top" value="«">';
		echo '<input style="width:30px;" class="button" type="submit" name="button_prev" value="‹">';
		echo '&nbsp;<select  style="width:100px;" class="actions bulkactions" name="page" onchange="this.form.submit()">';
		for ($x=1; $x <= $pageMax; $x++) {	
			echo '<option size="100px" value="'.$x.'" '.($pageStart == ($x-1) ? "selected" : "" ).' >'.__('Page', ADS_TEXT_DOMAIN).' '.$x.'</option>';
		}
		echo '</select>&nbsp'.__('von', ADS_TEXT_DOMAIN).'&nbsp;'.$pageMax.'&nbsp;';
		echo '<input style="width:30px;" class="button" type="submit" name="button_next" value="›">';
		echo '<input style="width:30px;" class="button" type="submit" name="button_last" value="»">';
		echo '</td><td align="right">'.__('rows&nbsp;', ADS_TEXT_DOMAIN);
		echo '&nbsp;<select style="width:60px;" class="actions bulkactions" name="rows" onchange="this.form.submit()">';
		echo '<option value="5" '.($pageRows == 5 ? "selected" : "").'>5</option>';
		echo '<option value="10" '.($pageRows == 10 ? "selected" : "").'>10</option>';
		echo '<option value="20" '.($pageRows == 20 ? "selected" : "").'>20</option>';
		echo '<option value="50" '.($pageRows == 50 ? "selected" : "").'>50</option>';
		echo '</select></td></tr></table></form><table class="widefat"><thead><tr>';
		echo '<th width="1%" align="center" class="manage-column"><strong>'.__('Count', ADS_TEXT_DOMAIN).'</strong></th>';
		echo '<th class="manage-column"><strong>'.__('page', ADS_TEXT_DOMAIN).'</strong></th>';
		echo '</tr></thead><tbody class="plugins">';
		if ($results) {
			$i=0;
			foreach($results as $result) { 
				$i++;
				if (($i % 2) == 1)
					echo '<tr style="background:#f5f5f5">';
				else
					echo '<tr>';
				echo '<td align="center">'.$result->Total.'</td>';
				$link  = get_permalink( $result->PageID, false ); 
				$title = get_the_title($result->PageID);
				if (empty($title) || empty($link) ) 
					echo "<td>-deleted- id:$result->PageID.</td>";
				else 
					echo "<td><a target='_blank' href='$link'>$title</a></td>";
				echo '</tr>';
			}	
		} else 
			echo '<tr><td>-</td><td><b>'.__('no data found', ADS_TEXT_DOMAIN).'</b></td></tr>';
		echo '</tbody></table>';
		echo '<form class="update" method="post" action="#" enctype="multipart/form-data">';
		echo '<p class="submit">'.__('delete rows',ADS_TEXT_DOMAIN).' ('.$pageMaxx.') <input class="checkbox" type="checkbox" name="clean_yes">';
		echo '&nbsp;<input class="button" type="submit" name="button_clean" value="'.__('GO', ADS_TEXT_DOMAIN).'">';
		echo '</p></form>';
		}
}

$adswsc_DashboardWidget = new adswsc_clsDashboardWidgets();
?>
