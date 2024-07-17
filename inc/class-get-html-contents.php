<?php
namespace Shct;
/**
 * Generates HTML data for a given path.
 *
 * @return array The generated HTML data.
 */

class GetHTMLContents {

	public $contents;
	public $links;
	private $path;
	private $replacements;

	public function __construct() {

		$options            = get_sht_options();
		$this->path         = $options['path'];
		$this->replacements = isset( $options['replacement'] ) && count( $options['replacement'] ) ? $options['replacement'] : null;

		if ( ! is_html_file_exists( $this->path ) ) {
			$this->contents = '';
			return;
		}

		$html_contents = self::get_html_data( $this->path );
		$file_url      = self::get_contents_url( $this->path );
		$html_contents = $html_contents ? self::convert_relative_paths_to_absolute( $html_contents, $file_url ) : null;
		// if($this->replacements) {
		//  $html_contents = self::replace_links($html_contents);
		// };
		$this->links    = self::get_dynamic_links( $html_contents ) ?? null;
		$this->contents = self::split_html( $html_contents );
		return;
	}

	/**
	 * Retrieves HTML data from a file and converts relative paths to absolute paths.
	 *
	 * @return string The HTML data with converted paths or a default error message if conversion fails.
	 */
	private function get_html_data() {

		return file_get_contents( $this->path );
	}

	/**
	* Retrieves the URL of the contents directory based on the given file path.
	*
	* @return string The URL of the contents directory.
	*/
	private function get_contents_url() {

		$contents_path = dirname( $this->path );

		return site_url( '/' ) . str_replace( $_SERVER['DOCUMENT_ROOT'], '', $contents_path );
	}

	/**
		* HTMLファイルを指定の部分で4つに分割する関数
		*
		* @param string $html HTML文字列
		* @return array 分割されたHTML文字列の配列
		*/
	private function split_html( $html ) {
		// 正規表現のパターンを作成
		$pattern = '/(<\/head>)(.*?)(<body[^>]*>)(.*?)(<\/body>)/is';

		// 正規表現で分割
		if ( preg_match( $pattern, $html, $matches ) ) {
			return array(
				'before_head_close'             => substr( $html, 0, strpos( $html, $matches[1] ) ),
				'head_close_to_body_open'       => $matches[1] . $matches[2] . $matches[3],
				'body_open_to_body_close_start' => $matches[4],
				'body_close_to_end'             => $matches[5] . substr( $html, strpos( $html, $matches[5] ) + strlen( $matches[5] ) ),
			);
		}

		return null;
	}

	/**
 * Converts relative paths to absolute paths in HTML contents.
 *
 * This function searches for href, src, and srcset attributes using regular expressions
 * and replaces the relative paths with absolute paths based on the provided contents URL.
 *
 * @param string $html_contents The HTML contents to be processed.
 * @param string $contents_url  The base URL used to resolve the relative paths.
 * @return string The updated HTML contents with absolute paths.
 */
	private function convert_relative_paths_to_absolute( $html_contents, $contents_url ) {

		// 正規表現でhref、src、srcset属性を検索
		$pattern = '/(href|src|srcset)=["\'](?!http|https|\/\/|#|tel:|mailto:|\[.*?\])([^"\']+)["\']/i';

		// 置換コールバック関数
		$callback = function ( $matches ) use ( $contents_url ) {
			$attribute     = $matches[1];
			$relative_path = $matches[2];

			// srcsetの場合は複数のパスが含まれる可能性があるため、個別に処理
			if ( $attribute === 'srcset' ) {
				$sources       = explode( ',', $relative_path );
				$absolute_urls = array_map(
					function ( $src ) use ( $contents_url ) {
						$src_parts    = preg_split( '/\s+/', trim( $src ) );
						$src_parts[0] = self::resolve_relative_url( $contents_url, $src_parts[0] );
						return implode( ' ', $src_parts );
					},
					$sources
				);
				$absolute_url  = implode( ', ', $absolute_urls );
			} else {
				$absolute_url = self::resolve_relative_url( $contents_url, $relative_path );
			}

			return "{$attribute}=\"{$absolute_url}\"";
		};

		// 正規表現置換
		$updated_html = preg_replace_callback( $pattern, $callback, $html_contents );

		return $updated_html;
	}

	/**
	 * Resolves a relative URL based on a base URL.
	 *
	 * @param string $base_url The base URL.
	 * @param string $relative_url The relative URL to resolve.
	 * @return string The resolved URL.
	 */
	private function resolve_relative_url( $base_url, $relative_url ) {
		// ベースURLをパースする
		$parsed_base = parse_url( $base_url );

		// ベースURLのパスを取得し、ディレクトリ区切りで分割する
		$base_path       = preg_replace( '/^\/+/', '/', $parsed_base['path'] );
		$base_path_parts = explode( '/', rtrim( $base_path, '/' ) );

		// 相対URLを分割する
		$relative_parts = explode( '/', $relative_url );

		// パスを組み立てるための配列を初期化
		$resolved_parts = $base_path_parts;

		// 相対URLの各部分を処理
		foreach ( $relative_parts as $part ) {
			if ( $part === '' || $part === '.' ) {
				// 空文字列または現在のディレクトリを無視
				continue;
			} elseif ( $part === '..' ) {
				// 親ディレクトリに戻る
				array_pop( $resolved_parts );
			} else {
				// 通常のパス部分を追加
				$resolved_parts[] = $part;
			}
		}

		// 結合して新しいパスを生成
		$resolved_path = implode( '/', $resolved_parts );

		return self::build_absolute_url( $parsed_base, $resolved_path );
	}

	/**
	 * Builds an absolute URL by combining the scheme, host, port, and resolved path.
	 *
	 * @param array  $parsed_base   The parsed base URL components.
	 * @param string $resolved_path The resolved path to be appended to the base URL.
	 *
	 * @return string The generated absolute URL.
	 */
	private function build_absolute_url( $parsed_base, $resolved_path ) {
		// スキーム、ホスト、ポートを結合して完全なURLを生成
		$resolved_url = $parsed_base['scheme'] . '://' . $parsed_base['host'];
		if ( isset( $parsed_base['port'] ) ) {
			$resolved_url .= ':' . $parsed_base['port'];
		}
		$resolved_url .= $resolved_path;

		return $resolved_url;
	}


	// private function replace_text( $text ) {

	//  // 置換処理
	//  foreach ( $this->replacements as $replacement ) {
	//      if ( ! isset( $replacement['target'] ) ) {
	//          continue;
	//      }
	//      $text = str_replace( $replacement['target'], $replacement['new'], $text );
	//  }

	//  return $text;
	// }

	/**
	 * Retrieves the href attribute values enclosed in square brackets from the provided HTML contents.
	 *
	 * @param string $html_contents The HTML contents to parse.
	 * @return array An array of unique href attribute values enclosed in square brackets.
	 */
	private function get_dynamic_links( $html_contents ) {
		libxml_use_internal_errors( true );
		// Load HTML
		$dom = new \DOMDocument();
		$dom->loadHTML( $html_contents );

		// Retrieve any errors
		$errors = libxml_get_errors();

		if ( ! empty( $errors ) ) {
			// Write error handling here
			foreach ( $errors as $error ) {
				// Log the error or other handling
			}
		}

		// Restore libxml error handling
		libxml_clear_errors();
		libxml_use_internal_errors( false );

		// Get a tags
		$links = $dom->getElementsByTagName( 'a' );

		// Array to store href attribute values
		$href_values = array();

		// Check href attribute values of a tags
		foreach ( $links as $link ) {
			$href = $link->getAttribute( 'href' );
			// Check if enclosed in []
			preg_match( '/\[(.*?)\]/', $href, $matches );
			if ( ! empty( $matches[1] ) ) {
				$href_values[] = $matches[1];
			}
		}

		// Remove duplicates and return
		return array_unique( $href_values );
	}
}
