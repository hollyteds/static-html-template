<?php
$post_data  = array();
$post_args  = array(
	'post_type'      => 'page',
	'posts_per_page' => 1,
	'post_status'    => 'publish',
);
$post_query = new WP_Query( $post_args );
if ( $post_query->have_posts() ) :
	while ( $post_query->have_posts() ) :
		$post_query->the_post();
		global $post;
		$googlemap    = get_field( 'poi_coordinate' );
		$thumbnail_id = get_post_thumbnail_id( $post->ID );
		switch ( get_field( 'icon_size' ) ) {
			case 'small':
				$image = wp_get_attachment_image_src( $thumbnail_id, 'map_icon_s' );
				break;
			case 'large':
				$image = wp_get_attachment_image_src( $thumbnail_id, 'map_icon_l' );
				break;
			default:
				$image = wp_get_attachment_image_src( $thumbnail_id, array( 160, 160 ) );
		}
		if ( $image ) { //サムネイルがある場合
			$thumbnail = $image[0];
			$width     = $image[1];
			$height    = $image[2];
		} else { //サムネイルがない場合
			$thumbnail = get_template_directory_uri() . '/img/ico_default.png';
			$width     = 40;
			$height    = 40;
		}
		$post_data[] = array(
			'title'     => get_the_title(), //名称
			'lat'       => $googlemap['lat'],
			'lng'       => $googlemap['lng'],
			'thumbnail' => $thumbnail,
			'width'     => $width,
			'height'    => $height,
		);
	endwhile;
	wp_reset_postdata();
endif;

return $post_data;
