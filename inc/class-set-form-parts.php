<?php

namespace Shct;

class SetFormParts {
	public array $data;

	public function __construct( $data ) {
		$this->data = is_array( $data ) ? $data : array();
	}
	/**
	 * Generates a text form input field.
	 *
	 * @param array $option The option settings.
	 * @param array $settings The current option values.
	 * @return void
	 */
	public function set_text_form( $option ) {
		$set_value   = isset( $this->data[ $option['name'] ] ) ? esc_attr( $this->data[ $option['name'] ] ) : '';
		$size        = isset( $option['size'] ) ? esc_attr( $option['size'] ) : '';
		$placeholder = isset( $option['placeholder'] ) ? esc_attr( $option['placeholder'] ) : '';

		echo isset( $option['title'] ) ? '<h2 class="title">' . $option['title'] . '</h2>' : '';
		echo isset( $option['help'] ) ? '<p class="description">' . $option['help'] . '</p>' : '';
		?>
	<div>
	<input type="text" name="scht_settings[<?php echo esc_attr( $option['name'] ); ?>]" size="<?php echo $size; ?>" placeholder="<?php echo $placeholder; ?>" value="<?php echo $set_value; ?>">
	</div>
		<?php
	}

	/**
	 * Generates a textarea input field.
	 *
	 * @param array $option The option settings.
	 * @param array $settings The current option values.
	 * @return void
	 */
	public function set_textarea( $option ) {
		$set_value   = isset( $this->data[ $option['name'] ] ) ? esc_attr( $this->data[ $option['name'] ] ) : '';
		$size        = isset( $option['size'] ) ? esc_attr( $option['size'] ) : '50';
		$placeholder = isset( $option['placeholder'] ) ? $option['placeholder'] : '';

		echo isset( $option['title'] ) ? '<h2 class="title">' . $option['title'] . '</h2>' : '';
		echo isset( $option['help'] ) ? '<p class="description">' . $option['help'] . '</p>' : '';
		?>
	<div>
	<textarea name="scht_settings[<?php echo esc_attr( $option['name'] ); ?>]" cols="<?php echo $size; ?>" rows="5" placeholder="<?php echo $placeholder; ?>"><?php echo $set_value; ?></textarea>
	</div>
		<?php
	}

	/**
	* Generates a textarea input field.
	*
	* @param array $option The option settings.
	* @param array $settings The current option values.
	* @return void
	*/
	public function set_checkbox( $option ) {
		$set_value   = isset( $this->data[ $option['name'] ] ) ? esc_attr( $this->data[ $option['name'] ] ) : '';
		$size        = isset( $option['size'] ) ? esc_attr( $option['size'] ) : '50';
		$placeholder = isset( $option['placeholder'] ) ? $option['placeholder'] : __( 'If there are multiple entries, input them with line breaks.', 'static-html-template' );

		echo isset( $option['title'] ) ? '<h2 class="title">' . $option['title'] . '</h2>' : '';
		echo isset( $option['help'] ) ? '<p class="description">' . $option['help'] . '</p>' : '';
		?>
	<div>
	<input type="checkbox" name="scht_settings[<?php echo esc_attr( $option['name'] ); ?>]" size="<?php echo $size; ?>" placeholder="<?php echo $placeholder; ?>" value="<?php echo $set_value; ?>">
	</div>
		<?php
	}
}
