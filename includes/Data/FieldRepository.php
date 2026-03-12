<?php

namespace BS\ModularFramework\Data;

use BS\ModularFramework\Domain\FieldDefinition;
use wpdb;

defined( 'ABSPATH' ) || exit;

/**
 * Repository für Felddefinitionen.
 */
class FieldRepository extends Repository {

	public function __construct( wpdb $db ) {
		parent::__construct( $db, $db->prefix . 'bs_mf_fields' );
	}

	public function find_by_id( int $id ): ?FieldDefinition {
		$row = $this->get_row_by_id( $id );
		return $row ? $this->map_row_to_entity( $row ) : null;
	}

	/**
	 * Liefert alle Felder eines Moduls.
	 *
	 * @param int $module_id Modul-ID.
	 * @return FieldDefinition[]
	 */
	public function find_by_module_id( int $module_id ): array {
		$sql  = "SELECT * FROM {$this->table} WHERE module_id = %d ORDER BY sort_order ASC, id ASC";
		$rows = $this->db->get_results(
			$this->db->prepare( $sql, $module_id ) // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		);

		if ( ! $rows ) {
			return array();
		}

		return array_map( array( $this, 'map_row_to_entity' ), $rows );
	}

	/**
	 * Erstellt oder aktualisiert eine Felddefinition.
	 *
	 * @param FieldDefinition $field Felddefinition.
	 * @return int ID.
	 */
	public function save( FieldDefinition $field ): int {
		$config_json = null !== $field->config ? wp_json_encode( $field->config ) : null;

		$data = array(
			'module_id'  => $field->module_id,
			'label'      => $field->label,
			'field_key'  => $field->field_key,
			'field_type' => $field->field_type,
			'is_required'=> $field->is_required ? 1 : 0,
			'config_json'=> $config_json,
			'sort_order' => $field->sort_order,
			'status'     => $field->status,
		);

		$formats = array( '%d', '%s', '%s', '%s', '%d', '%s', '%d', '%s' );

		if ( null === $field->id ) {
			$this->db->insert( $this->table, $data, $formats );
			return (int) $this->db->insert_id;
		}

		$this->db->update(
			$this->table,
			$data,
			array( 'id' => $field->id ),
			$formats,
			array( '%d' )
		);

		return (int) $field->id;
	}

	/**
	 * Mappt eine DB-Zeile auf eine Felddefinition.
	 *
	 * @param object $row DB-Zeile.
	 * @return FieldDefinition
	 */
	protected function map_row_to_entity( object $row ): FieldDefinition {
		$config = null;
		if ( isset( $row->config_json ) && null !== $row->config_json ) {
			$decoded = json_decode( (string) $row->config_json, true );
			if ( is_array( $decoded ) ) {
				$config = $decoded;
			}
		}

		return new FieldDefinition(
			(int) $row->id,
			(int) $row->module_id,
			(string) $row->label,
			(string) $row->field_key,
			(string) $row->field_type,
			(bool) $row->is_required,
			$config,
			(int) $row->sort_order,
			(string) $row->status,
			(string) $row->created_at,
			(string) $row->updated_at
		);
	}
}

