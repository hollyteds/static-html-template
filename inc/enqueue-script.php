<?php
namespace Shct;

add_action(
	'wp',
	function () {

		$post_id = get_queried_object_id();
		$shc_id  = get_shc_id( $post_id );

		if ( ! is_static_page() || ! get_html_contents( $shc_id ) ) {
			return;
		}

		$options        = get_sht_options( $shc_id );
		$is_valid_hooks = (bool) ! $options['disable_action_hooks'] && ! (bool) $options['disable_action_hooks'];

		if ( ! is_path_valid( $shc_id ) ) {
			return;
		}

		if ( $is_valid_hooks ) {

			remove_action( 'wp_head', '_wp_render_title_tag', 1 );
			// remove_action( 'wp_head', 'wp_enqueue_scripts', 1 );
			remove_action( 'wp_head', 'wp_resource_hints', 2 );
			remove_action( 'wp_head', 'wp_preload_resources', 1 );
			remove_action( 'wp_head', 'feed_links', 2 );
			remove_action( 'wp_head', 'feed_links_extra', 3 );
			remove_action( 'wp_head', 'rsd_link' );
			// remove_action( 'wp_head', 'locale_stylesheet' );
			// remove_action( 'wp_head', 'wp_robots', 1 );
			remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
			// remove_action( 'wp_head', 'wp_print_styles', 8 );
			// remove_action( 'wp_head', 'wp_print_head_scripts', 9 );
			remove_action( 'wp_head', 'wp_generator' );
			// remove_action( 'wp_head', 'rel_canonical' );
			remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
			remove_action( 'wp_head', 'wp_custom_css_cb', 101 );
			// remove_action( 'wp_head', 'wp_site_icon', 99 );
			remove_action( 'wp_head', 'rest_output_link_wp_head', 10, 0 );
			remove_action( 'wp_head', 'wp_post_preview_js', 1 );
			// remove_action( 'wp_head', 'wp_maybe_inline_styles', 1 ); // Run for styles enqueued in <head>.
			remove_action( 'wp_head', 'wp_print_font_faces', 50 );
			// remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
			// remove_action( 'wp_head', 'wp_oembed_add_host_js' ); // Back-compat for sites disabling oEmbed host JS by removing action.

			// remove_action( 'wp_body_open', 'wp_admin_bar_render', 0 );
			// remove_action( 'wp_footer', 'wp_admin_bar_render', 1000 ); // Back-compat for themes not using `wp_body_open`.
			remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );
			remove_action( 'wp_footer', 'wp_enqueue_stored_styles', 1 );

			remove_action( 'wp_enqueue_scripts', 'wp_enqueue_block_template_skip_link' );
			remove_action( 'wp_footer', 'the_block_template_skip_link' ); // Retained for backwards-compatibility. Unhooked by wp_enqueue_block_template_skip_link().
			remove_action( 'wp_footer', 'wp_maybe_inline_styles', 1 ); // Run for late-loaded styles in the footer.
			remove_action( 'wp_footer', 'print_embed_sharing_dialog' );
			remove_action( 'wp_footer', 'print_embed_scripts' );
			// remove_action( 'wp_footer', 'wp_print_footer_scripts', 20 );

		} else {

			// アクションフックを置換した場合の処理
			// スクリプトと管理バーの出力だけフックを有効化しておく
			add_action( 'sht_head', 'wp_enqueue_scripts', 1 );
			add_action( 'sht_head', 'wp_print_styles', 8 );
			add_action( 'sht_head', 'wp_print_head_scripts', 9 );
			add_action( 'sht_body_open', 'wp_admin_bar_render', 0 );
			add_action( 'sht_footer', 'wp_admin_bar_render', 1000 ); // Back-compat for themes not using `wp_body_open`.
			add_action( 'sht_footer', 'wp_print_footer_scripts', 20 );

		}

		add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_scripts', 999 );
		add_action( 'wp_print_footer_scripts', __NAMESPACE__ . '\enqueue_scripts', 1 );
	},
	1000
);

function enqueue_scripts() {

	if ( ! is_static_page() ) {
		return;
	}

	global $wp_scripts;
	global $wp_styles;

	$post_id = get_queried_object_id();
	$shc_id  = get_shc_id( $post_id );
	$options = get_sht_options( $shc_id );

	$permit_enqueue_styles = ! empty( $options['enqueue_styles'] )
		? preg_split( '/\r\n|\r|\n/', $options['enqueue_styles'] )
		: array( '' );

	$permit_enqueue_scripts = ! empty( $options['enqueue_scripts'] )
		? preg_split( '/\r\n|\r|\n/', $options['enqueue_scripts'] )
		: array( '' );

	$enqueue_style_list = array_merge(
		array(
			'admin-bar',
			'wp-block-library', // テーマでskip linkが出力された時のため読み込む
		),
		$permit_enqueue_styles,
	);

	$enqueue_script_list = array_merge(
		array(
			'admin-bar',
		),
		$permit_enqueue_scripts,
	);

	foreach ( $wp_styles->queue as $handle ) {
		if ( in_array( $handle, $enqueue_style_list, true ) ) {
			continue;
		}
		// wp_deregister_style( $handle );
		wp_dequeue_style( $handle );

	}
	foreach ( $wp_scripts->queue as $handle ) {

		if ( in_array( $handle, $enqueue_script_list, true ) ) {
			continue;
		}
		// wp_deregister_script( $handle );
		wp_dequeue_script( $handle );

	}
}
