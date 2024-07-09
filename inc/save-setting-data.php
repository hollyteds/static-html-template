<?php
namespace Shct;
/**
 * Saves the data for a custom meta box.
 *
 * @param int $post_id The ID of the post being saved.
 * @return void
 */
function custom_save_meta_box_data( $post_id ) {

	// nonceが設定されていない場合、もしくはチェックに失敗した場合は保存しない
	if ( ! isset( $_POST['custom_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['custom_meta_box_nonce'], 'custom_save_meta_box_data' ) ) {
		return;
	}

	// 自動保存の時は何もしない
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// ユーザーが必要な権限を持っていない場合は保存しない
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// データが送信されているかどうかチェックし、クリーンアップして保存
	if ( isset( $_POST['scht_settings'] ) ) {

		$data = SanitizeUploadData::sanitize( $_POST['scht_settings'] );

		// 設定項目を保存
		update_post_meta( $post_id, '_scht_settings', $data );

		// 保存されたpathを元に、最適化したhtmlデータも保存しておく
		update_post_meta( $post_id, '_scht_html', generate_html_data( $data['path'] ) );
	}
}
add_action( 'save_post', 'Shct\custom_save_meta_box_data' );
