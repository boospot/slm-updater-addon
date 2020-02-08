<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/raoabid
 * @since      1.0.0
 *
 * @package    Slm_Updater_Addon
 * @subpackage Slm_Updater_Addon/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Slm_Updater_Addon
 * @subpackage Slm_Updater_Addon/includes
 * @author     Rao Abid <raoabid491@gmail.com>
 */
class Slm_Updater_Addon_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		$is_slm_plugin_active = ( is_plugin_active( 'software-license-manager/slm_bootstrap.php' ) ) ? true : false;

		if ( ! $is_slm_plugin_active ) {
			die( __( 'Software License Manager plugin needs to be active for this plugin to work', 'slm-updater-addon' ) );
		}


		$is_slm_plugin_active = ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) ? true : false;

		if ( ! $is_slm_plugin_active ) {
			die( __( 'Woocommerce plugin needs to be active for this plugin to work', 'slm-updater-addon' ) );
		}

	}

}
