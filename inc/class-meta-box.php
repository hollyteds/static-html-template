<?php
namespace Shct;

class MetaBox {

	public function __construct() {

		add_action( 'add_meta_boxes', array( $this, 'init' ) );
	}

	public function init() {

		// Remove the default save meta box to replace it with a custom save meta box.
		if ( is_edit( 'static_html_setting' ) ) {
			remove_meta_box( 'submitdiv', 'static_html_setting', 'side' );
		}

		add_meta_box(
			'shct-select-template',
			__( 'Static HTML Template', 'static-html-template' ),
			array( $this, 'add_meta_box_to_select_template_on_page' ),
			'page',
			'side',
			'high'
		);

		add_meta_box(
			'submitdiv',
			__( 'Status' ),
			array( $this, 'add_meta_box_to_save' ),
			'static_html_setting',
			'side',
			'high'
		);
	}

	public function add_meta_box_to_save( $post ) {
		$post_id = (int) $post->ID;
		$value   = $post->post_status !== 'publish' ? esc_attr__( 'Publish' ) : esc_attr__( 'Save' );

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
	}

	public function add_meta_box_to_select_template_on_page( $post ) {
		$shct_templates = get_posts(
			array(
				'posts_per_page' => -1,
				'post_type'      => 'static_html_setting',
				'post_status'    => 'publish',
			)
		);

		if ( empty( $shct_templates ) ) {
			return; // Don't display if there is no template.
		}

		// nonce
		wp_nonce_field( 'shct_select_template_nonce_action', 'shct_select_template_nonce' );

		// get shc id.
		$shc_id = get_shc_id( $post->ID );
		?>
	<div class="shct-post-meta-box">
	<label for="shct-template-id" class="shct-template-title"><?php _e( 'Select the static HTML template.', 'static-html-template' ); ?></label>
	<select id="shct-template-id" name="shct_selected_template_id">
		<?php
		foreach ( $shct_templates as $template ) {
			$attr_selected = (int) $shc_id === $template->ID ? ' selected' : '';
			echo '<option' . $attr_selected . ' value="' . $template->ID . '">' . esc_html( $template->post_title ) . '</option>';
		}
		?>
	</select>
	<p class="description"><?php _e( 'Invalid on the front page and the blog page.', 'static-html-template' ); ?></p>
	<div id="shct-dynasmic-link">
		<?php
		$dynamic_links = get_dynamic_links( $shc_id );
		$values        = get_replace_links( $post->ID );
		if ( $dynamic_links ) {
			echo '<hr><h3>' . __( 'A list of shortcodes set in the template', 'static-html-template' ) . '</h3>';
			echo '<p class="description">' . __( 'Replace the shortcodes set in the template\'s link with the entered strings for each item.', 'static-html-template' ) . '</p>';
			foreach ( $dynamic_links as $link ) {
				?>
			<div>
				<label for="scht_replace_link[<?php echo esc_attr( $link ); ?>]">[<?php echo esc_attr( $link ); ?>]</label>
				<input type="text" name="scht_replace_link[<?php echo esc_attr( $link ); ?>]" size="100" value="<?php echo $values[ $link ] ?? ''; ?>">
			</div>
				<?php
			}
		}
		?>
		</div>
	</div>
		<?php
	}
}

