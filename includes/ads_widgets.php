<?php
/*
 Widget for the WpSiteCount plugin
 Date : 2014/08/15
 //char(äöü)
*/

defined('ABSPATH') OR exit;

if ( WP_DEBUG ) error_reporting(-1);

add_action( 'widgets_init', 'adswsc_load_widget' );

// Register and load the widget
function adswsc_load_widget() {
	register_widget( 'adswsc_clsWidget' );
}

class adswsc_clsWidget extends WP_Widget {
	
	function __construct() {
		parent::__construct(	
			'adswscWIDGET', // Base ID of your widget
			ADS_PLUGIN_NAME, // Widget name will appear in UI
			array( 'description' => __('Count the page hit from each ip and display a counter into widget or page.', ADS_TEXT_DOMAIN ), ) // Widget description
		);
	}

	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {
		$options = array();
		$options['title'] = apply_filters( 'widget_title', $instance['title'] );
		$options['before'] = apply_filters( 'widget_before', $instance['before'] );
		$options['after'] = apply_filters( 'widget_after', $instance['after'] );
		$options['width'] = apply_filters( 'widget_width', $instance['width'] );
		$options['height'] = apply_filters( 'widget_height', $instance['height'] );
		$options['whunit'] = apply_filters( 'widget_whunit', $instance['whunit'] );
		$options['image'] = apply_filters( 'widget_image', $instance['image'] );
		$options['align'] = apply_filters( 'widget_align', $instance['align'] );
		$options['length'] = apply_filters( 'widget_before', $instance['length'] );
		$options['fill'] = apply_filters( 'widget_fill', $instance['fill'] );
		$options['text'] = apply_filters( 'widget_text', $instance['text'] );
		$options['block'] = apply_filters( 'widget_block', $instance['block'] );
		$options['docount'] = apply_filters( 'widget_docount', $instance['docount'] );
		$options['randimg'] = apply_filters( 'widget_randimg', $instance['randimg'] );
		echo $args['before_widget'];

		switch ($options['randimg']) {
			case 1: // hour
				$rand = adswsc_GetOptions( ADS_OPTIONS_RANDOM );
				if ($rand['time'] != date('h')) {
					$rand['image'] = $this->get_randomImage();
					$rand['time'] = date('h');
					update_option(ADS_OPTIONS_RANDOM , $rand );
				}
				$options['image'] = $rand['image'];
				break;
			case 2: //day
				$rand = adswsc_GetOptions( ADS_OPTIONS_RANDOM );
				if ($rand['time'] != date('d')) {
					$rand['image'] = $this->get_randomImage();
					$rand['time'] = date('d');
					update_option(ADS_OPTIONS_RANDOM , $rand );
				}
				$options['image'] = $rand['image'];
				break;
			case 3:	// week
				$rand = adswsc_GetOptions( ADS_OPTIONS_RANDOM );
				if ($rand['time'] != date('w')) {
					$rand['image'] = $this->get_randomImage();
					$rand['time'] = date('w');
					update_option(ADS_OPTIONS_RANDOM , $rand );
				}
				$options['image'] = $rand['image'];
				break;
			case 4:	// month
				$rand = adswsc_GetOptions( ADS_OPTIONS_RANDOM );
				if ($rand['time'] != date('m')) {
					$rand['image'] = $this->get_randomImage();
					$rand['time'] = date('m');
					update_option(ADS_OPTIONS_RANDOM , $rand );
				}
				$options['image'] = $rand['image'];
				break;
			case 5: // minutes
				$rand = adswsc_GetOptions( ADS_OPTIONS_RANDOM );
				if ($rand['time'] != date('i')) {
					$rand['image'] = $this->get_randomImage();
					$rand['time'] = date('i');
					update_option(ADS_OPTIONS_RANDOM , $rand );
				}
				$options['image'] = $rand['image'];
				break;
			default: 
				break;
		}

		echo $args['before_widget'];
		if ( ! empty( $options['title'] ) )
			echo $args['before_title'] . $options['title'] . $args['after_title'];
		echo adswsc_GetViewCounter(null, $options); 
		echo $args['after_widget'];
	}
		
	private function get_randomImage($standard) {
		$Directory = ADS_COUNTER_DIR.'*.jpg';
		$FILES = glob($Directory);
		return (sizeof($FILES) == 0) ? $standard : basename($FILES[rand(0, sizeof($FILES)-1)]);
	}		

	
	// Widget Backend 
	public function form( $instance ) {
		$Directory = ADS_COUNTER_DIR.'*.jpg';
		$FILES = glob($Directory);
		if (sizeof($FILES) > 0)
			$default_file = basename($FILES[0]);
		$title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : __('Visitor counter', ADS_TEXT_DOMAIN);
		$before = isset( $instance[ 'before' ] ) ? $instance[ 'before' ] : __('Visits since 2014', ADS_TEXT_DOMAIN);
		$after = isset( $instance[ 'after' ] ) ? $instance[ 'after' ] : '%['.__('Welcome %dname', ADS_TEXT_DOMAIN).'\n]%'.
																			__('Your IP: %ip', ADS_TEXT_DOMAIN);
		$width = isset( $instance[ 'width' ] ) ? $instance[ 'width' ] : 180;
		$height = isset( $instance[ 'height' ] ) ? $instance[ 'height' ] : '';
		$whunit = isset( $instance[ 'whunit' ] ) ? $instance[ 'whunit' ] : 'px';
		$image = isset( $instance[ 'image' ] ) ? $instance[ 'image' ] : $default_file;
		$align = isset( $instance[ 'align' ] ) ? $instance[ 'align' ] : 'center';
		$length = isset( $instance[ 'length' ] ) ? $instance[ 'length' ] : 7;
		$fill = isset( $instance[ 'fill' ] ) ? $instance[ 'fill' ] : '';
		$text = isset( $instance[ 'text' ] ) ? $instance[ 'text' ] : '';
		$block = isset( $instance[ 'block' ] ) ? $instance[ 'block' ] : 'p';
		$docount = isset( $instance[ 'docount' ] ) ? $instance[ 'docount' ] : 'on';
		$randimg = isset( $instance[ 'randimg' ] ) ? $instance[ 'randimg' ] : 'on';
        		
		$options = array (
			'title' => $title,
			'before' => $before,
			'after' => $after,
			'width' => $width,
			'height' => $height,
			'whunit' => $whunit,
			'image' => $image,
			'align' => $align,
			'length' => $length,
			'fill' => $fill,
			'text' => $text,
			'block' => $block,
			'docount' => $docount,
			'randimg' => $randimg,
		);
			
		// Widget admin form
		// title
		echo '<p>';
		echo '<label for="'.$this->get_field_id( 'title' ).'">'.__('Title text', ADS_TEXT_DOMAIN).'</label><br>';
		echo '<input type="text" class="widefat" id="'.$this->get_field_id( 'title' ).'" name="'.$this->get_field_name( 'title' ).'" value="'.esc_attr( $title ).'" />';
		echo '</p>';
		
		// before
		echo '<p>';
		echo '<label for="'.$this->get_field_id( 'before' ).'">'.__('Text before counter', ADS_TEXT_DOMAIN).'</label><br>';
		echo '<input type="text" maxlength="50" class="widefat" id="'.$this->get_field_id( 'before' ).'" name="'.$this->get_field_name( 'before' ).'" value="'.esc_attr( $before ).'" />';
		echo '</p>';

		// after
		echo '<p>';
		echo '<label for="'.$this->get_field_id( 'before' ).'">'.__('Text after counter', ADS_TEXT_DOMAIN).'</label><br>';
		echo '<input type="text" maxlength="50" class="widefat" id="'.$this->get_field_id( 'after' ).'" name="'.$this->get_field_name( 'after' ).'" value="'.esc_attr( $after ).'" />';
		echo '</p>';

		
		// width/height
		echo '<p>';
		echo '<label for="'.$this->get_field_id( 'width' ).'">'.__('Counter size width / hight / unit', ADS_TEXT_DOMAIN).'</label><br>';
		echo '<div style="vertical-align:middle; white-space:nowrap;">';
		echo '<input type="text" style="width: 34%" maxlength="3" class="widefat" id="'.$this->get_field_id( 'width' ).'" name="'.$this->get_field_name( 'width' ).'" value="'.esc_attr( $width ).'"/>&nbsp';
		echo '<input type="text" style="width: 34%"  maxlength="3" class="widefat" id="'.$this->get_field_id( 'height' ).'" name="'.$this->get_field_name( 'height' ).'" value="'.esc_attr( $height ).'" />&nbsp';
		echo '<select  style="width: 30%" id="'.$this->get_field_id( 'whunit' ).'" name="'.$this->get_field_name( 'whunit' ).'" >';
		echo '<option value="%" '.($whunit == '%' ? "selected" : "" ).' >%</option>';
		echo '<option value="px" '.($whunit == 'px' ? "selected" : "" ).' >Pixels</option>';
		echo '<option value="pt" '.($whunit == 'pt' ? "selected" : "" ).' >Points</option>';
		echo '<option value="em" '.($whunit == 'em' ? "selected" : "" ).' >Ems</option>';
		echo '</select>';
		echo '</div>';
		echo '</p>';
		
		// image
		echo '<p>';
		echo '<label for="'.$this->get_field_id( 'image' ).'">'.__('Counter image', ADS_TEXT_DOMAIN).'</label>';
		echo '<select id="'.$this->get_field_id( 'image' ).'" name="'.$this->get_field_name( 'image' ).'" style="width:100%">';
		if (sizeof($FILES) == 0) {
			$tmp = __('Sorry, no counter image found', ADS_TEXT_DOMAIN);
			echo '<option value="'.$tmp.'" selected >'.$tmp.'</option>';
		} else {
			for($x = 0; $x < sizeof($FILES); $x++) { 
				echo '<option value="'.basename($FILES[$x]).'" '.($image == basename($FILES[$x]) ? "selected" : "" ).' >'.basename($FILES[$x]).'</option>';
			}
		}  
		echo '</select></p>';
		
		//Align and Length and Block
		echo '<label for="'.$this->get_field_id( 'align' ).'">'.__('Counter align, length and block type', ADS_TEXT_DOMAIN).'</label><br>';
		echo '<select  id="'.$this->get_field_id( 'align' ).'" name="'.$this->get_field_name( 'align' ).'" >';
		echo '<option value="left" '.($align == 'left' ? "selected" : "" ).' >'.__('left', ADS_TEXT_DOMAIN).'</option>';
		echo '<option value="right" '.($align == 'right' ? "selected" : "" ).' >'.__('right', ADS_TEXT_DOMAIN).'</option>';
		echo '<option value="center" '.($align == 'center' ? "selected" : "" ).' >'.__('center', ADS_TEXT_DOMAIN).'</option>';
		echo '<option value="" '.($align == '' ? "selected" : "" ).' >'.__('none', ADS_TEXT_DOMAIN).'</option>';
		echo '</select>&nbsp';
		echo '<select id="'.$this->get_field_id( 'length' ).'" name="'.$this->get_field_name( 'length' ).'" >';
		for($x = 0; $x <= 9; $x++) {	
			echo '<option value="'.$x.'" '.($length == $x ? "selected" : "" ).' >'.$x.'</option>';
		} 
		echo '</select>&nbsp';
		echo '<select  id="'.$this->get_field_id( 'block' ).'" name="'.$this->get_field_name( 'block' ).'" >';
		echo '<option value="p" '.($block == 'p' ? "selected" : "" ).' >p</option>';
		echo '<option value="div" '.($block == 'div' ? "selected" : "" ).' >div</option>';
		echo '<option value="" '.($block == '' ? "selected" : "" ).' >'.__('none', ADS_TEXT_DOMAIN).'</option>';
		echo '</select>';

		//fill
		echo '<p>';
		echo '<label for="'.$this->get_field_id( 'option' ).'">'.__('Other options', ADS_TEXT_DOMAIN).'</label><br>';
		echo '<input value="on" type="checkbox" id="'.$this->get_field_id( 'fill' ).'" name="'.$this->get_field_name( 'fill' ).'" '.($fill == "on" ? "checked" : "" ).' />'.__('fill with zerro', ADS_TEXT_DOMAIN).'<br>';
		
		//text
		echo '<input value="on" type="checkbox" id="'.$this->get_field_id( 'text' ).'" name="'.$this->get_field_name( 'text' ).'" '.($text == "on" ? "checked" : "" ).' />'.__('display as text', ADS_TEXT_DOMAIN).'<br>';
		
		//text
		echo '<input value="on" type="checkbox" id="'.$this->get_field_id( 'docount' ).'" name="'.$this->get_field_name( 'docount' ).'" '.($docount == "on" ? "checked" : "" ).' />'.__('counting activated', ADS_TEXT_DOMAIN);
		echo '</p>';
		
		//random counter
		echo '<p>';
		echo '<label for="'.$this->get_field_id( 'randimg' ).'">'.__('Random counter selected', ADS_TEXT_DOMAIN).'</label><br>';
		echo '<select  id="'.$this->get_field_id( 'randimg' ).'" name="'.$this->get_field_name( 'randimg' ).'" >';
		echo '<option value="0" '.($randimg == 0 ? "selected" : "" ).' >'.__('none', ADS_TEXT_DOMAIN).'</option>';
		echo '<option value="1" '.($randimg == 1 ? "selected" : "" ).' >'.__('hourly', ADS_TEXT_DOMAIN).'</option>';
		echo '<option value="2" '.($randimg == 2 ? "selected" : "" ).' >'.__('dayli', ADS_TEXT_DOMAIN).'</option>';
		echo '<option value="3" '.($randimg == 3 ? "selected" : "" ).' >'.__('weekly', ADS_TEXT_DOMAIN).'</option>';
		echo '<option value="4" '.($randimg == 4 ? "selected" : "" ).' >'.__('monthly', ADS_TEXT_DOMAIN).'</option>';
		echo '<option value="5" '.($randimg == 5 ? "selected" : "" ).' >'.__('every minute', ADS_TEXT_DOMAIN).'</option>';
		echo '</select>';
		echo '</p>';
		//show counter
		echo '<p>';
		$options['docount'] = '';
		echo adswsc_GetViewCounter(null, $options); 
		echo '</p>';
	}
	
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['before'] = ( ! empty( $new_instance['before'] ) ) ? strip_tags( $new_instance['before'] ) : '';
		$instance['after'] = ( ! empty( $new_instance['after'] ) ) ? strip_tags( $new_instance['after'] ) : '';
		$instance['width'] = ( ! empty( $new_instance['width'] ) ) ? strip_tags( $new_instance['width'] ) : '';
		$instance['height'] = ( ! empty( $new_instance['height'] ) ) ? strip_tags( $new_instance['height'] ) : '';
		$instance['whunit'] = ( ! empty( $new_instance['whunit'] ) ) ? strip_tags( $new_instance['whunit'] ) : '';
		$instance['image'] = ( ! empty( $new_instance['image'] ) ) ? strip_tags( $new_instance['image'] ) : '';
		$instance['align'] = ( ! empty( $new_instance['align'] ) ) ? strip_tags( $new_instance['align'] ) : '';
		$instance['length'] = ( ! empty( $new_instance['length'] ) ) ? strip_tags( $new_instance['length'] ) : '';
		$instance['fill'] = ( ! empty( $new_instance['fill'] ) ) ? strip_tags( $new_instance['fill'] ) : '';
		$instance['text'] = ( ! empty( $new_instance['text'] ) ) ? strip_tags( $new_instance['text'] ) : '';
		$instance['block'] = ( ! empty( $new_instance['block'] ) ) ? strip_tags( $new_instance['block'] ) : '';
		$instance['docount'] = ( ! empty( $new_instance['docount'] ) ) ? strip_tags( $new_instance['docount'] ) : '';
		$instance['randimg'] = ( ! empty( $new_instance['randimg'] ) ) ? strip_tags( $new_instance['randimg'] ) : '';
		return $instance;
	}
} 

