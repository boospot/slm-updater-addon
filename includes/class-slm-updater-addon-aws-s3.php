<?php
/**
 * A wrapper for our Amazon S3 API actions.
 *
 * @package    Wp_License_Manager
 * @subpackage Wp_License_Manager/includes
 * @author     Jarkko Laine <jarkko@jarkkolaine.com>
 */

class SlmUpdaterAddonAwsS3 {

	/**
	 * Returns a signed Amazon S3 download URL.
	 *
	 * @param $bucket       string  Bucket name
	 * @param $file_name    string  File name (URI)
	 *
	 * @return string       The signed download URL
	 */
	public static function get_s3_url( $aws_key, $aws_secret, $bucket_region, $bucket, $file_name , $version = 'latest' ) {
//		$options = get_option( 'da-license-manager-settings' );


		if(!class_exists( 'Aws\S3\S3Client')){

//			die('This class does not exit');
			require_once SLM_UPDATER_BASE_DIR . 'lib/aws/aws-autoloader.php';
		}

//		RaoUtils::var_dump( $bucket_region);

		$credentials = new Aws\Credentials\Credentials($aws_key, $aws_secret);

		$s3_client = new Aws\S3\S3Client(
			array(

				'region'      => $bucket_region,
				'version'     => $version,
				'credentials' => $credentials
			)
		);

		$command = $s3_client->getCommand('GetObject', [
			'Bucket' => $bucket,
			'Key' => $file_name
		]); // You'll have to send bucket and filename through the get_product() method in Wp_License_Manager_API

		$req = $s3_client->createPresignedRequest($command, '+1440 minutes');
		return $presignedUrl = (string) $req->getUri();
//		echo $presignedUrl = (string) $req->getUri(); // echo it to see the signed url

	}


}