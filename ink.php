<?php
/*
Plugin Name: INK - Own your content
Plugin URI: http://www.no-margin-for-errors.com/projects/ink-own-your-content/
Description: Installs INK on your wordpress blog so you get credited everytime someone copied and paste content from your blog.
						 The code to build this plugin has been re-worked from this plugin: http://wordpress.org/extend/plugins/browser-dns-prefetching/
						 Thanks @pluc! (http://twitter.com/pluc)
Version: 1.0.7
Author: Stephane Caron
Author URI: http://www.no-margin-for-errors.com
Text Domain: ink
License: http://www.opensource.org/licenses/mit-license.php
*/

class ink {
	public function enable() {
		add_action('admin_init', array($this, 'ink_admin_init'));
		add_action('admin_menu', array($this, 'ink_hooks_admin'));
		add_action('plugin_action_links_'.plugin_basename(__FILE__), array($this, 'ink_plugin_links'));

		add_action('wp_footer', array($this,'ink_footer'));
	}

	public function ink_footer(){
		$ink_status = get_option('ink_status');
		$ink_license = get_option('ink_license');
		if($ink_status == 'on'){
			$wpurl = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__), '', plugin_basename(__FILE__));
			
			if(!empty($ink_license)){
				echo '<script type="text/javascript" charset="utf-8">var ink_license="'.$ink_license.'"</script>';
			}
			
			echo '<script src="'.$wpurl.'js/INK.js" type="text/javascript" charset="utf-8"></script>';
		}
	}

  public function ink_admin_init() {
    register_setting('ink', 'ink_status');
	register_setting('ink', 'ink_license');
  }

  public function ink_hooks_admin() {
    add_options_page('ink-settings', __('INK'), 'edit_plugins', __FILE__, array($this, 'ink_options_page'));
  }

  public function ink_plugin_links($links) {
    $additionalLinks = array('<a href="options-general.php?page=ink-own-you-content/ink.php">'.__('Settings').'</a>');
    return array_merge($additionalLinks, $links);
  }

  public function ink_options_page() {
    echo '<div class="wrap"><h2>INK - Own your content</h2><form method="post" action="options.php">';
    $nonce = (function_exists('settings_fields'))? settings_fields('ink') : wp_nonce_field('update-options').'<input type="hidden" name="action" value="update" /><input type="hidden" name="page_options" value="ink_status" /><input type="hidden" name="page_options" value="ink_license" />';
    echo '<input type="hidden" name="action" value="update" />';
    echo '<h3>'.__('INK Status', 'ink').'</h3>';
    echo get_option('ink_status') == 'on'? '<input type="radio" name="ink_status" value="on" checked="checked" />On  <input type="radio" name="ink_status" value="off">Off<br />' : '<input type="radio" name="ink_status" value="on" />On  <input type="radio" name="ink_status" value="off" checked="checked">Off<br />';
	echo '<h3>'.__('INK License', 'ink').'</h3>';
	echo '<p>'.__('It is possible to disable ads in INK by buying a license.', 'ink').'</p>';
	echo '<p><a href="http://www.no-margin-for-errors.com/projects/ink-own-your-content/#premium" target="_blank">'.__('More information', 'ink').'</a></p>';
    echo '<label for="ink_license">'.__('License number: ', 'ink').'</label><input type="text" name="ink_license" value="'.get_option('ink_license').'" style="width:300px;" /><br />';
    echo '<p class="submit"><input type="submit" name="Submit" value="Save Changes" /></p></form></div>';
  }
}

//Enable the plugin for the init hook, but only if WP is loaded. Calling this php file directly will do nothing.
if(defined('ABSPATH') && defined('WPINC')) {
	// add_action("init",array("GoogleSitemapGeneratorLoader","Enable"),1000,0);
	$ink = new ink();

	add_action('init', array(&$ink, 'enable'));
}

?>
