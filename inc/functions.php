<?php 

function is_html_file_exists( $file ) {
	// ファイルが存在するかを確認
	if ( is_file( $file ) ) {
		// ファイルの拡張子を取得
		$extension = pathinfo( $file, PATHINFO_EXTENSION );

		// 拡張子が 'htm' または 'html' なら true を返す
		return $extension === 'htm' || $extension === 'html';
	}

	// ファイルが存在しないか、拡張子が 'htm' または 'html' でない場合 false を返す
	return false;
}

/**
 * HTMLファイルを指定の部分で4つに分割する関数
 *
 * @param string $html HTML文字列
 * @return array 分割されたHTML文字列の配列
 */
function split_html( $html ) {
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

	// マッチしなかった場合、元のHTMLをそのまま返す
	return array( 'html' => $html );
}

function convert_relative_paths_to_absolute( $html_contents, $contents_url ) {
	// 正規表現でhref、src、srcset属性を検索
	$pattern = '/(href|src|srcset)=["\'](?!http|https|\/)([^"\']+)["\']/i';

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
					$src_parts[0] = resolve_relative_url( $contents_url, $src_parts[0] );
					return implode( ' ', $src_parts );
				},
				$sources
			);
			$absolute_url  = implode( ', ', $absolute_urls );
		} else {
			$absolute_url = resolve_relative_url( $contents_url, $relative_path );
		}

		return "{$attribute}=\"{$absolute_url}\"";
	};

	// 正規表現置換
	$updated_html = preg_replace_callback( $pattern, $callback, $html_contents );

	return $updated_html;
}

function resolve_relative_url( $base_url, $relative_url ) {
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

	// スキーム、ホスト、ポートを結合して完全なURLを生成
	$resolved_url = $parsed_base['scheme'] . '://' . $parsed_base['host'];
	if ( isset( $parsed_base['port'] ) ) {
		$resolved_url .= ':' . $parsed_base['port'];
	}
	$resolved_url .= $resolved_path;

	return $resolved_url;
}
