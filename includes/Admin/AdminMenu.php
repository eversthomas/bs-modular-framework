<?php

namespace BS\ModularFramework\Admin;

use BS\ModularFramework\Core\Capabilities;
use BS\ModularFramework\Data\ModuleRepository;
use wpdb;

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
			__( 'Modular Framework – Module', 'bs-modular-framework' ),
			__( 'Module', 'bs-modular-framework' ),
			Capabilities::manage_modules(),
			self::MENU_SLUG_MODULES,
			array( $this, 'render_modules_page' ),
			'dashicons-index-card',
			60
		);

		add_submenu_page(
			self::MENU_SLUG_MODULES,
			__( 'Felder (alle Module)', 'bs-modular-framework' ),
			__( 'Felder', 'bs-modular-framework' ),
			Capabilities::manage_modules(),
			'bs-modular-framework-fields',
			array( $this, 'render_fields_page' )
		);

		add_submenu_page(
			self::MENU_SLUG_MODULES,
			__( 'Einträge (alle Module)', 'bs-modular-framework' ),
			__( 'Einträge', 'bs-modular-framework' ),
			Capabilities::manage_entries(),
			'bs-modular-framework-entries',
			array( $this, 'render_entries_page' )
		);
	}

	/**
	 * Liefert alle Module für Übersichtsseiten.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	protected function get_all_modules(): array {
		global $wpdb;

		if ( ! $wpdb instanceof wpdb ) {
			return array();
		}

		if ( ! class_exists( \BS\ModularFramework\Data\Repository::class ) ) {
			require_once dirname( __DIR__, 1 ) . '/Data/Repository.php';
		}

		if ( ! class_exists( \BS\ModularFramework\Data\ModuleRepository::class ) ) {
			require_once dirname( __DIR__, 1 ) . '/Data/ModuleRepository.php';
		}

		if ( ! class_exists( \BS\ModularFramework\Domain\Module::class ) ) {
			require_once dirname( __DIR__, 1 ) . '/Domain/Module.php';
		}

		$repository = new ModuleRepository( $wpdb );

		$modules = $repository->find_all();

		$result = array();
		foreach ( $modules as $module ) {
			$result[ $module->id ] = array(
				'id'   => $module->id,
				'name' => $module->name,
				'slug' => $module->slug,
			);
		}

		return $result;
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

		$module_id = isset( $_GET['module_id'] ) ? (int) wp_unslash( $_GET['module_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( $module_id > 0 ) {
			if ( ! class_exists( __NAMESPACE__ . '\\FieldAdminPage' ) ) {
				require_once __DIR__ . '/FieldAdminPage.php';
			}

			$page = new FieldAdminPage();
			$page->handle_request();
			return;
		}

		$modules = $this->get_all_modules();

		require dirname( __DIR__, 2 ) . '/admin/views/fields-overview.php';
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

		$module_id = isset( $_GET['module_id'] ) ? (int) wp_unslash( $_GET['module_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( $module_id > 0 ) {
			if ( ! class_exists( __NAMESPACE__ . '\\EntryAdminPage' ) ) {
				require_once __DIR__ . '/EntryAdminPage.php';
			}

			$page = new EntryAdminPage();
			$page->handle_request();
			return;
		}

		$modules = $this->get_all_modules();

		require dirname( __DIR__, 2 ) . '/admin/views/entries-overview.php';
	}
}

