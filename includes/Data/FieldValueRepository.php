<?php

namespace BS\ModularFramework\Data;

use BS\ModularFramework\Domain\FieldValue;
use wpdb;

defined( 'ABSPATH' ) || exit;

/**
 * Repository für Feldwerte.
 */
class FieldValueRepository extends Repository {

	public function __construct( wpdb $db ) {
		parent::__construct( $db, $db->prefix . 'bs_mf_field_values' );
	}

	public function find_by_id( int $id ): ?FieldValue {
		$row = $this->get_row_by_id( $id );
		return $row ? $this->map_row_to_entity( $row ) : null;
	}

	/**
	 * Liefert alle Feldwerte zu einem Eintrag.
	 *
	 * @param int $entry_id Eintrags-ID.
	 * @return FieldValue[]
	 */
	public function find_by_entry_id( int $entry_id ): array {
		$sql  = "SELECT * FROM {$this->table} WHERE entry_id = %d";
		$rows = $this->db->get_results(
			$this->db->prepare( $sql, $entry_id ) // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		);

		if ( ! $rows ) {
			return array();
		}

		return array_map( array( $this, 'map_row_to_entity' ), $rows );
	}

	/**
	 * Speichert einen Feldwert.
	 *
	 * @param FieldValue $value Feldwert.
	 * @return int ID.
	 */
	public function save( FieldValue $value ): int {
		$data = array(
			'entry_id'      => $value->entry_id,
			'field_id'      => $value->field_id,
			'value_longtext'=> $value->value_longtext,
			'value_varchar' => $value->value_varchar,
			'value_int'     => $value->value_int,
			'value_date'    => $value->value_date,
		);

		$formats = array( '%d', '%d', '%s', '%s', '%d', '%s' );

		if ( null === $value->id ) {
			$this->db->insert( $this->table, $data, $formats );
			return (int) $this->db->insert_id;
		}

		$this->db->update(
			$this->table,
			$data,
			array( 'id' => $value->id ),
			$formats,
			array( '%d' )
		);

		return (int) $value->id;
	}

	/**
	 * Mappt eine DB-Zeile auf einen Feldwert.
	 *
	 * @param object $row DB-Zeile.
	 * @return FieldValue
	 */
	protected function map_row_to_entity( object $row ): FieldValue {
		return new FieldValue(
			(int) $row->id,
			(int) $row->entry_id,
			(int) $row->field_id,
			$row->value_longtext !== null ? (string) $row->value_longtext : null,
			$row->value_varchar !== null ? (string) $row->value_varchar : null,
			isset( $row->value_int ) ? (int) $row->value_int : null,
			$row->value_date !== null ? (string) $row->value_date : null,
			(string) $row->created_at,
			(string) $row->updated_at
		);
	}
}

