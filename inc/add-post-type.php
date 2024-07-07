<?php

/**
 * 投稿タイプの追加
 *
 * オプションについて
 * https://wpdocs.osdn.jp/関数リファレンス/register_post_type
 *
 */

add_action(
	'init',
	function () {

		$post_types = array(
			array(
				'slug'    => 'static_html_setting', //投稿タイプスラッグ

				'setting' => array(
					'label'                 => __( 'Static Page Setting', 'static-code-template' ),
					'labels'                => array(
						'name'          => __( 'Static Page', 'static-code-template' ),
						'singular_name' => __( 'Static Page', 'static-code-template' ),
						'menu_name'     => __( 'Static Page', 'static-code-template' ),
						'all_items'     => __( 'Static Page', 'static-code-template' ),

					),
					'description'           => '',
					'menu_icon'             => 'dashicons-media-document', //https://developer.wordpress.org/resource/dashicons/
					'menu_position'         => 50,
					'publicly_queryable'    => false,
					'show_ui'               => true,
					'show_in_rest'          => false,
					'rest_base'             => '',
					'rest_controller_class' => 'WP_REST_Posts_Controller',
					'rest_namespace'        => 'wp/v2',
					'has_archive'           => false,
					'show_in_menu'          => true,
					'show_in_nav_menus'     => false,
					'delete_with_user'      => false,
					'exclude_from_search'   => true,
					'capability_type'       => 'post',
					'map_meta_cap'          => true,
					'hierarchical'          => false,
					'can_export'            => false,
					'rewrite'               => false,
					'query_var'             => false,
					'supports'              => array( 'title' ),
					'show_in_graphql'       => false,
				),
			),
		);

		foreach ( $post_types as $post_type ) {
			register_post_type( $post_type['slug'], $post_type['setting'] );
		}
	},
	20
);

add_action(
	'admin_menu',
	function () {
		remove_meta_box( 'slugdiv', 'static_page_setting', 'normal' );
	}
);
