<?php
namespace Shct;

$post_id             = get_queried_object_id();
$shc_id              = get_shc_id( $post_id );
$split_html_contents = get_html_contents( $shc_id );
$options             = get_sht_options( $shc_id );
$is_valid_hooks      = (bool) ! $options['disable_action_hooks'] && ! (bool) $options['disable_action_hooks'];
$replace_links       = get_replace_links( $post_id ); // 置換対象のリンクの連想配列

if ( ! is_array( $split_html_contents ) ) {
	return;
}
echo $split_html_contents['before_head_close'] ?? '<html><head>';
if ( ! $is_valid_hooks ) {
	do_action( 'sht_head' );
} else {
	wp_head();
}
echo $split_html_contents['head_close_to_body_open'] ?? '</head><body>';
if ( ! $is_valid_hooks ) {
	do_action( 'sht_body_open' );
} else {
	wp_body_open();
}
if ( $replace_links ) {
	$body_open_to_body_close_start = $split_html_contents['body_open_to_body_close_start'];
	foreach ( $replace_links as $key => $value ) {
		$body_open_to_body_close_start = str_replace( '[' . $key . ']', esc_attr( $value ), $body_open_to_body_close_start );
	}
	echo $body_open_to_body_close_start;
} else {
	echo $split_html_contents['body_open_to_body_close_start'] ?? '';
}
if ( ! $is_valid_hooks ) {
	do_action( 'sht_footer' );
} else {
	wp_footer();
}
echo $split_html_contents['body_close_to_end'] ?? '</body></html>';
