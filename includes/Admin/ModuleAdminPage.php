<?php

namespace BS\ModularFramework\Admin;

use BS\ModularFramework\Core\Capabilities;
use BS\ModularFramework\Data\ModuleRepository;
use BS\ModularFramework\Domain\Module;
use wpdb;

defined( 'ABSPATH' ) || exit;

/**
 * Controller für die Modulverwaltung im Admin.
 */
class ModuleAdminPage {

	/**
	 * @var ModuleRepository
	 */
	protected ModuleRepository $modules;

	public function __construct() {
		// Sicherstellen, dass die benötigten Klassen verfügbar sind,
		// auch wenn kein Composer-Autoloader aktiv ist.
		if ( ! class_exists( \BS\ModularFramework\Data\Repository::class ) ) {
			require_once dirname( __DIR__, 1 ) . '/Data/Repository.php';
		}

		if ( ! class_exists( \BS\ModularFramework\Data\ModuleRepository::class ) ) {
			require_once dirname( __DIR__, 1 ) . '/Data/ModuleRepository.php';
		}

		if ( ! class_exists( \BS\ModularFramework\Domain\Module::class ) ) {
			require_once dirname( __DIR__, 1 ) . '/Domain/Module.php';
		}

		global $wpdb;

		if ( ! $wpdb instanceof wpdb ) {
			wp_die( esc_html__( 'Datenbankverbindung nicht verfügbar.', 'bs-modular-framework' ) );
		}

		$this->modules = new ModuleRepository( $wpdb );
	}

	/**
	 * Verarbeitet Anfrage und rendert die passende Ansicht.
	 *
	 * @return void
	 */
	public function handle_request(): void {
		$action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( isset( $_POST['bs_mf_module_action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$this->handle_post();
			return;
		}

		if ( 'add' === $action ) {
			$this->render_form();
		} elseif ( 'edit' === $action ) {
			$this->render_form( $this->get_module_id_from_request() );
		} elseif ( 'delete' === $action ) {
			$this->handle_delete();
		} else {
			$this->render_list();
		}
	}

	/**
	 * Holt eine Modul-ID aus der Anfrage.
	 *
	 * @return int|null
	 */
	protected function get_module_id_from_request(): ?int {
		$id = isset( $_GET['id'] ) ? wp_unslash( $_GET['id'] ) : null; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( null === $id ) {
			return null;
		}

		$id = (int) $id;
		return $id > 0 ? $id : null;
	}

	/**
	 * Behandelt POST-Requests zum Speichern eines Moduls.
	 *
	 * @return void
	 */
	protected function handle_post(): void {
		if ( ! current_user_can( Capabilities::manage_modules() ) ) {
			wp_die( esc_html__( 'Du hast keine Berechtigung, diese Aktion auszuführen.', 'bs-modular-framework' ) );
		}

		check_admin_referer( 'bs_mf_save_module' );

		$id          = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$name        = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$slug        = isset( $_POST['slug'] ) ? sanitize_title( wp_unslash( $_POST['slug'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$description = isset( $_POST['description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['description'] ) ) : null; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$status      = isset( $_POST['status'] ) ? sanitize_key( wp_unslash( $_POST['status'] ) ) : 'active'; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$sort_order  = isset( $_POST['sort_order'] ) ? (int) $_POST['sort_order'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		$errors = array();

		if ( '' === $name ) {
			$errors[] = __( 'Name ist erforderlich.', 'bs-modular-framework' );
		}

		if ( '' === $slug ) {
			$slug = sanitize_title( $name );
		}

		// Eindeutigkeit des Slugs prüfen.
		global $wpdb;
		$table  = $wpdb->prefix . 'bs_mf_modules';
		$query  = "SELECT id FROM {$table} WHERE slug = %s";
		$params = array( $slug );

		if ( $id > 0 ) {
			$query  .= ' AND id != %d';
			$params[] = $id;
		}

		$existing_id = $wpdb->get_var( $wpdb->prepare( $query, $params ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		if ( $existing_id ) {
			$errors[] = __( 'Slug ist bereits vergeben.', 'bs-modular-framework' );
		}

		if ( ! in_array( $status, array( 'active', 'inactive' ), true ) ) {
			$status = 'active';
		}

		if ( ! empty( $errors ) ) {
			add_settings_error( 'bs_mf_modules', 'bs_mf_module_error', implode( ' ', $errors ), 'error' );
			$this->render_form( $id );
			return;
		}

		$now = current_time( 'mysql', true );

		$module = new Module(
			$id > 0 ? $id : null,
			$name,
			$slug,
			$description,
			$status,
			$sort_order,
			$now,
			$now
		);

		$saved_id = $this->modules->save( $module );

		add_settings_error(
			'bs_mf_modules',
			'bs_mf_module_saved',
			$id > 0 ? __( 'Modul wurde aktualisiert.', 'bs-modular-framework' ) : __( 'Modul wurde erstellt.', 'bs-modular-framework' ),
			'updated'
		);

		// Nach dem Speichern Liste anzeigen.
		$_GET['action'] = ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$this->render_list();
	}

	/**
	 * Behandelt das Löschen eines Moduls.
	 *
	 * @return void
	 */
	protected function handle_delete(): void {
		if ( ! current_user_can( Capabilities::manage_modules() ) ) {
			wp_die( esc_html__( 'Du hast keine Berechtigung, diese Aktion auszuführen.', 'bs-modular-framework' ) );
		}

		$id = $this->get_module_id_from_request();
		if ( ! $id ) {
			$this->render_list();
			return;
		}

		check_admin_referer( 'bs_mf_delete_module_' . $id );

		$this->modules->delete_by_id( $id );

		add_settings_error(
			'bs_mf_modules',
			'bs_mf_module_deleted',
			__( 'Modul wurde gelöscht.', 'bs-modular-framework' ),
			'updated'
		);

		$this->render_list();
	}

	/**
	 * Rendert die Modulliste.
	 *
	 * @return void
	 */
	protected function render_list(): void {
		if ( ! class_exists( __NAMESPACE__ . '\\ModuleListTable' ) ) {
			require_once __DIR__ . '/ModuleListTable.php';
		}

		$table = new ModuleListTable( $this->modules );
		$table->prepare_items();

		$add_url = add_query_arg(
			array(
				'page'   => AdminMenu::MENU_SLUG_MODULES,
				'action' => 'add',
			),
			admin_url( 'admin.php' )
		);

		settings_errors( 'bs_mf_modules' );

		// Von includes/Admin/ eine Ebene hoch ins Plugin-Root und dort admin/views laden.
		require dirname( __DIR__, 2 ) . '/admin/views/modules-list.php';
	}

	/**
	 * Rendert das Formular zum Anlegen/Bearbeiten.
	 *
	 * @param int|null $id Modul-ID oder null.
	 * @return void
	 */
	protected function render_form( ?int $id = null ): void {
		$module = null;

		if ( $id ) {
			$module = $this->modules->find_by_id( $id );
		}

		settings_errors( 'bs_mf_modules' );

		// Von includes/Admin/ eine Ebene hoch ins Plugin-Root und dort admin/views laden.
		require dirname( __DIR__, 2 ) . '/admin/views/module-form.php';
	}
}

