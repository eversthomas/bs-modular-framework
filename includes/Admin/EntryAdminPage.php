<?php

namespace BS\ModularFramework\Admin;

use BS\ModularFramework\Core\Capabilities;
use BS\ModularFramework\Data\EntryRepository;
use BS\ModularFramework\Data\FieldRepository;
use BS\ModularFramework\Data\FieldValueRepository;
use BS\ModularFramework\Data\ModuleRepository;
use BS\ModularFramework\Domain\Entry;
use BS\ModularFramework\Domain\FieldDefinition;
use BS\ModularFramework\Domain\Module;
use BS\ModularFramework\Registry\FieldTypeRegistry;
use BS\ModularFramework\Support\Sanitizer;
use BS\ModularFramework\Support\Validator;
use wpdb;

defined( 'ABSPATH' ) || exit;

/**
 * Controller für die Eintragsverwaltung je Modul.
 */
class EntryAdminPage {

	protected EntryRepository $entries;
	protected ModuleRepository $modules;
	protected FieldRepository $fields;
	protected FieldValueRepository $field_values;
	protected FieldTypeRegistry $field_type_registry;
	protected Validator $validator;
	protected Sanitizer $sanitizer;

	protected ?Module $current_module = null;

	public function __construct() {
		// Defensive Nachlader für benötigte Klassen.
		if ( ! class_exists( \BS\ModularFramework\Data\Repository::class ) ) {
			require_once dirname( __DIR__, 1 ) . '/Data/Repository.php';
		}

		if ( ! class_exists( \BS\ModularFramework\Data\EntryRepository::class ) ) {
			require_once dirname( __DIR__, 1 ) . '/Data/EntryRepository.php';
		}

		if ( ! class_exists( \BS\ModularFramework\Data\ModuleRepository::class ) ) {
			require_once dirname( __DIR__, 1 ) . '/Data/ModuleRepository.php';
		}

		if ( ! class_exists( \BS\ModularFramework\Data\FieldRepository::class ) ) {
			require_once dirname( __DIR__, 1 ) . '/Data/FieldRepository.php';
		}

		if ( ! class_exists( \BS\ModularFramework\Data\FieldValueRepository::class ) ) {
			require_once dirname( __DIR__, 1 ) . '/Data/FieldValueRepository.php';
		}

		if ( ! class_exists( \BS\ModularFramework\Domain\Entry::class ) ) {
			require_once dirname( __DIR__, 1 ) . '/Domain/Entry.php';
		}

		if ( ! class_exists( \BS\ModularFramework\Domain\Module::class ) ) {
			require_once dirname( __DIR__, 1 ) . '/Domain/Module.php';
		}

		if ( ! class_exists( \BS\ModularFramework\Domain\FieldDefinition::class ) ) {
			require_once dirname( __DIR__, 1 ) . '/Domain/FieldDefinition.php';
		}

		if ( ! class_exists( \BS\ModularFramework\Registry\FieldTypeRegistry::class ) ) {
			require_once dirname( __DIR__, 1 ) . '/Registry/FieldTypeRegistry.php';
		}

		if ( ! class_exists( \BS\ModularFramework\Support\Validator::class ) ) {
			require_once dirname( __DIR__, 1 ) . '/Support/Validator.php';
		}

		if ( ! class_exists( \BS\ModularFramework\Support\Sanitizer::class ) ) {
			require_once dirname( __DIR__, 1 ) . '/Support/Sanitizer.php';
		}

		// Feldtypen.
		if ( ! class_exists( \BS\ModularFramework\FieldTypes\FieldTypeInterface::class ) ) {
			require_once dirname( __DIR__, 1 ) . '/FieldTypes/FieldTypeInterface.php';
		}
		if ( ! class_exists( \BS\ModularFramework\FieldTypes\AbstractFieldType::class ) ) {
			require_once dirname( __DIR__, 1 ) . '/FieldTypes/AbstractFieldType.php';
		}
		if ( ! class_exists( \BS\ModularFramework\FieldTypes\TextField::class ) ) {
			require_once dirname( __DIR__, 1 ) . '/FieldTypes/TextField.php';
		}
		if ( ! class_exists( \BS\ModularFramework\FieldTypes\TextareaField::class ) ) {
			require_once dirname( __DIR__, 1 ) . '/FieldTypes/TextareaField.php';
		}
		if ( ! class_exists( \BS\ModularFramework\FieldTypes\NumberField::class ) ) {
			require_once dirname( __DIR__, 1 ) . '/FieldTypes/NumberField.php';
		}
		if ( ! class_exists( \BS\ModularFramework\FieldTypes\EmailField::class ) ) {
			require_once dirname( __DIR__, 1 ) . '/FieldTypes/EmailField.php';
		}
		if ( ! class_exists( \BS\ModularFramework\FieldTypes\UrlField::class ) ) {
			require_once dirname( __DIR__, 1 ) . '/FieldTypes/UrlField.php';
		}
		if ( ! class_exists( \BS\ModularFramework\FieldTypes\DateField::class ) ) {
			require_once dirname( __DIR__, 1 ) . '/FieldTypes/DateField.php';
		}
		if ( ! class_exists( \BS\ModularFramework\FieldTypes\SelectField::class ) ) {
			require_once dirname( __DIR__, 1 ) . '/FieldTypes/SelectField.php';
		}
		if ( ! class_exists( \BS\ModularFramework\FieldTypes\CheckboxField::class ) ) {
			require_once dirname( __DIR__, 1 ) . '/FieldTypes/CheckboxField.php';
		}
		if ( ! class_exists( \BS\ModularFramework\FieldTypes\ImageField::class ) ) {
			require_once dirname( __DIR__, 1 ) . '/FieldTypes/ImageField.php';
		}

		global $wpdb;

		if ( ! $wpdb instanceof wpdb ) {
			wp_die( esc_html__( 'Datenbankverbindung nicht verfügbar.', 'bs-modular-framework' ) );
		}

		$this->entries       = new EntryRepository( $wpdb );
		$this->modules       = new ModuleRepository( $wpdb );
		$this->fields        = new FieldRepository( $wpdb );
		$this->field_values  = new FieldValueRepository( $wpdb );
		$this->field_type_registry = new FieldTypeRegistry();

		// Feldtypen-Registry befüllen.
		$this->field_type_registry->register( new \BS\ModularFramework\FieldTypes\TextField() );
		$this->field_type_registry->register( new \BS\ModularFramework\FieldTypes\TextareaField() );
		$this->field_type_registry->register( new \BS\ModularFramework\FieldTypes\NumberField() );
		$this->field_type_registry->register( new \BS\ModularFramework\FieldTypes\EmailField() );
		$this->field_type_registry->register( new \BS\ModularFramework\FieldTypes\UrlField() );
		$this->field_type_registry->register( new \BS\ModularFramework\FieldTypes\DateField() );
		$this->field_type_registry->register( new \BS\ModularFramework\FieldTypes\SelectField() );
		$this->field_type_registry->register( new \BS\ModularFramework\FieldTypes\CheckboxField() );
		$this->field_type_registry->register( new \BS\ModularFramework\FieldTypes\ImageField() );

		// FieldTypes werden in späteren Phasen/Bootstrap global registriert werden.
		$this->validator = new Validator( $this->field_type_registry );
		$this->sanitizer = new Sanitizer( $this->field_type_registry );

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
			'bs_mf_entries',
			'bs_mf_entries_missing_module',
			esc_html__( 'Bitte wähle ein Modul aus, um dessen Einträge zu verwalten.', 'bs-modular-framework' ),
			'error'
		);

		$redirect_url = add_query_arg(
			array(
				'page' => 'bs-modular-framework-entries',
			),
			admin_url( 'admin.php' )
		);

		wp_safe_redirect( $redirect_url );
		exit;
	}

	protected function get_entry_id_from_request(): ?int {
		$id = isset( $_GET['id'] ) ? wp_unslash( $_GET['id'] ) : null; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( null === $id ) {
			return null;
		}

		$id = (int) $id;
		return $id > 0 ? $id : null;
	}

	/**
	 * Steuert Anzeige/Aktionen.
	 */
	public function handle_request(): void {
		$action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( isset( $_POST['bs_mf_entry_action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$this->handle_post();
			return;
		}

		if ( 'add' === $action ) {
			$this->render_form();
		} elseif ( 'edit' === $action ) {
			$this->render_form( $this->get_entry_id_from_request() );
		} elseif ( 'delete' === $action ) {
			$this->handle_delete();
		} else {
			$this->render_list();
		}
	}

	protected function handle_post(): void {
		if ( ! current_user_can( Capabilities::manage_entries() ) ) {
			wp_die( esc_html__( 'Du hast keine Berechtigung, diese Aktion auszuführen.', 'bs-modular-framework' ) );
		}

		check_admin_referer( 'bs_mf_save_entry' );

		$id         = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$title      = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$status     = isset( $_POST['status'] ) ? sanitize_key( wp_unslash( $_POST['status'] ) ) : 'draft'; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$sort_order = isset( $_POST['sort_order'] ) ? (int) $_POST['sort_order'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		$errors = array();

		if ( '' === $title ) {
			$errors[] = __( 'Titel ist erforderlich.', 'bs-modular-framework' );
		}

		if ( ! in_array( $status, array( 'draft', 'published' ), true ) ) {
			$status = 'draft';
		}

		// Feldwerte einsammeln und validieren.
		$fields = $this->fields->find_by_module_id( $this->current_module->id );
		$field_values_input = array();

		/** @var FieldDefinition $field */
		foreach ( $fields as $field ) {
			$key = 'field_' . $field->id;
			$raw = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : null; // phpcs:ignore WordPress.Security.NonceVerification.Missing

			// Checkbox: nicht gesetztes Feld bedeutet false.
			if ( 'checkbox' === $field->field_type ) {
				$raw = isset( $_POST[ $key ] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
			}

			$validation = $this->validator->validate_field( $field, $raw );
			if ( true !== $validation ) {
				$errors[] = sprintf(
					/* translators: %s: Feldlabel. */
					__( 'Fehler im Feld „%s“: %s', 'bs-modular-framework' ),
					$field->label,
					(string) $validation
				);
			}

			$field_values_input[ $field->id ] = $raw;
		}

		if ( ! empty( $errors ) ) {
			add_settings_error( 'bs_mf_entries', 'bs_mf_entry_error', implode( ' ', $errors ), 'error' );
			$this->render_form( $id ?: null, $field_values_input );
			return;
		}

		$now = current_time( 'mysql', true );

		$entry = new Entry(
			$id > 0 ? $id : null,
			$this->current_module->id,
			$title,
			$status,
			$sort_order,
			$now,
			$now
		);

		$entry_id = $this->entries->save( $entry );

		// Feldwerte speichern.
		foreach ( $fields as $field ) {
			$raw    = $field_values_input[ $field->id ] ?? null;
			$stored = $this->sanitizer->sanitize_for_storage( $field, $raw );

			// Für Einfachheit: vorhandene Werte für diesen Eintrag/Feld löschen und neu schreiben.
			$this->delete_field_value( $entry_id, $field->id );
			$this->insert_field_value( $entry_id, $field->id, $stored );
		}

		add_settings_error(
			'bs_mf_entries',
			'bs_mf_entry_saved',
			$id > 0 ? __( 'Eintrag wurde aktualisiert.', 'bs-modular-framework' ) : __( 'Eintrag wurde erstellt.', 'bs-modular-framework' ),
			'updated'
		);

		$_GET['action'] = ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$this->render_list();
	}

	protected function delete_field_value( int $entry_id, int $field_id ): void {
		global $wpdb;
		$table = $wpdb->prefix . 'bs_mf_field_values';

		$wpdb->delete(
			$table,
			array(
				'entry_id' => $entry_id,
				'field_id' => $field_id,
			),
			array( '%d', '%d' )
		);
	}

	protected function insert_field_value( int $entry_id, int $field_id, array $stored ): void {
		global $wpdb;
		$table = $wpdb->prefix . 'bs_mf_field_values';

		$wpdb->insert(
			$table,
			array(
				'entry_id'      => $entry_id,
				'field_id'      => $field_id,
				'value_longtext'=> $stored['value_longtext'],
				'value_varchar' => $stored['value_varchar'],
				'value_int'     => $stored['value_int'],
				'value_date'    => $stored['value_date'],
			),
			array( '%d', '%d', '%s', '%s', '%d', '%s' )
		);
	}

	protected function handle_delete(): void {
		if ( ! current_user_can( Capabilities::manage_entries() ) ) {
			wp_die( esc_html__( 'Du hast keine Berechtigung, diese Aktion auszuführen.', 'bs-modular-framework' ) );
		}

		$id = $this->get_entry_id_from_request();
		if ( ! $id ) {
			$this->render_list();
			return;
		}

		check_admin_referer( 'bs_mf_delete_entry_' . $id );

		// Eintrag löschen.
		$this->entries->delete_by_id( $id );

		// Zugehörige Feldwerte löschen.
		global $wpdb;
		$table = $wpdb->prefix . 'bs_mf_field_values';
		$wpdb->delete( $table, array( 'entry_id' => $id ), array( '%d' ) );

		add_settings_error(
			'bs_mf_entries',
			'bs_mf_entry_deleted',
			__( 'Eintrag wurde gelöscht.', 'bs-modular-framework' ),
			'updated'
		);

		$this->render_list();
	}

	protected function render_list(): void {
		if ( ! class_exists( __NAMESPACE__ . '\\EntryListTable' ) ) {
			require_once __DIR__ . '/EntryListTable.php';
		}

		$current_module = $this->current_module;
		$table          = new EntryListTable( $this->entries, $this->current_module );
		$table->prepare_items();

		settings_errors( 'bs_mf_entries' );

		require dirname( __DIR__, 2 ) . '/admin/views/entries-list.php';
	}

	protected function render_form( ?int $id = null, array $submitted_values = array() ): void {
		$current_module = $this->current_module;
		$entry          = null;

		if ( $id ) {
			$entry = $this->entries->find_by_id( $id );
		}

		$fields        = $this->fields->find_by_module_id( $this->current_module->id );
		$field_values  = $this->load_field_values_for_entry( $id, $fields );

		// Falls Validierungsfehler mit bereits eingegebenen Werten vorliegen.
		if ( ! empty( $submitted_values ) ) {
			foreach ( $fields as $field ) {
				if ( array_key_exists( $field->id, $submitted_values ) ) {
					$field_values[ $field->id ] = $submitted_values[ $field->id ];
				}
			}
		}

		settings_errors( 'bs_mf_entries' );

		require dirname( __DIR__, 2 ) . '/admin/views/entry-form.php';
	}

	/**
	 * Lädt die Feldwerte eines Eintrags und baut eine einfache Map field_id => domänennaher Wert.
	 *
	 * @param int|null           $entry_id Eintrags-ID.
	 * @param FieldDefinition[]  $fields   Felddefinitionen.
	 * @return array<int, mixed>
	 */
	protected function load_field_values_for_entry( ?int $entry_id, array $fields ): array {
		if ( ! $entry_id ) {
			return array();
		}

		global $wpdb;
		$table = $wpdb->prefix . 'bs_mf_field_values';

		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$table} WHERE entry_id = %d",
				$entry_id
			),
			ARRAY_A
		); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		if ( ! $rows ) {
			return array();
		}

		$by_field = array();
		$fields_by_id = array();
		foreach ( $fields as $field ) {
			$fields_by_id[ $field->id ] = $field;
		}

		foreach ( $rows as $row ) {
			$field_id = (int) $row['field_id'];
			if ( ! isset( $fields_by_id[ $field_id ] ) ) {
				continue;
			}

			$field  = $fields_by_id[ $field_id ];
			$stored = array(
				'value_longtext' => $row['value_longtext'],
				'value_varchar'  => $row['value_varchar'],
				'value_int'      => null !== $row['value_int'] ? (int) $row['value_int'] : null,
				'value_date'     => $row['value_date'],
			);

			$by_field[ $field_id ] = $this->sanitizer->value_from_storage( $field, $stored );
		}

		return $by_field;
	}
}

