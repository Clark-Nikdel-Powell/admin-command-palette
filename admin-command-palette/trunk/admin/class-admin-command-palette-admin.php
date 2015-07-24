<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Admin_Command_Palette
 * @subpackage Admin_Command_Palette/admin
 * @author     Your Name <email@example.com>
 */
class Admin_Command_Palette_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $admin_command_palette    The ID of this plugin.
	 */
	private $admin_command_palette;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The data objects we will be dealing with
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      object    $data    The data class containing cached or live data from database
	 */
	public $user_content;
	public $admin_pages;
	public $admin_actions;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $admin_command_palette       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $admin_command_palette, $version ) {

		$this->admin_command_palette = $admin_command_palette;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->admin_command_palette, plugin_dir_url( __FILE__ ) . 'css/admin-command-palette-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		$format = '';
		$format = get_option('acp_display_results_by_type');
		if ( is_array( $format ) && '1' == $format['group-by-type'] ) {
			$format = 'grouped';
		}

		$search_settings = array(
			'threshold' => get_option('acp_search_threshold'),
			'max_results_per_section' => get_option('acp_max_results_per_section'),
			'results_format' => $format
		);

		wp_enqueue_script( $this->admin_command_palette, plugin_dir_url( __FILE__ ) . 'js/admin.min.js', array( 'jquery' ), $this->version, true );

		// Add search settings to footer for use in admin.min.js.
		wp_localize_script( $this->admin_command_palette, 'acp_user_options', $search_settings );
		//wp_localize_script( $this->admin_command_palette, 'acp_search_data', $this->get_all_data() );

	}

	/**
	 * Register admin settings.
	 *
	 * @since    1.0.0
	 */
	public function register_settings() {

		register_setting( 'acp_options', 'acp_search_threshold' );
		register_setting( 'acp_options', 'acp_max_results_per_section' );
		register_setting( 'acp_options', 'acp_display_results_by_type' );
		register_setting( 'acp_options', 'acp_excluded_post_types' );
		register_setting( 'acp_options', 'acp_excluded_taxonomies' );

	}

	/**
	 * Register and display the admin settings page.
	 *
	 * @since    1.0.0
	 */
	public function settings_page() {

		add_options_page('Admin Command Palette', 'Admin Command Palette', 'manage_options', 'admin-command-palette', 'acp_options_page');

		function acp_options_page() {
			include_once('partials/plugin-admin-command-palette-display.php');
		}

	}


	/**
	 * Gets all the data to search through
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	public function get_all_data() {

		$user_content 	= $this->user_content->data;
		$admin_pages 	= $this->admin_pages->data;
		$admin_actions 	= $this->admin_actions->data;

		$all_data = array_merge($user_content, $admin_pages, $admin_actions);

		return $all_data;
		die();
	}


	/**
	 * Wordpress wrapper for getting all data
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	public function acp_gad() {
		header('Content-Type: application/json');
		echo json_encode($this->get_all_data());
		wp_die();
	}


	/**
	 * Clears the transient cache for all cached items
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	public function clear_cache() {

		$this->user_content->clear_transient();
		$this->admin_pages->clear_transient();
		$this->admin_actions->clear_transient();

		return true;
	}

}
