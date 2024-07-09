<?php
namespace Shct;
/**
 * The SCHT_Sanitize_Upload_Data class provides methods for sanitizing upload data.
 */
class SanitizeUploadData {
	/**
	 * Sanitizes the input data.
	 *
	 * @param array $input The input data to be sanitized.
	 * @return array The sanitized input data.
	 */
	public static function sanitize( $input ) {

		return array(
			'path'            => self::sanitize_path( $input['path'] ),
			'enqueue_styles'  => self::sanitize_enqueue( $input['enqueue_styles'] ),
			'enqueue_scripts' => self::sanitize_enqueue( $input['enqueue_scripts'] ),
		);
	}

	/**
	 * Sanitizes the given data by removing any characters that are not alphanumeric, underscore, forward slash, dot, hyphen, or whitespace.
	 *
	 * @param string $data The data to be sanitized.
	 * @return string The sanitized data.
	 */
	private static function sanitize_path( $data ) {

		return preg_replace( '/[^a-zA-Z0-9_\/\.\-]/', '', $data );
	}

	/**
	 * Sanitizes the enqueue properties by removing spaces and replacing newline characters with spaces.
	 *
	 * @param string $data The input data to be sanitized.
	 * @return string The sanitized input data.
	 */
	private static function sanitize_enqueue( $data ) {

		$sanitized_sata = preg_replace( '/[^a-zA-Z0-9_\s\-\r\n]/', '', $data );

		// Replace newline characters with spaces
		return preg_replace( '/\s+/', PHP_EOL, $sanitized_sata );
	}
}
