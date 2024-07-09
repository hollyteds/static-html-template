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
function is_static_page() {

	// フロントページは除く
	if ( is_front_page() ) {
		return false;
	}
	// 投稿一覧ページは除く
	if ( is_home() ) {
		return false;
	}
	return is_page_template( 'static-html-template.php' );
}

/**s
 * Checks if the path specified in the settings is valid and if the HTML file exists.
 *
 * @param int $post_id The ID of the post. Defaults to the current post ID.
 * @return bool Returns true if the path is valid and the HTML file does not exist, false otherwise.
 */
function get_path_status( $post_id = null ) {
	$post_id  = $post_id ?? get_the_ID();
	$settings = get_post_meta( $post_id, '_scht_settings', true );
	if ( ! isset( $settings['path'] ) ) {
		return false;
	}
	return is_html_file_exists( $settings['path'] ) ? 'valid' : 'invalid';
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
