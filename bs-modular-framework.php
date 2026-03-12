<?php
/**
 * Plugin Name:       BS_Modular-Framework
 * Plugin URI:        https://example.com/
 * Description:       Tabellenbasiertes, modulares Datenframework für strukturierte Inhalte im WordPress-Backend.
 * Version:           0.1.0
 * Author:            BS
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       bs-modular-framework
 * Domain Path:       /languages
 * Requires at least: 6.3
 * Requires PHP:      8.0
 */

defined( 'ABSPATH' ) || exit;

use BS\ModularFramework\Core\Plugin;
use BS\ModularFramework\Core\Activator;
use BS\ModularFramework\Core\Deactivator;

// Composer Autoloader (falls vorhanden).
$bs_mf_autoload = __DIR__ . '/vendor/autoload.php';
if ( file_exists( $bs_mf_autoload ) ) {
	require_once $bs_mf_autoload;
} else {
	// Fallback-Autoloader für BS\ModularFramework\ Klassen ohne Composer.
	spl_autoload_register(
		static function ( $class ): void {
			if ( ! is_string( $class ) ) {
				return;
			}

			$prefix = 'BS\\ModularFramework\\';

			if ( 0 !== strpos( $class, $prefix ) ) {
				return;
			}

			$relative_class = substr( $class, strlen( $prefix ) );
			$relative_path  = str_replace( '\\', DIRECTORY_SEPARATOR, $relative_class ) . '.php';

			$file = __DIR__ . '/includes/' . $relative_path;

			if ( file_exists( $file ) ) {
				require_once $file;
			}
		}
	);
}

// Sicherstellen, dass Kernklassen für Aktivierung/Deaktivierung verfügbar sind.
if ( ! class_exists( \BS\ModularFramework\Core\Capabilities::class ) ) {
	require_once __DIR__ . '/includes/Core/Capabilities.php';
}

if ( ! class_exists( \BS\ModularFramework\Core\Loader::class ) ) {
	require_once __DIR__ . '/includes/Core/Loader.php';
}

if ( ! class_exists( \BS\ModularFramework\Core\Migrator::class ) ) {
	require_once __DIR__ . '/includes/Core/Migrator.php';
}

if ( ! class_exists( Activator::class ) ) {
	require_once __DIR__ . '/includes/Core/Activator.php';
}

if ( ! class_exists( Deactivator::class ) ) {
	require_once __DIR__ . '/includes/Core/Deactivator.php';
}

if ( ! class_exists( Plugin::class ) ) {
	require_once __DIR__ . '/includes/Core/Plugin.php';
}

/**
 * Startet das Plugin.
 *
 * @return void
 */
function bs_mf_run_plugin() {
	$plugin = new Plugin();
	$plugin->run();
}

register_activation_hook( __FILE__, array( Activator::class, 'activate' ) );
register_deactivation_hook( __FILE__, array( Deactivator::class, 'deactivate' ) );

// Bootstrapping nach dem Laden aller Plugins.
add_action( 'plugins_loaded', 'bs_mf_run_plugin' );

