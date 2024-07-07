<?php
/**
 * Plugin Name: Static Code Template
 * Text Domain: static-html-template
 * Description: 静的LPを組み込むためのプラグインです。ページテンプレートで「Static LP Format」を選択して、ショートコードを通してLPを出力します。
 * Version: 0.0.1
 * Author: Hollyteds.
 */
defined( 'ABSPATH' ) || exit;

// Post type.
require_once __DIR__ . '/inc/add-post-type.php';

// Functions.
require_once __DIR__ . '/inc/functions.php';

// Admin menu settings.
require_once __DIR__ . '/inc/admin-menu-settings.php';

// Sanitize
require_once __DIR__ . '/inc/sanitize-upload-data.php';

// dequeue
require_once __DIR__ . '/inc/enqueue-script.php';

add_action(
	'plugins_loaded',
	function () {
		load_plugin_textdomain( 'static-html-template', false, basename( __DIR__ ) . '/languages' );
	}
);

/**
 * Adds a new page template to the list of available templates in the theme.
 *
 * This function is a filter callback for the 'theme_page_templates' filter hook.
 * It adds a new template named 'Static LP Format' with the file name 'static-html-template.php'
 * to the list of available templates.
 *
 * @param array $templates An array of existing page templates.
 * @return array The modified array of page templates.
 */
add_filter(
	'theme_page_templates',
	function ( $templates ) {
		$templates['static-html-template.php'] = __( 'Static code template', 'static-html-template' );
		return $templates;
	}
);

/**
 * Adds a filter to modify the page template.
 *
 * This function checks if the current page template is 'static-html-template.php'.
 * If it is, it returns the path to the 'static-lp-template.php' file located in the plugin directory.
 * If not, it returns the original template path.
 *
 * @param string $template The current page template.
 * @return string The modified or original template path.
 */
add_filter(
	'page_template',
	function ( $template ) {
		if ( is_page_template( 'static-html-template.php' ) ) {
			return plugin_dir_path( __FILE__ ) . 'static-html-template.php';
		}
		return $template;
	}
);
