<?php
/*
Plugin Name: TentBlogger Simple AdSense
Plugin URI: http://tentblogger.com/adsense-plugin/
Description: This simple plugin lets you embed <a href="http://tentblogger.com/adsense/">Google Adsense</a> code into strategic locations in your blog posts to maximize profit.
Version: 1.7
Author: TentBlogger
Author URI: http://tentblogger.com
License:

  Copyright 2012 - 2013 TentBlogger (info@tentblogger.com)

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

class TentBlogger_Adsense {

	/*--------------------------------------------*
	 * Attributes
	 *--------------------------------------------*/

	private $plugin_name;
	
	private $plugin_slug;
	
	private $code_settings;
	
	private $code_settings_key = 'code_settings';
	
	private $position_settings;
	
	private $position_settings_key = 'position_settings';
	
	private $display_settings;
	
	private $display_settings_key = 'display_settings';

	/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/
	 
	function __construct() {
		
		$this->plugin_name = __('TentBlogger Simple Adsense', 'tentblogger-adsense');
		$this->plugin_slug = 'tentblogger-adsense';
		
		
		load_plugin_textdomain('tentblogger-adsense', false, dirname( plugin_basename( __FILE__ )) . '/lang');
		
		add_action('init', array(&$this, 'load_settings'));
		add_action('admin_init', array(&$this, 'register_code_options'));
		add_action('admin_init', array(&$this, 'register_position_options'));
		add_action('admin_init', array(&$this, 'register_display_options'));
		add_action('admin_menu', array(&$this, 'init_tentblogger_adsense_menu'));
		add_action('admin_enqueue_scripts', array(&$this, 'load_scripts'));
		
		add_filter('the_content', array(&$this, 'tentblogger_adsense_content'));
		add_filter('the_excerpt', array(&$this, 'tentblogger_adsense_content'));
		
	} // end constructor

	/*--------------------------------------------*
	 * Core Functions
	 *--------------------------------------------*/
	 
	public function init_tentblogger_adsense_menu() {	
		if(is_admin()) { 
			if(!$this->my_menu_exists('tentblogger-handle')) {
	        	add_menu_page('TentBlogger', 'TB AdSense', 'administrator', 'tentblogger-adsense', array($this, 'display'));
			} // end if
			add_submenu_page('tentblogger-handle', 'TentBlogger', $this->plugin_name, 'administrator', $this->plugin_slug, array(&$this, 'display'));
		} // end if/else		
	} // end init_tentblogger_adsense_menu
	 
	public function load_settings() {
		
		$this->position_settings = (array)get_option($this->position_settings_key);
		$this->position_settings = array_merge(array('display_position' => 'Top of Post'), $this->position_settings);
		
		$this->display_settings = (array)get_option($this->display_settings_key);
		
		$this->code_settings = (array)get_option($this->code_settings_key);
		
	} // end load_settings
	
	public function display() {
		include_once('views/admin.php');
	} // end display

	public function tentblogger_adsense_content($content) {
				
		// Single Post
		if(!is_home() && (is_single() && $this->display_settings['display_single_post'] == 1)) {
		
			// Post age
			if(strtolower(trim($this->display_settings['single_post_age'])) == 'all posts') {
			
				if(strtolower(trim($this->position_settings['display_position'])) == 'top of post') {
					$content = $this->code_settings['adsense_code'] . $content;
				} // end if
				
				// Only display AdSense at the bottom if the more link isn't present.
				if((strtolower(trim($this->position_settings['display_position'])) == 'bottom of post') && (strpos($content, "more-link") == false)) {
					$content = $content . $this->code_settings['adsense_code'];
				} // end if
			
			} else {
		
				$day = time() - (int)($this->display_settings['single_post_age'] * 24 * 60 * 60);
				if(get_the_time('U') < $day) {
					
					if(strtolower(trim($this->position_settings['display_position'])) == 'top of post') {
						$content = $this->code_settings['adsense_code'] . $content;
					} // end if
					
					// Only display AdSense at the bottom if the more link isn't present.
					if((strtolower(trim($this->position_settings['display_position'])) == 'bottom of post') && (strpos($content, "more-link") == false)) {
						$content = $content . $this->code_settings['adsense_code'];
					} // end if
						
				} // end if
		
			} // end if
			
		} // end if
		
		// Single Page
		if(is_page() && $this->display_settings['display_single_page'] == 1) {
		
			if(strtolower(trim($this->position_settings['display_position'])) == 'top of post') {
				$content = $this->code_settings['adsense_code'] . $content;
			} // end if
			
			if((strtolower(trim($this->position_settings['display_position'])) == 'bottom of post')) {
				$content = $content . $this->code_settings['adsense_code'];
			} // end if
			
		} // end if
		
		// Front Page
		global $post;
		$recent_posts = get_posts('numberposts=1&orderby=post_date&order=DESC');
		$recent_post = $recent_posts[0];
		
		if(is_home() && $this->display_settings['display_front_page'] == 1 && (get_the_ID() == $recent_post->ID)) {
			
			if(strtolower(trim($this->position_settings['display_position'])) == 'top of post') {
				$content = $this->code_settings['adsense_code'] . $content;
			} // end if
			
			// Only display AdSense at the bottom if the more link isn't present.
			if((strtolower(trim($this->position_settings['display_position'])) == 'bottom of post') && (strpos($content, "more-link") == false)) {
				$content = $content . $this->code_settings['adsense_code'];
			} // end if
			
		} // end if
		
		return $content;
		
	} // end tentblogger_adsense_content

	/*--------------------------------------------*
	 * Code Functions
	 *--------------------------------------------*/

	public function register_code_options() {
	
		$code_section = 'code_section';
		
		register_setting(
			$this->code_settings_key,
			$this->code_settings_key
		);
		
		add_settings_section(
			$code_section,
			__('AdSense Code', 'tentblogger-adsense'),
			create_function(null, 'echo "Paste your AdSense code here.";'),
			$this->code_settings_key
		);
		
		add_settings_field(
			'adsense_code',
			__('Adsense Code', 'tentblogger-adsense'),
			array(&$this, 'adsense_code_option'),
			$this->code_settings_key,
			$code_section
		);
	
	} // end register_code_options
	
	public function adsense_code_option() {
		
		$option = '<textarea name="' . $this->code_settings_key . '[adsense_code]" rows="10" cols="75" >';
			$option .= esc_textarea($this->code_settings['adsense_code']);
		$option .= '</textarea>';
		
		echo $option;
			
	} // end display_position_option
	
	/*--------------------------------------------*
	 * Position Functions
	 *--------------------------------------------*/
	
	public function register_position_options() {
	
		$position_section = 'position_section';
		
		register_setting(
			$this->position_settings_key,
			$this->position_settings_key
		);
		
		add_settings_section(
			$position_section,
			__('Advertisement Position', 'tentblogger-adsense'),
			create_function(null, 'echo "This option controls where your advertisements are displayed. This option will apply to all posts and pages on which your adsense is displayed.";'),
			$this->position_settings_key
		);
		
		add_settings_field(
			'display_position',
			__('Position', 'tentblogger-adsense'),
			array(&$this, 'display_position_option'),
			$this->position_settings_key,
			$position_section
		);
	
	} // end register_position_options
	
	public function display_position_option() {
		
		$positions = array(
			__('Top of Post', 'tentblogger-adsense'),
			__('Bottom of Post', 'tentblogger-adsense')
		);
		
		$option = '<select name="' . $this->position_settings_key . '[display_position]">';
			$option .= __('Select a Position', 'tentblogger-adsense');
			foreach($positions as $position) {
				$selected = $this->position_settings['display_position'] == $position ? 'selected="selected"' : '';
				$option .= '<option value="' . $position . '" ' . $selected . '>' . $position . '</option>';
			} // end foreach
		$option .= '</select>';
		$option .= '&nbsp<span class="description">' . __('Note that advertisements will not display below the "Continue Reading..." tag.', 'tentblogger-adsense') . '</span>';
		
		echo $option;
		
	} // end display_position_option
	
	/*--------------------------------------------*
	 * Display Functions
	 *--------------------------------------------*/
	 
	public function register_display_options() {
	
		$display_section = 'display_section';
		
		register_setting(
			$this->display_settings_key,
			$this->display_settings_key
		);
		
		add_settings_section(
			$display_section,
			__('Advertisement Display Settings', 'tentblogger-adsense'),
			create_function(null, 'echo "Specify where you want your advertisements to appear throughout your blog.";'),
			$this->display_settings_key
		);
		
		add_settings_field(
			'display_front_page',
			__('Front Page', 'tentblogger-adsense'),
			array(&$this, 'display_front_page_option'),
			$this->display_settings_key,
			$display_section
		);
		
		add_settings_field(
			'display_single_page',
			__('Single Pages', 'tentblogger-adsense'),
			array(&$this, 'display_single_page_option'),
			$this->display_settings_key,
			$display_section
		);

		add_settings_field(
			'display_single_post',
			__('Single Posts', 'tentblogger-adsense'),
			array(&$this, 'display_single_post_option'),
			$this->display_settings_key,
			$display_section
		);

		add_settings_field(
			'single_post_age',
			__('When To Display Ads?', 'tentblogger-adsense'),
			array(&$this, 'single_post_age_option'),
			$this->display_settings_key,
			$display_section
		);
	
	} // end register_position_options
	
	public function display_front_page_option() {
	
		$option = '<input type="checkbox" name="' . $this->display_settings_key . '[display_front_page]" value="1"' . checked($this->display_settings['display_front_page'], 1, false)  . ' />';		
		$option .= '&nbsp;';
		$option .= '<label>' . __('Display advertisements on the front page?', 'tentblogger-adsense') . '</label>';
		
		echo $option;
	
	} // end display_front_page_option

	public function display_single_page_option() {

		$option = '<input type="checkbox" name="' . $this->display_settings_key . '[display_single_page]" value="1"' . checked($this->display_settings['display_single_page'], 1, false)  . ' />';		
		$option .= '&nbsp;';
		$option .= '<label>' . __('Display advertisements on single pages (not posts)?', 'tentblogger-adsense') . '</label>';
		
		echo $option;


	} // end display_single_page_option

	public function display_single_post_option() {

		$option = '<input type="checkbox" name="' . $this->display_settings_key . '[display_single_post]" value="1"' . checked($this->display_settings['display_single_post'], 1, false)  . ' />';		
		$option .= '&nbsp;';
		$option .= '<label>' . __('Display advertisements on individual posts?', 'tentblogger-adsense') . '</label>';
		
		echo $option;
		
	} // end display_single_post_option
	
	public function single_post_age_option() {
	
		$age = array();
		for($i = 1; $i < 15; $i++) {
			array_push(&$age, $i);
		} // end for
	
		$option = '<select name="' . $this->display_settings_key . '[single_post_age]">';	
			$option .= '<option>' . __('All Posts', 'tentblogger-adsense') . '</option>';
			foreach($age as $day) {
				$selected = $this->display_settings['single_post_age'] == $day ? 'selected="selected"' : '';
				$option .= '<option value="' . $day . '" ' . $selected . '>' . $day . '</option>';
			} // end foreach
		$option .= '</select>';
		$option .= '<span class="description">&nbsp;' . __('Advertisements will be displayed on posts that are older than this setting.', 'tentblogger-adsense'). '</span>';
		
		echo $option;
		
	} // end single_post_age_option
	
	/*--------------------------------------------*
	 * Helper Functions
	 *--------------------------------------------*/
	 
	public function load_scripts() {
		wp_register_script('tentblogger-adsense-admin',  WP_PLUGIN_URL . '/tentblogger-adsense-plugin/js/admin.js', array('jquery'));
		wp_enqueue_script('tentblogger-adsense-admin');
	} // end load_scripts
	 
	/**
	 * http://wordpress.stackexchange.com/questions/6311/how-to-check-if-an-admin-submenu-already-exists
	 */
	private function my_menu_exists( $handle, $sub = false){
	
		if( !is_admin() || (defined('DOING_AJAX') && DOING_AJAX) )
		  return false;
		global $menu, $submenu;
		$check_menu = $sub ? $submenu : $menu;
		if( empty( $check_menu ) )
		  return false;
		foreach( $check_menu as $k => $item ){
		  if( $sub ){
		    foreach( $item as $sm ){
		      if($handle == $sm[2])
		        return true;
		    }
		  } else {
		    if( $handle == $item[2] )
		      return true;
		  }
		}
		return false;
	} // end my_menu_exists
  

} // end class
new TentBlogger_Adsense();

?>