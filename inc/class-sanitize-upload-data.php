<?php
namespace Shct;
/**
 * The SCHT_Sanitize_Upload_Data class provides methods for sanitizing upload data.
 */
class SanitizeUploadData {

	/**
	 * Sanitizes the given data by removing any characters that are not alphanumeric, underscore, forward slash, dot, hyphen, or whitespace.
	 *
	 * @param string $data The data to be sanitized.
	 * @return string The sanitized data.
	 */
	public static function path( $data ) {

		return sanitize_url( preg_replace( '/[^a-zA-Z0-9_\/\.\-]/', '', $data ) );
	}

	/**
	 * Sanitizes the enqueue properties by removing spaces and replacing newline characters with spaces.
	 *
	 * @param string $data The input data to be sanitized.
	 * @return string The sanitized input data.
	 */
	public static function enqueue( $data ) {

		$sanitized_sata = preg_replace( '/[^a-zA-Z0-9_\s\-\r\n]/', '', $data );

		// Replace newline characters with spaces
		return preg_replace( '/\s+/', PHP_EOL, $sanitized_sata );
	}


	/**
	 * Sanitizes the given data by removing all non-numeric characters.
	 *
	 * @param mixed $data The data to be sanitized.
	 * @return mixed The sanitized data.
	 */
	public static function number( $data ) {
		return filter_var( $data, FILTER_SANITIZE_NUMBER_INT );
	}


	/**
	 * Sanitizes a URL.
	 *
	 * @param string $data The URL to be sanitized.
	 * @return string The sanitized URL.
	 */
	public static function url( $data ) {
		return self::encode_mb( sanitize_url( $data ) );
	}

	/**
	 * Sanitizes an array using a callback function.
	 *
	 * @param array $array The array to be sanitized.
	 * @param string $callback The callback function to be applied to each element of the array.
	 * @return array The sanitized array.
	 */
	public static function array( $array_data, $callback ) {
		$sanitized_data = array();
		foreach ( $array_data as $key => $value ) {
			$sanitized_data[ $key ] = self::$callback( $value );
			error_log( $sanitized_data[ $key ] );
		}

		return $sanitized_data;
	}


	/**
	 * Encodes multi-byte characters in a string to URL-encoded format.
	 *
	 * @param string $data The string to be encoded.
	 * @return string The URL-encoded string.
	 */
	private function encode_mb( $data ) {
		// 全角文字をURLエンコード形式に変換する
		$encode_data = '';
		for ( $i = 0; $i < mb_strlen( $data, 'UTF-8' ); $i++ ) {
			$char = mb_substr( $data, $i, 1, 'UTF-8' );
			if ( strlen( $char ) > 1 ) {
				$encode_data .= '%' . strtoupper( bin2hex( $char ) );
			} else {
				$encode_data .= $char;
			}
		}
		return $encode_data;
	}

}
