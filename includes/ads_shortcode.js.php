<?php
/*
 handles the settings for the WpSiteCount plugin
 Date : 2014/08/26
 Author: ad-software, André
*/ 
    require_once('../../../../wp-load.php');
    require_once('../../../../wp-admin/includes/admin.php');
    do_action('admin_init');
    
    if ( ! is_user_logged_in() )
       die('You must be logged in to access this script.');

	$Directory = ADS_COUNTER_DIR.'*.jpg';
	$FILES = glob($Directory);
	$options = adswsc_GetOptions( ADS_OPTIONS_SHORTCODE );

?>
(function() {
	tinymce.create('tinymce.plugins.adswsc_sc', {
		init : function(ed, url) {
			ed.addButton(
				'adswsc_sc', {
					title : '<?php echo ADS_PLUGIN_NAME .": ". __("Insert Shortcode", ADS_TEXT_DOMAIN); ?>',
					image: url + '/../images/scbutton.png',  //icon: 'adsicon', //
					onclick: function() {
						ed.windowManager.open( {
							//width: 700,
							//height: 600,
							title: '<?php echo __("Insert Shortcode", ADS_TEXT_DOMAIN); ?>',
							body: [ 
								{type: 'textbox', name: 'xcount', label: '<?php echo __('Counter value', ADS_TEXT_DOMAIN); ?>',value: ''},
								{type: 'listbox', name: 'ximage', label: '<?php echo __('Image', ADS_TEXT_DOMAIN); ?>', 'values': [
									{text: '', value: ''},
							<?php 
								if (sizeof($FILES) > 0)
									foreach($FILES as $FILE) 
										echo "{text: '".basename($FILE)."', value: '".basename($FILE)."'},";
							?>
								]},
								{type: 'textbox', name: 'xbefore', label: '<?php echo __('Text before counter', ADS_TEXT_DOMAIN); ?>',value: '',
									onPostRender: function() {
										ed.irstyle_control = this;
										this.value('<?php echo $options['before']; ?>');
								}},
								{type: 'textbox',name: 'xafter',label: '<?php echo __('Text after counter', ADS_TEXT_DOMAIN); ?>',value: '',
									onPostRender: function() {
										ed.irstyle_control = this;
										this.value('<?php echo $options['after']; ?>');
								}},
								{type: 'listbox',name: 'xalign',label: '<?php echo __('Align', ADS_TEXT_DOMAIN); ?>','values': [
									{text: '', value: ''},
									{text: '<?php echo __('left', ADS_TEXT_DOMAIN); ?>', value: 'left'},
									{text: '<?php echo __('right', ADS_TEXT_DOMAIN); ?>', value: 'right'},
									{text: '<?php echo __('center', ADS_TEXT_DOMAIN); ?>', value: 'center'}
								],
									onPostRender: function() {
										ed.irstyle_control = this;
										this.value('<?php echo $options['align']; ?>');
								}},
								{type: 'textbox', name: 'xwidth', label: '<?php echo __('Width', ADS_TEXT_DOMAIN); ?>',value: '',
									onPostRender: function() {
										ed.irstyle_control = this;
										this.value('<?php echo $options['width']; ?>');
								}},
								{type: 'textbox', name: 'xheight', label: '<?php echo __('Height', ADS_TEXT_DOMAIN); ?>',value: '',
									onPostRender: function() {
										ed.irstyle_control = this;
										this.value('<?php echo $options['height']; ?>');
								}},
								{type: 'listbox',name: 'xwhunit',label: '<?php echo __('Unit', ADS_TEXT_DOMAIN); ?>','values': [
									{text: '', value: ''},
									{text: '%', value: '%'},
									{text: 'pixels', value: 'px'},
									{text: 'points', value: 'pt'},
									{text: 'ems', value: 'en'}
								],
									onPostRender: function() {
										ed.irstyle_control = this;
										this.value('<?php echo $options['whunit']; ?>');
								}},
								{type: 'textbox', name: 'xmaxw', label: '<?php echo __('Max image size', ADS_TEXT_DOMAIN); ?>',value: '',
									onPostRender: function() {
										ed.irstyle_control = this;
										this.value('<?php echo $options['imgmaxw']; ?>');
								}},
								{type: 'listbox',name: 'xlength',label: '<?php echo __('Digits', ADS_TEXT_DOMAIN); ?>', default: '5', 'values': [
									{text: '', value: ''},
									{text: '0', value: '0'},
									{text: '1', value: '1'},
									{text: '2', value: '2'},
									{text: '3', value: '3'},
									{text: '4', value: '4'},
									{text: '5', value: '5'},
									{text: '6', value: '6'},
									{text: '7', value: '7'},
									{text: '8', value: '8'},
									{text: '9', value: '9'}
									],
									onPostRender: function() {
										// Select the third item by default
										ed.irstyle_control = this;
										this.value(<?php echo $options['length']; ?>);
									}
								},
								{type: 'checkbox', name: 'xfill', label: '<?php echo __('Fill zero', ADS_TEXT_DOMAIN); ?>', value: '',
									onPostRender: function() {
										ed.irstyle_control = this;
										this.value(<?php echo $options['fill']== 'on' ? 1 : 0; ?>);
								}},
								{type: 'checkbox', name: 'xtext', label: '<?php echo __('Text counter', ADS_TEXT_DOMAIN); ?>', value: '',
									onPostRender: function() {
										ed.irstyle_control = this;
										this.value(<?php echo $options['text']== 'on' ? 1 : 0; ?>);
								}},
								{type: 'listbox',name: 'xblock',label: '<?php echo __('Block type', ADS_TEXT_DOMAIN); ?>','values': [
									{text: '', value: ''},
									{text: 'div', value: 'div'},
									{text: 'p', value: 'p'}
									],
									onPostRender: function() {
										ed.irstyle_control = this;
										this.value('<?php echo $options['block']; ?>');
									}
								}
							],
							onsubmit: function( e ) {
								x = '[ads-wpsitecount';
								x += (e.data.xcount != '') 	? ' count='+e.data.xcount : '';
								x += (e.data.ximage != '') 	? ' image="'+e.data.ximage+'"' : '';
								x += (e.data.xbefore != '') ? ' before="'+e.data.xbefore+'"': '';
								x += (e.data.xafter != '') 	? ' after="'+e.data.xafter+'"': '';
								x += (e.data.xalign != '') 	? ' align="'+e.data.xalign+'"': '';
								x += (e.data.xwidth != '') 	? ' width='+e.data.xwidth: '';
								x += (e.data.xheight != '') ? ' height='+e.data.xheight: '';
								x += (e.data.xwhunit != '') ? ' wgunit="'+e.data.xwhunit+'"': '';
								x += (e.data.xmaxw != '') 	? ' imgmaxw='+e.data.xmaxw: '';
								x += (e.data.xlength != '') ?  ' length='+e.data.xlength: '';
								x += (e.data.xfill != '') 	? ' fill="'+e.data.xfill+'"': '';
								x += (e.data.xtext != '') 	?' text="'+e.data.xtext+'"': '';
								x += (e.data.xblock != '') 	? ' block="'+e.data.xblock+'"': '';
								x += ']';
								ed.focus();
								ed.insertContent( x );
							}
						},
						{
							plugin_url : url, // Plugin absolute URL
							some_custom_arg : 'custom arg' // Custom argument					
						});
					}
				}	
			);
		},
		createControl : function(n, cm) {
			return null;
		},
		getInfo : function() {
			return {
				longname : 'ADS-WpSiteCount',
				author : 'andrew',
				authorurl : 'http://www.ad-soft.ch',
				infourl : 'http://www.ad-soft.ch',
				version : "1.0"
			};
		}	
	});
	tinymce.PluginManager.add('adswsc_sc', tinymce.plugins.adswsc_sc);
})();
