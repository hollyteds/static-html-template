<?php

/**
 * Adds a custom admin menu page for the Static HTML Template plugin.
 *
 * This function is hooked to the 'admin_menu' action and adds a new menu page
 * with the title 'Static HTML Template' to the WordPress admin menu. The menu
 * page is accessible to users with the 'manage_options' capability.
 *
 * @since 1.0.0
 */
add_action(
	'admin_menu',
	function () {
		add_options_page(
			'Static HTML Template',
			'Static HTML Template',
			'manage_options',
			'static-html-template',
			'scht_settings_page',
			100
		);

		add_meta_box(
			'shct-select-template',
			__( 'Select HTML file ID.', 'static-html-template' ),
			function ( $post ) {
				$options = get_option( 'scht_settings' );
				if ( empty( $options ) ) {
					return;
				}
				wp_nonce_field( 'shct_select_template_nonce_action', 'shct_select_template_nonce' );
				$selected_id = get_post_meta( $post->ID, '_shct_selected_template_id', true );
				?>
		<div>
		<select id="shct-template-id" name="shct_selected_template_id">
				<?php
				foreach ( $options as $index => $value ) {
					$attr_selected = $selected_id === strval( $index ) ? ' selected' : '';
					echo '<option' . $attr_selected . ' value="' . strval( $index ) . '">' . $value['id'] . '</option>';
				}
				?>
		</select>
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
		if ( is_singular( ! 'static_html_setting' ) ) {
			return;
		}
		scht_settings_page();
	}
);

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
 * Adds an action to register the settings for the admin menu.
 *
 * This function is hooked to the 'admin_init' action, which is fired when the admin area is initialized.
 * It registers the settings for the 'scht_settings_group' and uses the 'SCHT_Sanitize_Upload_Data' class
 * to sanitize the uploaded data.
 *
 * @since 1.0.0
 */
add_action(
	'admin_init',
	function () {
		register_setting( 'scht_settings_group', 'scht_settings', array( 'SCHT_Sanitize_Upload_Data', 'sanitize' ) );
	}
);

/**
 * Renders the settings page for the Static HTML Template plugin.
 */
function scht_settings_page() {
	$options = get_option( 'scht_settings' );
	?>
	<div class="wrap">
		<h1>Static HTML Template Settings</h1>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'scht_settings_group' );
			do_settings_sections( 'custom-settings' );
			?>
			<table id="dynamic-form">
			<thead>
				<tr>
					<th>Name</th>
					<th>HTML Path</th>
					<th>Styles to Retain in the Queue.</th>
					<th>Scripts to Retain in the Queue.</th>
					<th>Status</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php
				if ( ! empty( $options ) ) {
					foreach ( $options as $key => $value ) {
						if ( ! $value['id'] && ! $value['path'] ) {
							continue;
						}
						?>
						<tr>
							<?php
							shct_set_text_form(
								array(
									'key'  => $key,
									'name' => 'id',
									'size' => 15,
								),
								$value
							);
							shct_set_text_form(
								array(
									'key'  => $key,
									'name' => 'path',
									'size' => 50,
								),
								$value
							);
							shct_set_textarea(
								array(
									'key'  => $key,
									'name' => 'enqueue_styles',
								),
								$value
							);
							shct_set_textarea(
								array(
									'key'  => $key,
									'name' => 'enqueue_scripts',
								),
								$value
							);
							?>
							<td>
							<?php
							if ( $value['path'] ) {
								echo is_html_file_exists( $value['path'] ) ? 'パスは有効です' : '無効なパスです';
							}
							?>
							</td>
							<td>
							<?php
							if ( $key !== 0 ) {
								?>
							<button type="button" class="button remove-row">Remove</button><?php } ?>
							<td>
							</td>
						</tr>
						<?php
					}
				} else {
					?>
				<tr>
					<?php
					shct_set_text_form(
						array(
							'key'  => 0,
							'name' => 'id',
							'size' => 15,
						)
					);
					shct_set_text_form(
						array(
							'key'  => 0,
							'name' => 'path',
							'size' => 50,
						)
					);
					shct_set_textarea(
						array(
							'key'  => $key,
							'name' => 'enqueue_styles',
						),
						$value
					);
					shct_set_textarea(
						array(
							'key'  => 0,
							'name' => 'enqueue_scripts',
						)
					);
					?>
					<td></td>
					<td></td>
				</tr>
					<?php
				}
				?>
			</tbody>
			</table>
			<button type="button" class="button add-row">Add Row</button>
			<?php submit_button(); ?>
		</form>
	</div>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			document.querySelector('.add-row').addEventListener('click', function() {
				const table = document.getElementById('dynamic-form');
				const rowCount = table.rows.length - 1;
				const newRow = table.insertRow(-1);
				newRow.innerHTML = `
				<?php
				shct_set_text_form(
					array(
						'key'  => '${rowCount}',
						'name' => 'id',
						'size' => 15,
					)
				);
				shct_set_text_form(
					array(
						'key'  => '${rowCount}',
						'name' => 'path',
						'size' => 50,
					)
				);
				shct_set_textarea(
					array(
						'key'  => '${rowCount}',
						'name' => 'enqueue_styles',
					),
					$value
				);
				shct_set_textarea(
					array(
						'key'  => '${rowCount}',
						'name' => 'enqueue_scripts',
					)
				);
				?>
				<td></td>
				<td><button type="button" class="button remove-row">Remove</button></td>
				`;
			i++;
			});
			document.getElementById('dynamic-form').addEventListener('click', function(event) {
				if (event.target && event.target.matches('button.remove-row')) {
					event.target.closest('tr').remove();
				}
			});
		});
	</script>
	<?php
}

/**
 * Generates a text form input field.
 *
 * @param array $option The option settings.
 * @param array $value The current option values.
 * @return void
 */
function shct_set_text_form( $option, $value = '' ) {
	$set_value   = isset( $value[ $option['name'] ] ) ? $value[ $option['name'] ] : '';
	$size        = isset( $option['size'] ) ? $option['size'] : '';
	$placeholder = isset( $option['placeholder'] ) ? $option['placeholder'] : '';
	echo '<td><input type="text" name="scht_settings[' . $option['key'] . '][' . $option['name'] . ']" size="' . $size . '" placeholder="' . $placeholder . '" value="' . $set_value . '"></td>';
}

/**
 * Generates a textarea input field.
 *
 * @param array $option The option settings.
 * @param array $value The current option values.
 * @return void
 */
function shct_set_textarea( $option, $value = '' ) {
	$set_value   = isset( $value[ $option['name'] ] ) ? $value[ $option['name'] ] : '';
	$size        = isset( $option['size'] ) ? $option['size'] : '25';
	$placeholder = isset( $option['placeholder'] ) ? $option['placeholder'] : __( 'If there are multiple entries, input them with line breaks.', 'static-html-template' );
	echo '<td><textarea name="scht_settings[' . $option['key'] . '][' . $option['name'] . ']" cols="' . $size . '" rows="6" placeholder="' . $placeholder . '">' . $set_value . '</textarea></td>';
}

