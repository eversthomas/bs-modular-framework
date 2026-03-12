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

		add_submenu_page(
			self::MENU_SLUG_MODULES,
			__( 'Felder', 'bs-modular-framework' ),
			__( 'Felder', 'bs-modular-framework' ),
			Capabilities::manage_modules(),
			'bs-modular-framework-fields',
			array( $this, 'render_fields_page' )
		);

		add_submenu_page(
			self::MENU_SLUG_MODULES,
			__( 'Einträge', 'bs-modular-framework' ),
			__( 'Einträge', 'bs-modular-framework' ),
			Capabilities::manage_entries(),
			'bs-modular-framework-entries',
			array( $this, 'render_entries_page' )
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

	/**
	 * Callback für die Feldverwaltungs-Seite.
	 *
	 * @return void
	 */
	public function render_fields_page(): void {
		if ( ! current_user_can( Capabilities::manage_modules() ) ) {
			wp_die( esc_html__( 'Du hast keine Berechtigung, diese Seite zu sehen.', 'bs-modular-framework' ) );
		}

		if ( ! class_exists( __NAMESPACE__ . '\\FieldAdminPage' ) ) {
			require_once __DIR__ . '/FieldAdminPage.php';
		}

		$page = new FieldAdminPage();
		$page->handle_request();
	}

	/**
	 * Callback für die Eintragsverwaltungs-Seite.
	 *
	 * @return void
	 */
	public function render_entries_page(): void {
		if ( ! current_user_can( Capabilities::manage_entries() ) ) {
			wp_die( esc_html__( 'Du hast keine Berechtigung, diese Seite zu sehen.', 'bs-modular-framework' ) );
		}

		if ( ! class_exists( __NAMESPACE__ . '\\EntryAdminPage' ) ) {
			require_once __DIR__ . '/EntryAdminPage.php';
		}

		$page = new EntryAdminPage();
		$page->handle_request();
	}
}

