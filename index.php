<?php
namespace Shct;
/**
 * Plugin Name: Static Code Template
 * Text Domain: static-html-template
 * Description: 静的LPを組み込むためのプラグインです。ページテンプレートで「Static LP Format」を選択して、ショートコードを通してLPを出力します。
 * Version: 0.0.1
 * Author: Hollyteds.
 */
defined( 'ABSPATH' ) || exit;

define( 'SHT_DIR_URL', plugins_url( '/', __FILE__ ) );
define( 'SHT_DIR_PATH', plugin_dir_path( __FILE__ ) . '/' );
define( 'SHC_TEMPLATE_NAME', 'static-html-template.php' );

// Functions
require_once SHT_DIR_PATH . 'inc/functions.php';

// Post type
require_once SHT_DIR_PATH . 'inc/add-post-type.php';

// Admin menu settings
require_once SHT_DIR_PATH . 'inc/class-admin-settings.php';

// dequeue
require_once SHT_DIR_PATH . 'inc/enqueue-script.php';


new AdminSettings();
/**
 * Adds an action to load the plugin text domain when the plugins are loaded.
 *
 * This function loads the translation files for the 'static-html-template' plugin.
 * It is executed when the 'plugins_loaded' action is triggered.
 * The translation files are located in the '/languages' directory of the plugin.
 *
 * @since 1.0.0
 */
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
 * It adds a new template named 'Static LP Format' to the list of available templates.
 *
 * @param array $templates An array of existing page templates.
 * @return array The modified array of page templates.
 */
add_filter(
	'theme_page_templates',
	function ( $templates ) {
			$templates[ SHC_TEMPLATE_NAME ] = __( 'Static html template', 'static-html-template' );
		return $templates;
	}
);

/**
 * Adds a filter to modify the page template.
 *
 * This function checks if the current page template is shc template.
 * If it is, it returns the path to the 'static-lp-template.php' file located in the plugin directory.
 * If not, it returns the original template path.
 *
 * @param string $template The current page template.
 * @return string The modified or original template path.
 */
add_filter(
	'page_template',
	function ( $template ) {
		$shc_id = get_shc_id();

		if ( ! get_html_contents( $shc_id ) || ! is_static_page() || ! is_path_valid( $shc_id ) ) {
			return $template;
		}
		return plugin_dir_path( __FILE__ ) . SHC_TEMPLATE_NAME;
	}
);


// add_action('enqueue_block_editor_assets', function () {
//  wp_enqueue_script(
//          'gutenberg-custom-script',
//          plugin_dir_path( __FILE__ ) . 'assets/ts/settingPage.js',
//          array('wp-blocks', 'wp-element', 'wp-editor', 'wp-edit-post', 'wp-data'),
//          false,
//          true
//  );
// });
