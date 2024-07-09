<?php
namespace Shct;

$post_id             = get_queried_object_id();
$selected_id         = get_post_meta( $post_id, '_shct_selected_template_id', true );
$split_html_contents = get_post_meta( $selected_id, '_scht_html', true );

echo $split_html_contents['before_head_close'] ?? '<html><head>';
// do_action( 'shct_head' );
wp_head();
echo $split_html_contents['head_close_to_body_open'] ?? '</head><body>';
// do_action( 'shct_body_open' );
wp_body_open();
echo $split_html_contents['body_open_to_body_close_start'] ?? '';
// do_action( 'shct_footer' );
wp_footer();
echo $split_html_contents['body_close_to_end'] ?? '</body></html>';
