<?php
/**
 * FILEPATH: /Users/hollyteds/htdocs/client-works/aim-agency/wp/wp-content/plugins/add-static-lp-format/static-html-template.php
 *
 * This file is responsible for retrieving and displaying static HTML templates.
 *
 * @package static-html-template
 */
$post_id = get_queried_object_id();
$options     = get_option( 'scht_settings' );
$selected_id = get_post_meta( $post_id, '_shct_selected_template_id', true );

if ( ! $selected_id && ! isset( $options[ $selected_id ] ) ) {
	return '<!-- データが取得できません -->';
}

$template = $options[ $selected_id ];

if ( ! is_html_file_exists( $template['path'] ) ) {
	return '<!-- データが取得できません -->';
}

$html_contents = file_get_contents( $template['path'] );
if ( $html_contents ) {

	$contents_path = dirname( $template['path'] );
	$contents_url  = site_url( '/' ) . str_replace( $_SERVER['DOCUMENT_ROOT'], '', $contents_path );
	$html_contents  = convert_relative_paths_to_absolute( $html_contents, $contents_url );

	$split_html_contents = split_html( $html_contents );

	if ( isset( $split_html_contents['html'] ) ) {
		echo $split_html_contents['html'];
	} else {
		echo $split_html_contents['before_head_close'];
		// do_action( 'shct_head' );
		wp_head();
		echo $split_html_contents['head_close_to_body_open'];
		// do_action( 'shct_body_open' );
		wp_body_open();
		echo $split_html_contents['body_open_to_body_close_start'];
		// do_action( 'shct_footer' );
		wp_footer();
		echo $split_html_contents['body_close_to_end'];
	}
}
