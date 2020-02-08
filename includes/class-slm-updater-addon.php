<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/raoabid
 * @since      1.0.0
 *
 * @package    Slm_Updater_Addon
 * @subpackage Slm_Updater_Addon/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Slm_Updater_Addon
 * @subpackage Slm_Updater_Addon/includes
 * @author     Rao Abid <raoabid491@gmail.com>
 */
class Slm_Updater_Addon {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Slm_Updater_Addon_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
			$this->version = PLUGIN_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'slm-updater-addon';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Slm_Updater_Addon_Loader. Orchestrates the hooks of the plugin.
	 * - Slm_Updater_Addon_i18n. Defines internationalization functionality.
	 * - Slm_Updater_Addon_Admin. Defines all hooks for the admin area.
	 * - Slm_Updater_Addon_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {


		/*
		 * Helper functions
		 */

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-helper-functions.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-slm-updater-addon-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-slm-updater-addon-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-slm-updater-addon-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-slm-updater-addon-public.php';

		$this->loader = new Slm_Updater_Addon_Loader();

		/*
		 * Require Woocommerce Related Details
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-slm-updater-addon-woocommerce.php';


		/**
		 * A wrapper class for our Amazon S3 connectivity.
		 */
		
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-slm-updater-addon-aws-s3.php';


		/*
		 * Require Api Modifier Class
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-slm-updater-addon-api.php';


	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Slm_Updater_Addon_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Slm_Updater_Addon_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Slm_Updater_Addon_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );



		/*
		 * Woocommerce Related Action and Filters
		 */

		$slm_updater_woocommerce = new Slm_Updater_Addon_Woocommerce( $this->get_plugin_name(), $this->get_version() );

		// Add Tab to Woocommerce
		$this->loader->add_action( 'woocommerce_product_data_tabs', $slm_updater_woocommerce, 'add_da_file_details_tab' , 99 , 1);
		// Add fields to the tab.
		$this->loader->add_action( 'woocommerce_product_data_panels', $slm_updater_woocommerce, 'add_da_file_details_tab_fields' , 99);

		// save newly added fields
		$this->loader->add_action( 'woocommerce_process_product_meta', $slm_updater_woocommerce, 'save_da_file_details_tab_fields' , 99);


		/*
		 * Api Related Actions and filters
		 */

		$slm_updater_api = new Slm_Updater_Addon_Api( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_filter( 'slm_api_response_args', $slm_updater_api, 'modify_api_output' , 99, 1);



	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Slm_Updater_Addon_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Slm_Updater_Addon_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
