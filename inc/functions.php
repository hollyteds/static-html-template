<?php
namespace Shct;
/**
 * Checks if the current screen is the static HTML editor.
 *
 * @return bool Returns true if the current screen is the static HTML editor, false otherwise.
 */
function is_edit( $post_type ) {
	$screen = get_current_screen();
	return $post_type === $screen->post_type;
}

/**
 * Checks if the current page is a static HTML page.
 *
 * @return bool Returns true if the current page is a static HTML page, false otherwise.
 */
function is_static_page( $post_id = null ) {

	// フロントページは除く
	$is_front_page = $post_id ? $post_id === (int) get_option( 'page_on_front' ) : is_front_page();
	if ( $is_front_page ) {
		return false;
	}

	// ブログページは除く
	$is_home = $post_id ? $post_id === (int) get_option( 'page_for_posts' ) : is_home();
	if ( $is_home ) {
		return false;
	}

	return $post_id ? SHC_TEMPLATE_NAME === get_page_template_slug( $post_id ) : is_page_template( SHC_TEMPLATE_NAME );
}

/**
 * Checks if an HTML file exists.
 *
 * @param string $file The path to the file.
 * @return bool Returns true if the file exists and has an extension of 'htm' or 'html', false otherwise.
 */
function is_html_file_exists( $file ) {
	// ファイルが存在するかを確認
	if ( is_file( $file ) ) {
		// ファイルの拡張子を取得
		$extension = pathinfo( $file, PATHINFO_EXTENSION );

		// 拡張子が 'htm' または 'html' なら true を返す
		return $extension === 'htm' || $extension === 'html';
	}

	// ファイルが存在しないか、拡張子が 'htm' または 'html' でない場合 false を返す
	return false;
}



/**
 * Retrieves the selected template ID for a given post.
 *
 * @param int|null $post_id The ID of the post. If not provided, the current post ID will be used.
 * @return int|null The selected template ID, or null if not found.
 */
function get_shc_id( $post_id = null ) {
	$post_id = $post_id ?? get_the_ID();
	return get_post_meta( $post_id, '_shct_selected_template_id', true );
}

/**
 * Retrieves the Static HTML Template options for a given post.
 *
 * @param int|null $shc_id The ID of the post "static_html_setting". If not provided, the current post ID will be used.
 * @return array|null The Static HTML Template options, or null if not found.
 */
function get_sht_options( $shc_id = null ) {
	$shc_id = $shc_id ?? get_the_ID();
	return get_post_meta( $shc_id, '_scht_settings', true );
}

/**
 * Retrieves the title of the currently selected Static HTML Template for a given post.
 *
 * @param int $post_id The ID of the post.
 * @return string|null The title of the selected template, or null if not found.
 */
function get_current_sht_template( $post_id ) {
	$shc_id = get_shc_id( $post_id );

	return get_the_title( $shc_id );
}

/**
 * Retrieves the HTML contents for a specific post or the current post.
 *
 * @param int|null $shc_id The ID of the post to retrieve the HTML contents for. Defaults to the current post if not provided.
 * @return string|null The HTML contents of the post, or null if not found.
 */
function get_html_contents( $shc_id = null ) {
	$shc_id = $shc_id ?? get_the_ID();
	return get_post_meta( $shc_id, '_scht_html', true );
}

/**
 * Retrieves the HTML contents for a specific post or the current post.
 *
 * @param int|null $shc_id The ID of the post to retrieve the HTML contents for. Defaults to the current post if not provided.
 * @return string|null The HTML contents of the post, or null if not found.
 */
function get_dynamic_links( $shc_id = null ) {
	$shc_id = $shc_id ?? get_the_ID();
	return get_post_meta( $shc_id, '_scht_dynamic_links', true );
}

/**
 * Retrieves the HTML contents for a specific post or the current post.
 *
 * @param int|null $shc_id The ID of the post to retrieve the HTML contents for. Defaults to the current post if not provided.
 * @return string|null The HTML contents of the post, or null if not found.
 */
function get_replace_links( $post_id = null ) {
	$post_id = $post_id ?? get_the_ID();
	return get_post_meta( $post_id, '_scht_replace_link', true );
}

/**
 * Checks if the path specified in the Static HTML Template options is valid.
 *
 * @param int|null $shc_id The ID of the post "static_html_setting". If not provided, the current post ID will be used.
 * @return bool|null True if the path is valid, false if not, or null if the path is not set.
 */
function is_path_valid( $shc_id = null ) {
	$shc_id  = $shc_id ?? get_the_ID();
	$options = get_sht_options( $shc_id );
	if ( ! isset( $options['path'] ) ) {
		return null;
	}
	return (bool) $options['path_status'];
}



add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'wp/v2',
			'/sht_rest_api_meta/(?P<id>\d+)',
			array(
				'methods'             => 'GET',
				'callback'            => __NAMESPACE__ . '\fetch_custom_post_meta',
				'permission_callback' => function () {
						return current_user_can( 'edit_posts' );
				},
			)
		);
	}
);

function fetch_custom_post_meta( $data ) {
	$shc_id    = $data['id'];
	$meta_data = get_dynamic_links( $shc_id );
	if ( $meta_data ) {
		$formatted_meta_data = array();
		foreach ( $meta_data as $value ) {
			$formatted_meta_data[] = maybe_unserialize( $value );
		}
		return new \WP_REST_Response( $formatted_meta_data, 200 );
	} else {
		return null;
	}
}
