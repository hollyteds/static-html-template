<?php
namespace Shct;
/**
 * Adds a custom admin menu page for the Static HTML Template plugin.
 *
 * This function is hooked to the 'admin_menu' action and adds a new menu page
 * with the title 'Static HTML Template' to the WordPress admin menu. The menu
 * page is accessible to users with the 'manage_options' capability.
 */

add_action(
	'add_meta_boxes',
	function () {
		if ( is_edit( 'static_html_setting' ) ) {
			remove_meta_box( 'submitdiv', 'static_html_setting', 'side' );
		}
		add_meta_box(
			'shct-select-template',
			__( 'Static HTML Template', 'static-html-template' ),
			function ( $post ) {
				$shct_templates = get_posts(
					array(
						'posts_per_page' => -1,
						'post_type'      => 'static_html_setting',
						'post_status'    => 'publish',
					)
				);
				if ( empty( $shct_templates ) ) {
					return;
				}
				wp_nonce_field( 'shct_select_template_nonce_action', 'shct_select_template_nonce' );
				$selected_id = get_post_meta( $post->ID, '_shct_selected_template_id', true );
				?>
		<div class="shct-post-meta-box">
		<label for="shct-template-id" class="shct-template-title"><?php _e( 'Select the static HTML template.', 'static-html-template' ); ?></label>
		<select id="shct-template-id" name="shct_selected_template_id">
				<?php
				foreach ( $shct_templates as $template ) {
					$attr_selected = (int) $selected_id === $template->ID ? ' selected' : '';
					echo '<option' . $attr_selected . ' value="' . $template->ID . '">' . esc_html( $template->post_title ) . '</option>';
				}
				?>
		</select>
		<p class="description"><?php _e( 'This template is invalid on the front page and blog homepage.', 'static-html-template' ); ?></p>
		</div>
				<?php
			},
			'page',
			'side',
			'high'
		);
	}
);


/**
 * Adds a custom meta box to the edit form after the title for the 'static_html_setting' post type.
 *
 * This function is hooked to the 'edit_form_after_title' action.
 * It checks if the current post is of the 'static_html_setting' post type.
 * If it is, it adds a container div and calls the 'do_meta_boxes' function to display the meta boxes.
 *
 * @return void
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
 * This code is responsible for saving the selected template ID when a post is saved.
 *
 * @param int $post_id The ID of the post being saved.
 * @return void
 */
add_action(
	'save_post',
	function ( $post_id ) {

		// ノンスチェック（セキュリティ対策）
		if ( ! isset( $_POST['shct_select_template_nonce'] ) || ! wp_verify_nonce( $_POST['shct_select_template_nonce'], 'shct_select_template_nonce_action' ) ) {
			return;
		}

		// 自動保存の場合は何もしない
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// ユーザー権限のチェック
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

		// 入力フィールドの値を取得
		if ( isset( $_POST['shct_selected_template_id'] ) ) {
			$custom_field_value = sanitize_text_field( $_POST['shct_selected_template_id'] );
			update_post_meta( $post_id, '_shct_selected_template_id', $custom_field_value );
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
		if ( 'path' == $column_name ) {
			echo isset( $settings['path'] ) ? $settings['path'] : __( 'undefined', 'static-html-template' );
		}
		if ( 'status' == $column_name ) {
			_e( get_path_status( $post_id ), 'static-html-template' );
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

	$form = new FormParts( $settings );
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

		if ( $post->post_type == 'static_html_setting' ) {
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

		if ( $post->post_type != $post_type ) {
			return;
		}

		if ( ! in_array( $new_status, array( 'publish', 'trash', 'auto-draft' ) ) ) {
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
 * Adds a meta box for the post status in the WordPress admin panel.
 */
add_action(
	'add_meta_boxes',
	function () {
		add_meta_box(
			'submitdiv',
			__( 'Status' ),
			function ( $post ) {
						$post_id = (int) $post->ID;
						$value   = $post->post_status != 'publish' ? esc_attr__( 'Publish' ) : esc_attr__( 'Save' );

				?>
					<div class="submitbox" id="submitpost">
						<div id="major-publishing-actions">
							<div id="delete-action">
									<?php
									if ( current_user_can( 'delete_post', $post_id ) ) {
										if ( ! EMPTY_TRASH_DAYS ) {
											$delete_text = __( 'Delete permanently' );
										} else {
											$delete_text = __( 'Move to Trash' );
										}
										?>
										<a class="submitdelete deletion" href="<?php echo get_delete_post_link( $post_id ); ?>"><?php echo $delete_text; ?></a>
										<?php
									}
									?>
							</div>

							<div id="publishing-action">
								<span class="spinner"></span>
									<?php
									if ( ! in_array( $post->post_status, array( 'publish', 'future', 'private' ), true ) || 0 === $post_id ) {
										?>
										<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Publish' ); ?>" />
										<?php submit_button( __( 'Publish' ), 'primary large', 'publish', false ); ?>
										<?php
									} else {
										?>
										<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Update' ); ?>" />
										<?php submit_button( __( 'Update' ), 'primary large', 'save', false, array( 'id' => 'publish' ) ); ?>
										<?php
									}
									?>
							</div>

							<div class="clear"></div>
						</div>
					</div>
						<?php
			},
			'static_html_setting',
			'side',
			'high'
		);
	}
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