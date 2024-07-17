<?php
namespace Shct;

/**
 * Class AdminSettings
 *
 * This class handles the admin settings for the Static HTML Template plugin.
 */
class AdminSettings {

	public function __construct() {

		require_once SHT_DIR_PATH . 'inc/class-meta-box.php';  // meta box
		require_once SHT_DIR_PATH . 'inc/class-save-meta-data.php'; // Save
		require_once SHT_DIR_PATH . 'inc/class-set-form-parts.php'; // Form Parts

		new MetaBox();
		new SaveMetaData();

		// Add filters
		add_filter( 'manage_static_html_setting_posts_columns', array( $this, 'set_sht_post_list' ) );
		add_filter( 'display_post_states', array( $this, 'add_template_name_to_page_title' ), 10, 2 );
		add_filter( 'post_row_actions', array( $this, 'remove_sht_quick_editor' ), 10, 2 );

		// Add actions
		add_action( 'manage_static_html_setting_posts_custom_column', array( $this, 'set_sht_post_list_columns' ), 10, 2 );
		add_action( 'transition_post_status', array( $this, 'restrict_sht_post_status' ), 10, 3 );
		add_action( 'edit_form_after_title', array( $this, 'set_forms' ) );
		add_action( 'edit_form_top', array( $this, 'show_warning_message' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'sht_enqueue_style' ), 20 );
	}


	/**
	 * Shows the warning message if the file path is invalid.
	 *
	 * @return void
	 */
	public function show_warning_message() {
		if ( is_edit( 'static_html_setting' ) && ! is_path_valid() && is_path_valid() !== null ) {
			?>
		<div class="notice notice-warning notice-alt is-dismissible">
			<p><?php _e( 'The file path is invalid.', 'static-html-template' ); // 無効なパスです。有効なパスを入力してください。 ?></p>
		</div>
			<?php
		}
	}

	/**
	 * Sets the columns for the post list in 'static_html_setting' post type.
	 *
	 * @param array $columns The array of columns for the post list.
	 * @return array The modified array of columns.
	 */
	public function set_sht_post_list( $columns ) {
		return array(
			'cb'     => '<input type="checkbox" />',
			'title'  => __( 'Title' ),
			'path'   => __( 'File Path', 'static-html-template' ),
			'status' => __( 'Status' ),
			'author' => __( 'Author', 'static-html-template' ),
			'date'   => __( 'Date' ),
		);
	}

	/**
	 * Sets the custom columns for the post list in the 'static_html_setting' post type.
	 *
	 * @param string $column_name The name of the column.
	 * @param int    $post_id     The ID of the post.
	 * @return void
	 */
	public function set_sht_post_list_columns( $column_name, $post_id ) {
		$options = get_sht_options( $post_id );

		if ( 'path' === $column_name ) {
			echo isset( $options['path'] ) ? $options['path'] : __( 'undefined', 'static-html-template' );
		}

		if ( 'status' === $column_name && ! empty( is_path_valid( $post_id ) ) ) { // pathの値がなければ表示しない
			echo is_path_valid( $post_id ) ? __( 'valid', 'static-html-template' ) : __( 'invalid', 'static-html-template' );
		}
	}

	/**
	 * Adds the template name to the page title for static template pages.
	 *
	 * @param array $states The array of states for the page.
	 * @param object $post The post object.
	 * @return array The modified array of states.
	 */
	public function add_template_name_to_page_title( $states, $post ) {

		if ( 'page' === get_post_type( $post->ID ) && is_static_page( $post->ID ) ) {

			$shc_id        = get_shc_id( $post->ID );
			$template_name = get_html_contents( $shc_id ) ? get_current_sht_template( $post->ID ) : __( 'Invalid template', 'static-html-template' );
			$states[]      = __( 'Static', 'static-html-template' ) . '-' . $template_name;
		}
		return $states;
	}

	/**
	 * Removes the quick editor action for static HTML settings.
	 *
	 * @param array $actions The array of actions for the post.
	 * @param object $post The post object.
	 * @return array The modified array of actions.
	 */
	public function remove_sht_quick_editor( $actions, $post ) {

		if ( $post->post_type === 'static_html_setting' ) {
			unset( $actions['inline hide-if-no-js'] );
		}
		return $actions;
	}

	/**
	 * Restricts the post status for the 'static_html_setting' post type.
	 * If the new post status is not 'publish', 'trash', or 'auto-draft', it updates the post status to 'publish'.
	 *
	 * @param string $new_status The new post status.
	 * @param string $old_status The old post status.
	 * @param WP_Post $post The post object.
	 * @return void
	 */
	public function restrict_sht_post_status( $new_status, $old_status, $post ) {

		// It checks if the current page is the 'static_html_setting' post type, and if not, it returns early.
		if ( $post->post_type !== 'static_html_setting' ) {
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
	}

	/**
	 * Registers and enqueues the admin stylesheet for the Static HTML Template plugin.
	 *
	 * @return void
	 */
	public function sht_enqueue_style() {
		wp_register_style( 'static-html-template-admin', SHT_DIR_URL . 'assets/css/admin.css', false, '1.0.0' );
		wp_register_script( 'static-html-template-admin', SHT_DIR_URL . 'assets/js/main.js', false, '1.0.0' );
		wp_enqueue_style( 'static-html-template-admin' );
		wp_enqueue_script( 'static-html-template-admin' );
		if ( is_edit( 'page' ) ) {
			wp_enqueue_script( 'sht-rest-api', SHT_DIR_URL . 'assets/js/shcReatApi.js', false, '1.0.0' );
			wp_localize_script(
				'sht-rest-api',
				'shtRestApi',
				array(
					'rest_url' => rest_url( 'wp/v2/sht_rest_api_meta' ),
					'nonce'    => wp_create_nonce( 'wp_rest' ),
				)
			);
		}
	}


	/**
	 * Sets the forms for the static HTML settings.
	 * This method is responsible for rendering the HTML forms for the static HTML settings.
	 * @return void
	 */
	public function set_forms() {

		// It checks if the current page is the edit page for the 'static_html_setting' post type, and if not, it returns early.
		if ( ! is_edit( 'static_html_setting' ) ) {
			return;
		}

		// It then creates an instance of the `SetFormParts` class and passes the options to it.
		$options = get_sht_options();
		$form    = new SetFormParts( $options );

		// It adds a security nonce field.
		wp_nonce_field( 'custom_save_meta_box_data', 'custom_meta_box_nonce' );

		?>
		<div id="custom-postbox-container" class="postbox-container">
		<div class="wrap">
			<div id="static-setting-forms">
			<?php
			$form->text_form(
				array(
					'title' => __( 'File path for Static site data', 'static-html-template' ),
					'name'  => 'path',
					'size'  => 100,
				),
			);
			$form->textarea(
				array(
					'title' => __( 'Styles to Retain in the Queue', 'static-html-template' ),
					'help'  => __( 'Enter with line breaks if there are multiple items.', 'static-html-template' ),
					'name'  => 'enqueue_styles',
				),
			);
			$form->textarea(
				array(
					'title' => __( 'Scripts to Retain in the Queue', 'static-html-template' ),
					'help'  => __( 'Enter with line breaks if there are multiple items.', 'static-html-template' ),
					'name'  => 'enqueue_scripts',
				),
			);
			$form->checkbox(
				array(
					'title' => __( 'Options', 'static-html-template' ),
					'label' => __( 'Replace with the title tag output by the page.', 'static-html-template' ),
					'name'  => 'replace_title_tag',
				),
			);
			$form->checkbox(
				array(
					'label' => __( 'Replace with the site icon output by the page.', 'static-html-template' ),
					'name'  => 'replace_site_icon',
				),
			);
			$form->checkbox(
				array(
					'title' => __( 'Replace the hooks', 'static-html-template' ),
					'help'  => __( 'Disable all hooked actions by replacing "wp_head", "wp_body_open", and "wp_footer" with "sht_head", "sht_body_open", and "sht_footer", respectively. This includes actions hooked by plugins . However, the retained enqueue styles and scripts will be output as they are, but scripts directly enqueued in the callback functions of the "wp_head" or "wp_footer" hooks will not be output.', 'static-html-template' ),
					'label' => __( 'Replace the hooks.', 'static-html-template' ),
					'name'  => 'disable_action_hooks',
				),
			);
			?>
			</div>
		</div>
		</div>
		<div class="clear"></div>
		<?php
	}
}