<?php

namespace BS\ModularFramework\Admin;

use BS\ModularFramework\Core\Capabilities;
use BS\ModularFramework\Data\FieldRepository;
use BS\ModularFramework\Data\ModuleRepository;
use BS\ModularFramework\Domain\FieldDefinition;
use BS\ModularFramework\Domain\Module;
use wpdb;

defined( 'ABSPATH' ) || exit;

/**
 * Controller für die Feldverwaltung je Modul.
 */
class FieldAdminPage {

	/**
	 * @var FieldRepository
	 */
	protected FieldRepository $fields;

	/**
	 * @var ModuleRepository
	 */
	protected ModuleRepository $modules;

	/**
	 * @var Module|null
	 */
	protected ?Module $current_module = null;

	public function __construct() {
		// Defensive Nachlader für benötigte Klassen, falls kein Autoloader aktiv ist.
		if ( ! class_exists( \BS\ModularFramework\Data\Repository::class ) ) {
			require_once dirname( __DIR__, 1 ) . '/Data/Repository.php';
		}

		if ( ! class_exists( \BS\ModularFramework\Data\FieldRepository::class ) ) {
			require_once dirname( __DIR__, 1 ) . '/Data/FieldRepository.php';
		}

		if ( ! class_exists( \BS\ModularFramework\Data\ModuleRepository::class ) ) {
			require_once dirname( __DIR__, 1 ) . '/Data/ModuleRepository.php';
		}

		if ( ! class_exists( \BS\ModularFramework\Domain\FieldDefinition::class ) ) {
			require_once dirname( __DIR__, 1 ) . '/Domain/FieldDefinition.php';
		}

		if ( ! class_exists( \BS\ModularFramework\Domain\Module::class ) ) {
			require_once dirname( __DIR__, 1 ) . '/Domain/Module.php';
		}

		global $wpdb;

		if ( ! $wpdb instanceof wpdb ) {
			wp_die( esc_html__( 'Datenbankverbindung nicht verfügbar.', 'bs-modular-framework' ) );
		}

		$this->fields  = new FieldRepository( $wpdb );
		$this->modules = new ModuleRepository( $wpdb );

		$module_id = $this->get_module_id_from_request();
		if ( ! $module_id ) {
			$this->handle_missing_module();
			return;
		}

		$this->current_module = $this->modules->find_by_id( $module_id );

		if ( ! $this->current_module ) {
			$this->handle_missing_module();
			return;
		}
	}

	/**
	 * Liefert die Modul-ID aus der Anfrage.
	 *
	 * @return int|null
	 */
	protected function get_module_id_from_request(): ?int {
		$id = isset( $_GET['module_id'] ) ? wp_unslash( $_GET['module_id'] ) : null; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( null === $id ) {
			return null;
		}

		$id = (int) $id;
		return $id > 0 ? $id : null;
	}

	/**
	 * Weicher Fallback, wenn kein Modul ausgewählt oder gefunden wurde.
	 *
	 * @return void
	 */
	protected function handle_missing_module(): void {
		add_settings_error(
			'bs_mf_fields',
			'bs_mf_fields_missing_module',
			esc_html__( 'Bitte wähle ein Modul aus, um dessen Felder zu verwalten.', 'bs-modular-framework' ),
			'error'
		);

		$redirect_url = add_query_arg(
			array(
				'page' => 'bs-modular-framework-fields',
			),
			admin_url( 'admin.php' )
		);

		wp_safe_redirect( $redirect_url );
		exit;
	}

	/**
	 * Steuert die Anzeige und Aktionen.
	 *
	 * @return void
	 */
	public function handle_request(): void {
		$action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( isset( $_POST['bs_mf_field_action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$this->handle_post();
			return;
		}

		if ( 'add' === $action ) {
			$this->render_form();
		} elseif ( 'edit' === $action ) {
			$this->render_form( $this->get_field_id_from_request() );
		} elseif ( 'delete' === $action ) {
			$this->handle_delete();
		} else {
			$this->render_list();
		}
	}

	/**
	 * Holt eine Feld-ID aus der Anfrage.
	 *
	 * @return int|null
	 */
	protected function get_field_id_from_request(): ?int {
		$id = isset( $_GET['id'] ) ? wp_unslash( $_GET['id'] ) : null; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( null === $id ) {
			return null;
		}

		$id = (int) $id;
		return $id > 0 ? $id : null;
	}

	/**
	 * Behandelt POST-Requests (Feld speichern).
	 *
	 * @return void
	 */
	protected function handle_post(): void {
		if ( ! current_user_can( Capabilities::manage_modules() ) ) {
			wp_die( esc_html__( 'Du hast keine Berechtigung, diese Aktion auszuführen.', 'bs-modular-framework' ) );
		}

		check_admin_referer( 'bs_mf_save_field' );

		$id         = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$label      = isset( $_POST['label'] ) ? sanitize_text_field( wp_unslash( $_POST['label'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$field_key  = isset( $_POST['field_key'] ) ? sanitize_key( wp_unslash( $_POST['field_key'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$field_type = isset( $_POST['field_type'] ) ? sanitize_key( wp_unslash( $_POST['field_type'] ) ) : 'text'; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$is_required = isset( $_POST['is_required'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$sort_order  = isset( $_POST['sort_order'] ) ? (int) $_POST['sort_order'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		// Select-Optionen als einfache Zeilenliste.
		$select_options_raw = isset( $_POST['select_options'] ) ? wp_unslash( $_POST['select_options'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		$errors = array();

		if ( '' === $label ) {
			$errors[] = __( 'Label ist erforderlich.', 'bs-modular-framework' );
		}

		if ( '' === $field_key ) {
			$field_key = sanitize_key( $label );
		}

		if ( '' === $field_key ) {
			$errors[] = __( 'Feld-Key ist erforderlich.', 'bs-modular-framework' );
		}

		// Validen Feldtyp sicherstellen.
		$allowed_types = $this->get_supported_field_types();
		if ( ! isset( $allowed_types[ $field_type ] ) ) {
			$field_type = 'text';
		}

		// Feld-Key-Eindeutigkeit innerhalb des Moduls prüfen.
		global $wpdb;
		$table  = $wpdb->prefix . 'bs_mf_fields';
		$query  = "SELECT id FROM {$table} WHERE module_id = %d AND field_key = %s";
		$params = array( $this->current_module->id, $field_key );

		if ( $id > 0 ) {
			$query   .= ' AND id != %d';
			$params[] = $id;
		}

		$existing_id = $wpdb->get_var( $wpdb->prepare( $query, $params ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		if ( $existing_id ) {
			$errors[] = __( 'Feld-Key ist innerhalb des Moduls bereits vergeben.', 'bs-modular-framework' );
		}

		// Select-Optionen aufbereiten.
		$config = null;
		if ( 'select' === $field_type ) {
			$options = array_filter(
				array_map(
					'trim',
					preg_split( '/\r\n|\r|\n/', (string) $select_options_raw )
				),
				static function ( $value ): bool {
					return '' !== $value;
				}
			);

			if ( empty( $options ) ) {
				$errors[] = __( 'Für Select-Felder müssen mindestens eine Option definiert werden.', 'bs-modular-framework' );
			} else {
				$config = array(
					'options' => array_values( $options ),
				);
			}
		}

		if ( ! empty( $errors ) ) {
			add_settings_error( 'bs_mf_fields', 'bs_mf_field_error', implode( ' ', $errors ), 'error' );
			$this->render_form( $id );
			return;
		}

		$now = current_time( 'mysql', true );

		$field = new FieldDefinition(
			$id > 0 ? $id : null,
			$this->current_module->id,
			$label,
			$field_key,
			$field_type,
			$is_required,
			$config,
			$sort_order,
			'active',
			$now,
			$now
		);

		$this->fields->save( $field );

		add_settings_error(
			'bs_mf_fields',
			'bs_mf_field_saved',
			$id > 0 ? __( 'Feld wurde aktualisiert.', 'bs-modular-framework' ) : __( 'Feld wurde erstellt.', 'bs-modular-framework' ),
			'updated'
		);

		$_GET['action'] = ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$this->render_list();
	}

	/**
	 * Löscht ein Feld.
	 *
	 * @return void
	 */
	protected function handle_delete(): void {
		if ( ! current_user_can( Capabilities::manage_modules() ) ) {
			wp_die( esc_html__( 'Du hast keine Berechtigung, diese Aktion auszuführen.', 'bs-modular-framework' ) );
		}

		$id = $this->get_field_id_from_request();
		if ( ! $id ) {
			$this->render_list();
			return;
		}

		check_admin_referer( 'bs_mf_delete_field_' . $id );

		$this->fields->delete_by_id( $id );

		add_settings_error(
			'bs_mf_fields',
			'bs_mf_field_deleted',
			__( 'Feld wurde gelöscht.', 'bs-modular-framework' ),
			'updated'
		);

		$this->render_list();
	}

	/**
	 * Rendert die Feldliste eines Moduls.
	 *
	 * @return void
	 */
	protected function render_list(): void {
		$current_module = $this->current_module;
		$fields         = $this->fields->find_by_module_id( $this->current_module->id );

		settings_errors( 'bs_mf_fields' );

		// Von includes/Admin/ zwei Ebenen hoch ins Plugin-Root und dort admin/views laden.
		require dirname( __DIR__, 2 ) . '/admin/views/fields-list.php';
	}

	/**
	 * Rendert das Formular zum Anlegen/Bearbeiten eines Feldes.
	 *
	 * @param int|null $id Feld-ID oder null.
	 * @return void
	 */
	protected function render_form( ?int $id = null ): void {
		$current_module = $this->current_module;
		$field          = null;

		if ( $id ) {
			$field = $this->fields->find_by_id( $id );
		}

		$supported_types = $this->get_supported_field_types();

		settings_errors( 'bs_mf_fields' );

		require dirname( __DIR__, 2 ) . '/admin/views/field-form.php';
	}

	/**
	 * Unterstützte Feldtypen (Key => Label).
	 *
	 * @return array<string, string>
	 */
	protected function get_supported_field_types(): array {
		return array(
			'text'     => __( 'Text (einzeilig)', 'bs-modular-framework' ),
			'textarea' => __( 'Textarea (mehrzeilig)', 'bs-modular-framework' ),
			'number'   => __( 'Zahl', 'bs-modular-framework' ),
			'email'    => __( 'E-Mail', 'bs-modular-framework' ),
			'url'      => __( 'URL', 'bs-modular-framework' ),
			'date'     => __( 'Datum', 'bs-modular-framework' ),
			'select'   => __( 'Select', 'bs-modular-framework' ),
			'checkbox' => __( 'Checkbox', 'bs-modular-framework' ),
			'image'    => __( 'Bild', 'bs-modular-framework' ),
		);
	}
}

