<?php
/**
 * Created by PhpStorm.
 * User: Abid
 * Date: 24-Apr-18
 * Time: 5:33 PM
 */

/*
 * Th Following Set of Functions are generic
 */

class RaoUtils {



	public static function var_dump($param){

		echo "<pre>";
		var_dump( $param);
		echo "<pre>";
		die();
	}
}


function wpsrecipe_return_markup_author_name_with_link() {
	global $post;
	$html = '';

	if ( isset( $post->post_author ) ) {
		$display_name = get_the_author_meta( 'display_name', $post->post_author );

		if ( empty( $display_name ) ) {
			$display_name = get_the_author_meta( 'nickname', $post->post_author );
		}

		// Get author's website URL
		$user_website = get_the_author_meta( 'url', $post->post_author );

		if ( ! empty( $user_website ) ) {
			$html .= "<a href='{$user_website}' target='_blank' rel='nofollow'>{$display_name}</a>";
		} else {
			$html .= "{$display_name}";
		}

	}

	return $html;
}

function wpsrecipe_sanitize_float( $number_input ) {

	return $number_input = ( isset( $number_input ) && ! empty( $number_input ) ) ? filter_var( $number_input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) : '';

}

function wpsrecipe_sanitize_textarea( $value ) {

	$value = implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $value ) ) );
//	$value = implode( PHP_EOL, array_map( 'sanitize_text_field', explode( PHP_EOL, $value ) ) );

//	$value = base64_encode($value);


//	$value = wpautop ($value);


	return $value;
}


function wpsrecipe_sanitize_absint( $number_input ) {
	return $number_input = ( isset( $number_input ) && ! empty( $number_input ) ) ? absint( $number_input ) : null;
}


/*
 * The Following Set of Functions are specific to recipes
 */

function wpsrecipe_get_recipe_meta( $key = null ) {

	$recipe_meta_array = get_post_meta( get_the_ID(), 'wpsrecipe-recipe-meta', true );


	if ( $key == null ) {
		return $recipe_meta_array;
	}

	if ( isset( $recipe_meta_array[ $key ] ) ) {
		return $recipe_meta_array[ $key ];
	} else {
		return false;
	}


}

function wpsrecipe_get_recipe_meta_object() {

	return $recipe_meta_object = (object) wpsrecipe_get_recipe_meta();

}


function wpsrecipe_return_markup_external_author() {

	$post_id = get_the_ID();

	$external_author_name = wpsrecipe_get_recipe_meta( 'external_author_name' );
	$external_author_url  = wpsrecipe_get_recipe_meta( 'external_author_link' );


	$html = '';

	if ( ! empty( $external_author_name ) ) {
		$display_name = $external_author_name;


		if ( ! empty( $external_author_url ) ) {
			$html .= "<a href='{$external_author_url}' target='_blank' rel='nofollow'>{$display_name}</a>";
		} else {
			$html .= "{$display_name}";
		}

	} else {
		$html .= __( 'Anonymous', 'wpsrecipe' );
	}

	return $html;
}

function wpsrecipe_return_markup_recipe_author_name() {


	$is_external_author = wpsrecipe_get_recipe_meta( 'is_external_author' );


	if ( $is_external_author == 'yes' ) {
		$posttype_author = wpsrecipe_return_markup_external_author();
	} else {
		$posttype_author = wpsrecipe_return_markup_author_name_with_link();
	}

	return $posttype_author;
}


function wpsrecipe_get_posttype_image_url( $post_id = null, $size = null ) {

	$image_size = ( $size != null ) ? $size : 'recipe-image';

	$post_id = ( $post_id != null ) ? $post_id : get_the_ID();

	$posttype_featured_image = get_the_post_thumbnail_url( $post_id, $image_size );

	if ( ! empty( $posttype_featured_image ) && ! false ) {
		return $posttype_featured_image;
	} else {
		return wpsrecipe_default_posttype_image( get_post_type() );
	}

}

function wpsrecipe_default_posttype_image( $post_type ) {
	$options = get_option( 'wpsrecipe-options' );

	if ( isset( $options['recipe_default_img_url'] ) && ! empty( $options['recipe_default_img_url'] ) ) {
		return esc_url_raw( $options['recipe_default_img_url'] );
	}

	return WPSRECIPE_PLUGIN_URL . "assets/images/{$post_type}-default-image.png";
}


function wpsrecipe_get_taxonomy_terms( $taxonomy ) {
//	global $post;

//	$taxonomy = 'wss_recipe_category';

// Get the term IDs assigned to post.
	$post_terms = wp_get_object_terms( get_the_ID(), $taxonomy, array( 'fields' => 'ids' ) );

// Separator between links.
	$separator = ', ';

	if ( ! empty( $post_terms ) && ! is_wp_error( $post_terms ) ) {

		$term_ids = implode( ',', $post_terms );

		$terms = wp_list_categories( array(
			'title_li' => '',
			'style'    => 'none',
			'echo'     => false,
			'taxonomy' => $taxonomy,
			'include'  => $term_ids
		) );


		$terms = rtrim( trim( str_replace( '<br />', $separator, $terms ) ), $separator );

		// Display post categories.
		return $terms;
	}
}


function wpsrecipe_get_taxonomy_terms_options_markup( $taxonomy_name_or_args_array ) {

	if ( is_array( $taxonomy_name_or_args_array ) ) {
		$taxonomy_name  = isset( $taxonomy_name_or_args_array['taxonomy'] ) ? $taxonomy_name_or_args_array['taxonomy'] : 0;
		$taxonomy_terms = get_terms( $taxonomy_name_or_args_array );

	} else {
		$taxonomy_name  = $taxonomy_name_or_args_array;
		$taxonomy_terms = get_terms( array(
			'taxonomy'   => $taxonomy_name_or_args_array,
			'hide_empty' => true
		) );
	}

	// Get Variables from GET array
	$get_taxonomy_term_from_url = ( isset( $_GET[ $taxonomy_name ] ) ) ? sanitize_key( $_GET[ $taxonomy_name ] ) : '';


	$output = false;

	if ( ! empty( $taxonomy_terms ) ) :
//		var_dump( $taxonomies);
		foreach ( $taxonomy_terms as $term ) {
//			echo "<pre>";
//			var_dump( $term);
//	        echo "</pre>";
			$selected_recipe = '';

			if ( $term->slug == $get_taxonomy_term_from_url ) {
				$selected_recipe = "selected=selected";
			}
			$output .= "<option value='$term->slug' {$selected_recipe}>{$term->name}</option>";
		}

	endif;

	return $output;
}


function wpsrecipe_get_skill_level_options_markup() {

	// Get Variables from GET array
	$get_skill_level_from_url = ( isset( $_GET['skill_level'] ) ) ? sanitize_key( $_GET['skill_level'] ) : '';


	$skill_levels = array(
		'easy'   => __( 'Easy', 'wpsrecipe' ),
		'medium' => __( 'Medium', 'wpsrecipe' ),
		'hard'   => __( 'Hard', 'wpsrecipe' ),
	);


	$output = false;

	foreach ( $skill_levels as $skill => $skill_name ) {
//			echo "<pre>";
//			var_dump( $term);
//	        echo "</pre>";
		$selected_skill = '';

		if ( $skill == $get_skill_level_from_url ) {
			$selected_skill = "selected=selected";
		}
		$output .= "<option value='$skill' {$selected_skill}>{$skill_name}</option>";
	}


	return $output;
}


function wpsrecipe_is_archive_query() {

	$queried_object = get_queried_object();

	$taxonomy_template = false;
	if ( get_class( $queried_object ) == 'WP_Term' ) {
		$taxonomy_template = ( ( $queried_object->taxonomy == 'recipe_category' || $queried_object->taxonomy == 'recipe_cuisine' ) || $queried_object->taxonomy == 'recipe_tags' ) ? true : false;
	}

	return $taxonomy_template;

}


function wpsrecipe_is_search_form_submitted() {

	$options                    = get_option( 'wpsrecipe-options' );
	$recipe_submit_button_label = ( isset( $options['recipe_submit_button_label'] ) && ! empty( $options['recipe_submit_button_label'] ) ) ? $options['recipe_submit_button_label'] : __( 'Search', 'wpsrecipe' );

	$is_search_form_submitted = (
		isset( $_GET['recipe_search'] )
		&&
		( sanitize_key( $_GET['recipe_search'] ) == strtolower( $recipe_submit_button_label ) )
	)
		? true : false;

	return $is_search_form_submitted;
}

/*
function wpsrecipe_is_recipe_author_query(){

	$recipe_author_query =  (isset($_GET['author'])) ? sanitize_key($_GET['author']) : false;

//var_dump( $recipe_author_query); die();
	return $recipe_author_query;
}
*/

function wpsrecipe_show_search_form() {

	$options                    = get_option( 'wpsrecipe-options' );
	$recipe_submit_button_label = ( isset( $options['show_search_form'] ) ) ? $options['show_search_form'] : 'yes';

	$is_show_search_form = ( 'yes' == $recipe_submit_button_label ) ? true : false;

	return $is_show_search_form;
}




/*
 * Un used Function
 */

/*

function wpsrecipe_get_posttype_meta($post_id, $meta_variables = null){
	$posttype_meta_values = array();

	if($post_id){
		$posttype_meta = get_post_meta($post_id);
	}

	$meta_variables = wpsrecipe_get_posttype_meta_variables(get_post_type($post_id)) ;


//echo  "<pre>";
//	var_dump( $meta_variables);
////	die();
//
//    var_dump( $posttype_meta);
////	die();
//echo  "</pre>";

	// Make sure we hae some data to work with
	if(!empty($meta_variables)  ){

		foreach($meta_variables as $meta_key){

			if(! isset($posttype_meta[$meta_key])){
				$posttype_meta[$meta_key] = '';
			}

		}

//		var_dump( $posttype_meta);

		foreach($posttype_meta as $key => $value){

			$key = str_replace("_wpsrecipe_", "", $key);
			$posttype_meta_values[$key] = $value;

			if (is_array( $value)){
				$posttype_meta_values[$key] = reset($value);
			} else {
				$value = str_replace("_wpsrecipe_", "", $value);
				$posttype_meta_values[$value] = '';
			}
		}

//		var_dump( $posttype_meta_values);

	}


	return $posttype_meta_values;

}

*/