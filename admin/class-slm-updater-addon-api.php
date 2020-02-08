<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/raoabid
 * @since      1.0.0
 *
 * @package    Slm_Updater_Addon
 * @subpackage Slm_Updater_Addon/admin
 */


class Slm_Updater_Addon_Api {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;


	private $license_status;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of this plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	public function modify_api_output( $args ) {

//        var_dump( $args);
//        die();
		$original_api_output = $args;

		if ( isset( $args['status'] ) && ( $args['status'] == 'active' ) ) {
			$this->license_status = $args['status'];


			// We are doing nothing is request is not from registered domain
			if ( ! $this->is_requesting_domain_registered( $args ) ) {

				$args['result']     = 'error';
				$args['message']    = 'Your Domain is not allowed to use this license.';
				$args['error_code'] = '9999';

				return $args;
			}


//			Get Product id from provided args
			$product_id = $this->get_product_id( $args );

//			Get Digital Asset related meta from product_id
			$digital_asset_meta = ( $product_id ) ? $this->get_woocommerce_digital_asset_meta_data( $product_id ) : false;

//          UpdateDigital Asset Meta with package Url
			$digital_asset_meta['download_link'] = $this->get_package_url( $product_id );
//			$digital_asset_meta->download_link = $this->get_package_url( $product_id );


//			Create Separate array element for Digital asset related info
			$digital_asset = array(
				'digital_asset' => $digital_asset_meta,
			);

//			Update original api output with our digital asset data
			$args = array_merge( $original_api_output, $digital_asset );

		}


		return $args;
	}

	protected function is_requesting_domain_registered( $args ) {


		if ( $this->license_status == 'pending' ) {
			//This is first time API is requested for this license.
			return true;
		}

		$requesting_domain = trim( wp_unslash( strip_tags( $_REQUEST['registered_domain'] ) ) );

		$registered_domains = $args['registered_domains'];

//		Setting initial value as false
		$is_valid_domain_request = false;

		foreach ( $registered_domains as $domain ) {
			$is_valid_domain_request = ( $domain->registered_domain === $requesting_domain ) ? true : false;
		}

		return $is_valid_domain_request;
	}

	public function get_product_id( $args ) {

		if ( isset( $args['license_key'] ) && ! empty( $args['license_key'] ) ) {

//			get license key from api output
			$license_key = $args['license_key'];

			return $product_id = $this->get_product_id_from_license_key( $license_key );

		}

		return false;

	}

	public function get_product_id_from_license_key( $license_key ) {

		global $wpdb;

		//Add to field to license key table if doesn't exist.
		$lic_key_table = SLM_TBL_LICENSE_KEYS;

//		$query      = "SELECT
//						    *
//						FROM
//						    %s
//						WHERE
//						    license_key = %s
//						LIMIT 1
//
//		";
//		$query_args = array( $lic_key_table, $license_key );
//		$prepare_statement = $wpdb->prepare( $query, $query_args );


		$query_statement = "SELECT * FROM {$lic_key_table} WHERE license_key = '{$license_key}' LIMIT 1";


		$result_row = $wpdb->get_row( $query_statement );
		$product_id = ( isset( $result_row->product_id ) ) ? $result_row->product_id : false;


		return $product_id;
	}

	public function get_woocommerce_digital_asset_meta_data( $product_id ) {

		$product_id = absint( $product_id );

		$product = wc_get_product( $product_id );


		$downloads           = $product->get_downloads();
		$download_path_array = array();
		if ( is_array( $downloads ) && ! empty( $downloads ) ) {

			foreach ( $downloads as $download_id => $download_object ) {

				$download_path = $product->get_file_download_path( $download_id );


				// Remove trailing slashes
				$path = untrailingslashit( ABSPATH );
				$relative_link = wp_make_link_relative( $download_path );

				$file_path = $path . $relative_link;

				$file_path_array[] = $file_path;


			}

		}

		if ( ! empty( $file_path_array ) ) {

			require_once SLM_UPDATER_BASE_DIR . 'includes/lib/wp-package-parser-develop/wp-package-parser.php';

			foreach ( $file_path_array as $index => $zip_file_path ) {

				/*
				 * Details: https://github.com/tutv/wp-package-parser
				 */

				$package = new Max_WP_Package( $zip_file_path );
				$read_me_data = $package->get_metadata();
			}


		}


		return $read_me_data;

//
//		$digital_asset_meta                            = array();
//		$digital_asset_meta['name']                    = $product->get_title();
//		$digital_asset_meta['homepage']                = get_permalink( $product_id ) . '#v=' . esc_html( get_post_meta( $product_id, '_asset_version', true ) );
//		$digital_asset_meta['version']                 = esc_html( get_post_meta( $product_id, '_asset_version', true ) );
//		$digital_asset_meta['tested']                  = esc_html( get_post_meta( $product_id, '_asset_tested_with_wordpress_version', true ) );
//		$digital_asset_meta['requires']                = esc_html( get_post_meta( $product_id, '_asset_requires_wordpress_version', true ) );
//		$digital_asset_meta['last_updated']            = esc_html( get_post_meta( $product_id, '_asset_last_updated_date', true ) );
//		$digital_asset_meta['sections']['description'] = $product->get_description();
//		$digital_asset_meta['sections']['changelog']   = 'This is change log';
//		$digital_asset_meta['banners']['low']          = esc_url_raw( get_post_meta( $product_id, '_asset_banner_low', true ) );
//		$digital_asset_meta['banners']['high']         = esc_url_raw( get_post_meta( $product_id, '_asset_banner_high', true ) );
//
//		return $digital_asset_meta;


	}

	/**
	 * @param $product_id
	 * @param $method
	 *
	 * @return bool|string
	 */
	public function get_package_url( $product_id ) {

		$method = get_post_meta( $product_id, '_asset_hosted_at', true );

		switch ( $method ) {
			case( 'aws' ):

				return $this->get_package_url_aws( $product_id );
				break;

			default:
//				TODO: Add more Methods to get digital asset package url for downloading zip
//				return get_post_meta( $product_id, '_asset_banner_high', true );
				return false;


		}


	}

	protected function get_package_url_aws( $product_id ) {

		//TODO: get aws_key and secret from plugin options
		$aws_key       = SLM_AWS_KEY;
		$aws_secret    = SLM_AWS_SECRET;
		$bucket_region = get_post_meta( $product_id, '_aws_region', true );
		$bucket        = get_post_meta( $product_id, '_aws_bucket', true );
		$file_name     = get_post_meta( $product_id, '_aws_file', true );

		if ( $aws_key && $aws_secret && $bucket_region && $bucket && $file_name ) {
			$presigned_url = SlmUpdaterAddonAwsS3::get_s3_url( $aws_key, $aws_secret, $bucket_region, $bucket, $file_name );

			return esc_url_raw( $presigned_url );
		} else {
			return false;
		}
	}


}