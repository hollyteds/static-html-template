<?php
/**
 * The SCHT_Sanitize_Upload_Data class provides methods for sanitizing upload data.
 */
class SCHT_Sanitize_Upload_Data {
	/**
	 * Sanitizes the input data.
	 *
	 * @param array $input The input data to be sanitized.
	 * @return array The sanitized input data.
	 */
	public static function sanitize( $input ) {
		// Reset the index numbers
		$sanitized_input = array();
		foreach ( $input as $key => $value ) {
			if ( self::is_valid_input( $value ) ) {
				$sanitized_input[] = array(
					'id'             => self::remove_spaces( $value['id'] ),
					'path'           => self::remove_spaces( $value['path'] ),
					'enqueue_styles'   => self::sanitize_enqueue( $value['enqueue_styles'] ),
					'enqueue_scripts'   => self::sanitize_enqueue( $value['enqueue_scripts'] ),
				);
			}
		}
		return array_values( $sanitized_input );
	}

	/**
	 * Checks if the input value is valid.
	 *
	 * @param array $value The input value to be checked.
	 * @return bool True if the input value is valid, false otherwise.
	 */
	private static function is_valid_input( $value ) {
		return ! empty( $value['id'] ) && ! empty( $value['path'] ) &&
			self::is_valid_id( $value['id'] ) && self::is_valid_path( $value['path'] );
	}

	/**
	 * Checks if the ID is valid.
	 *
	 * @param string $id The ID to be checked.
	* @return bool True if the ID is    lid, false otherwise.
	 */
	private static function is_valid_id( $id ) {
		// Check if the ID contains only alphanumeric characters, hyphens, and underscores
		return preg_match( '/^[a-zA-Z0-9_\-\s]+$/', $id );
	}

	/**
	 * Checks if the path is valid.
	 *
	 * @param string $path The path to be checked.
	 * @return bool True if the path is valid, false otherwise.
	 */
	private static function is_valid_path( $path ) {
		// Check if the path contains only valid characters for a relative directory path
		return preg_match( '/^[a-zA-Z0-9_\/\.\-\s]+$/', $path );
	}

	/**
	 * Removes spaces from the input string.
	 *
	 * This function uses regular expression to remove all spaces from the input string.
	 *
	 * @param string $input The input string to remove spaces from.
	 * @return string The input string with spaces removed.
	 */

	private static function remove_spaces( $input ) {
		// Remove spaces
		return preg_replace( '/\s+/', '', $input );
	}

	/**
	 * Sanitizes the enqueue properties by removing spaces and replacing newline characters with spaces.
	 *
	 * @param string $input The input data to be sanitized.
	 * @return string The sanitized input data.
	 */
	private static function sanitize_enqueue( $input ) {

		if ( ! preg_match( '/^[a-zA-Z0-9_\s\-\r\n]+$/', $input ) ) {
			return '';
		}

		// Replace newline characters with spaces
		$input = preg_replace( '/\s+/', PHP_EOL, $input );
		// Check if the input contains only alphanumeric characters, hyphens, and underscores
		// Return an empty string if the input is invalid

		return $input;
	}
}
