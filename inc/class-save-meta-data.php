<?php
namespace Shct;


require_once SHT_DIR_PATH . 'inc/class-sanitize-upload-data.php'; // Sanitize
require_once SHT_DIR_PATH . 'inc/class-get-html-contents.php'; // Generate html contents

/**
 * Class SaveMetaData
 *
 * This class is responsible for saving custom meta data when a post is saved.
 * It utilizes the 'save_post' action hook.
 */
class SaveMetaData {

	// Constructor
	public function __construct() {

		add_action( 'save_post', array( $this, 'save_settings_data' ) );
		add_action( 'save_post', array( $this, 'save_template_id' ) );
	}

	// Save the settings data.
	public function save_settings_data( $post_id ) {

		if ( ! $this->is_valid_save( $post_id, 'custom_meta_box_nonce', 'custom_save_meta_box_data' ) ) {
			return;
		}

		// データが送信されているかどうかチェックし、クリーンアップして保存
		if ( isset( $_POST['scht_settings'] ) ) {

			$data = SanitizeUploadData::sanitize( $_POST['scht_settings'] );

			// 設定項目を保存
			update_post_meta( $post_id, '_scht_settings', $data );

			// 保存されたpathを元に、最適化したhtmlデータも保存しておく
			$html = new GetHTMLContents( $data['path'] );
			update_post_meta( $post_id, '_scht_html', $html->contents );
		}
	}

	/**
		* Save the selected template ID
		*
		* @param int $post_id The ID of the post being saved.
		*/
	public function save_template_id( $post_id ) {

		if ( ! $this->is_valid_save( $post_id, 'shct_select_template_nonce', 'shct_select_template_nonce_action' ) ) {
			return;
		}

		// 入力フィールドの値を取得
		if ( isset( $_POST['shct_selected_template_id'] ) ) {
			$data = sanitize_text_field( $_POST['shct_selected_template_id'] );
			update_post_meta( $post_id, '_shct_selected_template_id', $data );
		}
	}

	/**
	 * Determine whether to allow the save.
	 *
	 * @param int $post_id The ID of the post being saved.
	 * @param string $post_nonce_name The name of the nonce in the POST request.
	 * @param string $check_nonce_name The action name to check the nonce against.
	 * @return bool True if the save is allowed, false otherwise.
	 */
	public function is_valid_save( $post_id, $post_nonce_name, $check_nonce_name ) {

		// Nonce check
		if ( ! isset( $_POST[ $post_nonce_name ] ) || ! wp_verify_nonce( $_POST[ $post_nonce_name ], $check_nonce_name ) ) {
			return;
		}

		// Do nothing during autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		// Do not save if the user does not have the necessary permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		return true;
	}
}
