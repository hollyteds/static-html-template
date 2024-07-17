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
	 * @param array $options The option settings.
	 * @param array $settings The current option values.
	 * @return void
	 */
	public function text_form( $options ) {
		$set_value   = isset( $this->data[ $options['name'] ] ) ? esc_attr( $this->data[ $options['name'] ] ) : '';
		$size        = isset( $options['size'] ) ? esc_attr( $options['size'] ) : '';
		$placeholder = isset( $options['placeholder'] ) ? esc_attr( $options['placeholder'] ) : '';

		echo isset( $options['title'] ) ? '<h2 class="title">' . $options['title'] . '</h2>' : '';
		echo isset( $options['help'] ) ? '<p class="description">' . $options['help'] . '</p>' : '';
		?>
	<div>
	<input type="text" name="scht_settings[<?php echo esc_attr( $options['name'] ); ?>]" size="<?php echo $size; ?>" placeholder="<?php echo $placeholder; ?>" value="<?php echo $set_value; ?>">
	</div>
		<?php
	}

	/**
	 * Generates a textarea input field.
	 *
	 * @param array $options The option settings.
	 * @param array $settings The current option values.
	 * @return void
	 */
	public function textarea( $options ) {
		$set_value   = isset( $this->data[ $options['name'] ] ) ? esc_attr( $this->data[ $options['name'] ] ) : '';
		$size        = isset( $options['size'] ) ? esc_attr( $options['size'] ) : '50';
		$placeholder = isset( $options['placeholder'] ) ? $options['placeholder'] : '';

		echo isset( $options['title'] ) ? '<h2 class="title">' . $options['title'] . '</h2>' : '';
		echo isset( $options['help'] ) ? '<p class="description">' . $options['help'] . '</p>' : '';
		?>
	<div>
	<textarea name="scht_settings[<?php echo esc_attr( $options['name'] ); ?>]" cols="<?php echo $size; ?>" rows="5" placeholder="<?php echo $placeholder; ?>"><?php echo $set_value; ?></textarea>
	</div>
		<?php
	}

	/**
	* Generates a checkbox input field.
	*
	* @param array $options The option settings.
	* @param array $settings The current option values.
	* @return void
	*/
	public function checkbox( $options ) {
		$checked = ( isset( $this->data[ $options['name'] ] ) && (bool) $this->data[ $options['name'] ] ) ? ' checked' : '';
		$label   = isset( $options['label'] ) ? esc_html( $options['label'] ) : '';

		echo isset( $options['title'] ) ? '<h2 class="title">' . $options['title'] . '</h2>' : '';
		echo isset( $options['help'] ) ? '<p class="description">' . $options['help'] . '</p>' : '';
		?>
		<div>
			<label><input type="checkbox" name="scht_settings[<?php echo esc_attr( $options['name'] ); ?>]"  <?php echo $checked; ?>><?php echo $label; ?></label>
		</div>
		<?php
	}

}