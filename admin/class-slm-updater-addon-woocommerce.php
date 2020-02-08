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


class Slm_Updater_Addon_Woocommerce {

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


	public function add_da_file_details_tab( $product_data_tabs ) {

		$product_data_tabs['da-file-details'] = array(
			'class'  => array( 'show_if_simple', 'show_if_simple' ),
			'label'  => __( 'Digital Assets File Details ', 'slm-updater-addon' ),
			'target' => 'da_file_details',
		);

		return $product_data_tabs;


	}

	public function add_da_file_details_tab_fields() {

		global $woocommerce, $post;
		?>
        <!-- id below must match target registered in above add_product_license_data_tab function -->
        <div id="da_file_details" class="panel woocommerce_options_panel">
            <p><?php


				woocommerce_wp_text_input( array(
					'id'          => '_asset_version',
					'label'       => __( 'Digital Asset Version', 'slm-updater-addon' ),
					'description' => __( 'This version number shall be used to check if the client needs an update', 'slm-updater-addon' ),
				) );

				woocommerce_wp_text_input( array(
					'id'    => '_asset_tested_with_wordpress_version',
					'label' => __( 'Tested with Wordpress Version', 'slm-updater-addon' ),
//		            'description' => __( 'This version number shall be used to check if the client needs an update', 'slm-updater-addon' ),
				) );

				woocommerce_wp_text_input( array(
					'id'    => '_asset_requires_wordpress_version',
					'label' => __( 'Requires WordPress version', 'slm-updater-addon' ),
//		            'description' => __( 'This version number shall be used to check if the client needs an update', 'slm-updater-addon' ),
				) );

				woocommerce_wp_text_input( array(
					'id'          => '_asset_last_updated_date',
					'label'       => __( 'Last Updated', 'slm-updater-addon' ),
					'description' => __( 'The Date when the asset was last updated in format 2000-12-31', 'slm-updater-addon' ),
				) );

				woocommerce_wp_text_input( array(
					'id'          => '_asset_banner_low',
					'label'       => __( 'URL to Banner image', 'slm-updater-addon' ),
					'description' => sprintf( "%s 772x250px", __( 'image dimensions should be', 'slm-updater-addon' ) )
				) );

				woocommerce_wp_text_input( array(
					'id'          => '_asset_banner_high',
					'label'       => __( 'URL to Banner image - Ratina', 'slm-updater-addon' ),
					'description' => sprintf( "%s 1544x500px", __( 'image dimensions should be', 'slm-updater-addon' ) )
				) );


	            woocommerce_wp_select(
		            array(
			            'id'      => '_asset_hosted_at',
			            'label'   => __( 'Where .zip file is hosted?', 'woocommerce' ),
			            'options' => array(
				            'aws'   => __( 'AWS', 'woocommerce' ),
//				            'two'   => __( 'Option 2', 'woocommerce' ),
//				            'three' => __( 'Option 3', 'woocommerce' )
			            ),
                        'description' => __( 'Only Amazon Web Service S3 service is available at the moment', 'slm-updater-addon' ),
                        'desc_tip' => true,

		            )
	            );

	            _e( '<p>If this downloadable product is hosted at AWS, fill below.<p>', 'slm-updater-addon' );

	            woocommerce_wp_text_input( array(
		            'id'          => '_aws_region',
		            'label'       => __( 'AWS Region', 'slm-updater-addon' ),
		            'description' => __( 'use region id like "eu-central-1" where bucket resides', 'slm-updater-addon' ),
//					'desc_tip' => 'true',
//					'placeholder' => __('LIKE: eu-central-1','slm-updater-addon'),
	            ) );


	            woocommerce_wp_text_input( array(
		            'id'          => '_aws_bucket',
		            'label'       => __( 'AWS S3 Bucket id', 'slm-updater-addon' ),
		            'description' => __( 'use bucket id like "my-premium-plugins"', 'slm-updater-addon' ),
//					'desc_tip' => 'false',
//					'placeholder' => __('Like: my-premium-plugins','slm-updater-addon'),
	            ) );

	            woocommerce_wp_text_input( array(
		            'id'          => '_aws_file',
		            'label'       => __( 'AWS .zip File name', 'slm-updater-addon' ),
		            'description' => __( 'use file name like "my-plugin.zip"', 'slm-updater-addon' ),
//					'desc_tip' => 'false',
//					'placeholder' => __('my-premium-plugins','slm-updater-addon'),
	            ) );



				//	            woocommerce_form_field( '_order_pickup_date', array(
				//		            'type'          => 'text',
				//		            'class'         => array('my-field-class form-row-wide'),
				//		            'id'            => 'datepicker',
				//		            'required'      => true,
				//		            'label'         => __('Delivery Date'),
				//		            'placeholder'       => __('Select Date'),
				//
				//	            ));

				//				woocommerce_wp_text_input( array(
				//					'id' => '_product_license_count',
				//					'label' => __('Product License Count','slm-updater-addon'),
				//					'description' => __('How many domains','slm-updater-addon'),
				//					'desc_tip' => '1',
				//					'placeholder' => __('1','slm-updater-addon'),
				//					'default'       => '1'
				//				));
				?>
                <script>
                    jQuery(function ($) {
                        $("#_asset_last_updated_date").datepicker({
                            dateFormat: "yy-mm-dd"
                        });
                    });
                </script>
        </div>
		<?php


	}


	public function save_da_file_details_tab_fields( $post_id ) {


		update_post_meta( $post_id, '_asset_version', sanitize_text_field( $_POST['_asset_version'] ) );
		update_post_meta( $post_id, '_asset_tested_with_wordpress_version', sanitize_text_field( $_POST['_asset_tested_with_wordpress_version'] ) );
		update_post_meta( $post_id, '_asset_requires_wordpress_version', sanitize_text_field( $_POST['_asset_requires_wordpress_version'] ) );
		update_post_meta( $post_id, '_asset_last_updated_date', sanitize_text_field( $_POST['_asset_last_updated_date'] ) );
		update_post_meta( $post_id, '_asset_banner_low', esc_url_raw( $_POST['_asset_banner_low'] ) );
		update_post_meta( $post_id, '_asset_banner_high', esc_url_raw( $_POST['_asset_banner_high'] ) );


		update_post_meta( $post_id, '_asset_hosted_at', sanitize_key( $_POST['_asset_hosted_at'] ) );
		update_post_meta( $post_id, '_aws_region', sanitize_key( $_POST['_aws_region'] ) );
		update_post_meta( $post_id, '_aws_bucket', sanitize_key( $_POST['_aws_bucket'] ) );
		update_post_meta( $post_id, '_aws_file', sanitize_file_name( $_POST['_aws_file'] ) );

	}


}