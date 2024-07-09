<?php
namespace Shct;
/**
 * Adds a custom admin menu page for the Static HTML Template plugin.
 *
 * This function is hooked to the 'admin_menu' action and adds a new menu page
 * with the title 'Static HTML Template' to the WordPress admin menu. The menu
 * page is accessible to users with the 'manage_options' capability.
 */


require_once SHT_DIR_PATH . 'inc/class-meta-box.php';  // meta box
require_once SHT_DIR_PATH . 'inc/class-save-meta-data.php'; // Save
require_once SHT_DIR_PATH . 'inc/class-set-form-parts.php'; // Form Parts


new MetaBox();
new SaveMetaData();

/**
 * Adds a custom meta box to the edit form after the title for the 'static_html_setting' post type.
 */
add_action(
	'edit_form_after_title',
	function () {
		if ( ! is_edit( 'static_html_setting' ) ) {
			return;
		}
		set_forms();
	}
);

/**
 * Displays a warning notice at the top of the edit form if the path is invalid within the edit screen of the current post type "static_html_setting".
 * This action hook is triggered at the top of the edit form in the WordPress admin area.
 */
add_action(
	'edit_form_top',
	function () {
		if ( is_edit( 'static_html_setting' ) && get_path_status() !== 'valid' ) {
			?>
			<div class="notice notice-warning notice-alt is-dismissible">
				<p><?php _e( 'It is an invalid path. Please provide a valid path.', 'static-html-template' ); // 無効なパスです。有効なパスを入力してください。 ?></p>
			</div>
			<?php
		}
	}
);

/**
 * Adds custom columns to the posts list table in the admin settings page.
 *
 * @param array $columns The existing columns in the posts list table.
 * @return array The modified columns array with custom columns added.
 */

add_filter(
	'manage_static_html_setting_posts_columns',
	function ( $columns ) {
		return array(
			'cb'     => '<input type="checkbox" />',
			'title'  => __( 'Title' ),
			'path'   => __( 'HTML Path', 'static-html-template' ),
			'status' => __( 'Status' ),
			'author' => __( 'Author', 'static-html-template' ),
			'date'   => __( 'Date' ),
		);
	}
);


/**
 * Adds a custom column to the "static_html_setting" post type in the admin panel.
 *
 * This function is hooked to the "manage_static_html_setting_posts_custom_column" action,
 * and it displays the value of the "path" and "status" columns for each post.
 *
 * @param string $column_name The name of the column being displayed.
 * @param int    $post_id     The ID of the current post.
 */
add_action(
	'manage_static_html_setting_posts_custom_column',
	function ( $column_name, $post_id ) {
		$settings = get_post_meta( $post_id, '_scht_settings', true );
		if ( 'path' === $column_name ) {
			echo isset( $settings['path'] ) ? $settings['path'] : __( 'undefined', 'static-html-template' );
		}
		if ( 'status' === $column_name ) {
			if ( get_path_status( $post_id ) === 'valid' ) {
				_e( 'valid', 'static-html-template' );
			} else {
				_e( 'invalid', 'static-html-template' );
			}
		}
	},
	10,
	2
);

/**
 * Renders the settings page.
 */
function set_forms() {
	$settings = get_post_meta( get_the_ID(), '_scht_settings', true );

	$form = new SetFormParts( $settings );
	// セキュリティのためのnonceフィールドを追加
	wp_nonce_field( 'custom_save_meta_box_data', 'custom_meta_box_nonce' );

	?>
	<div id="custom-postbox-container" class="postbox-container">
	<div class="wrap">
		<div id="static-setting-forms">
		<?php
		$form->set_text_form(
			array(
				'title' => __( 'HTML Path', 'static-html-template' ),
				'name'  => 'path',
				'size'  => 100,
			),
		);
		$form->set_textarea(
			array(
				'title' => __( 'Styles to Retain in the Queue.', 'static-html-template' ),
				'help'  => __( 'Enter with line breaks if there are multiple items.', 'static-html-template' ),
				'name'  => 'enqueue_styles',
			),
		);
		$form->set_textarea(
			array(
				'title' => __( 'Scripts to Retain in the Queue.', 'static-html-template' ),
				'help'  => __( 'Enter with line breaks if there are multiple items.', 'static-html-template' ),
				'name'  => 'enqueue_scripts',
			),
		);
		?>
		</div>
	</div>
	</div>
	<?php
}

/**
 * Adds a filter to modify the row actions for a post.
 */
add_filter(
	'post_row_actions',
	function ( $actions, $post ) {

		if ( $post->post_type === 'static_html_setting' ) {
			unset( $actions['inline hide-if-no-js'] );
		}
		return $actions;
	},
	10,
	2
);


/**
 * This function is triggered when a post's status is transitioned from one state to another.
 * It checks if the post type is 'static_html_setting' and if the new status is 'publish', 'trash', or 'auto-draft'.
 * If the new status is not one of these, it updates the post's status to 'publish'.
 */
add_action(
	'transition_post_status',
	function ( $new_status, $old_status, $post ) {
		$post_type = 'static_html_setting';

		if ( $post->post_type !== $post_type ) {
			return;
		}

		if ( ! in_array( $new_status, array( 'publish', 'trash', 'auto-draft' ), true ) ) {
			wp_update_post(
				array(
					'ID'          => $post->ID,
					'post_status' => 'publish',
				)
			);
		}
	},
	10,
	3
);

/**
 * Registers and enqueues the admin stylesheet for the Static HTML Template plugin.
 */
add_action(
	'admin_enqueue_scripts',
	function () {
		wp_register_style( 'static-html-template-admin', SHT_DIR_URL . 'assets/css/admin.css', false, '1.0.0' );
		wp_enqueue_style( 'static-html-template-admin' );
	},
	20
);