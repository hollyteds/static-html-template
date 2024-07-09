<?php
namespace Shct;
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
				'slug'    => 'static_html_setting',

				'setting' => array(
					'label'                 => __( 'Static Page Setting', 'static-html-template' ),
					'labels'                => array(
						'name'                  => __( 'Static Web Sites', 'static-html-template' ),
						'singular_name'         => __( 'Static Web Site', 'static-html-template' ),
						'menu_name'             => __( 'Static Web Site', 'static-html-template' ),
						'add_new'               => __( 'Add New Static Web Site', 'static-html-template' ),
						'add_new_item'          => __( 'Add New Static Web Site', 'static-html-template' ),
						'edit_item'             => __( 'Edit Setting', 'static-html-template' ),
						'new_item'              => __( 'New Setting', 'static-html-template' ),
						'view_item'             => __( 'View Setting', 'static-html-template' ),
						'view_items'            => __( 'View Settings', 'static-html-template' ),
						'search_items'          => __( 'Search Settings', 'static-html-template' ),
						'not_found'             => __( 'No Settings found.', 'static-html-template' ),
						'not_found_in_trash'    => __( 'No Settings found in Trash.', 'static-html-template' ),
						'parent_item_colon'     => __( 'Parent Setting:', 'static-html-template' ),
						'all_items'             => __( 'All Items', 'static-html-template' ),
						'archives'              => __( 'Setting Archives', 'static-html-template' ),
						'attributes'            => __( 'Setting Attributes', 'static-html-template' ),
						'insert_into_item'      => __( 'Insert into Setting', 'static-html-template' ),
						'uploaded_to_this_item' => __( 'Uploaded to this Setting', 'static-html-template' ),
						'featured_image'        => __( 'Featured Image', 'static-html-template' ),
						'set_featured_image'    => __( 'Set featured image', 'static-html-template' ),
						'remove_featured_image' => __( 'Remove featured image', 'static-html-template' ),
						'use_featured_image'    => __( 'Use as featured image', 'static-html-template' ),
						'menu_name'             => __( 'Static Web Site', 'static-html-template' ),
						'filter_items_list'     => __( 'Filter Settings list', 'static-html-template' ),
						'items_list_navigation' => __( 'Settings list navigation', 'static-html-template' ),
						'items_list'            => __( 'Settings list', 'static-html-template' ),

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
