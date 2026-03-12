<?php

namespace BS\ModularFramework\Admin;

use BS\ModularFramework\Core\Capabilities;

defined( 'ABSPATH' ) || exit;

/**
 * Registriert Admin-Menüeinträge für das Plugin.
 */
class AdminMenu {

	public const MENU_SLUG_MODULES = 'bs-modular-framework-modules';

	/**
	 * Registriert das Hauptmenü und die Modul-Seite.
	 *
	 * @return void
	 */
	public function register(): void {
		add_menu_page(
			__( 'Modular Framework', 'bs-modular-framework' ),
			__( 'Modular Framework', 'bs-modular-framework' ),
			Capabilities::manage_modules(),
			self::MENU_SLUG_MODULES,
			array( $this, 'render_modules_page' ),
			'dashicons-index-card',
			60
		);
	}

	/**
	 * Callback für die Modulverwaltungs-Seite.
	 *
	 * @return void
	 */
	public function render_modules_page(): void {
		if ( ! current_user_can( Capabilities::manage_modules() ) ) {
			wp_die( esc_html__( 'Du hast keine Berechtigung, diese Seite zu sehen.', 'bs-modular-framework' ) );
		}

		if ( ! class_exists( __NAMESPACE__ . '\\ModuleAdminPage' ) ) {
			require_once __DIR__ . '/ModuleAdminPage.php';
		}

		$page = new ModuleAdminPage();
		$page->handle_request();
	}
}

