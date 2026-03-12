<?php

namespace BS\ModularFramework\Data;

use BS\ModularFramework\Domain\Entry;
use wpdb;

defined( 'ABSPATH' ) || exit;

/**
 * Repository für Einträge.
 */
class EntryRepository extends Repository {

	public function __construct( wpdb $db ) {
		parent::__construct( $db, $db->prefix . 'bs_mf_entries' );
	}

	public function find_by_id( int $id ): ?Entry {
		$row = $this->get_row_by_id( $id );
		return $row ? $this->map_row_to_entity( $row ) : null;
	}

	/**
	 * Erstellt oder aktualisiert einen Eintrag.
	 *
	 * @param Entry $entry Eintrag.
	 * @return int ID.
	 */
	public function save( Entry $entry ): int {
		$data = array(
			'module_id' => $entry->module_id,
			'title'     => $entry->title,
			'status'    => $entry->status,
			'sort_order'=> $entry->sort_order,
		);

		$formats = array( '%d', '%s', '%s', '%d' );

		if ( null === $entry->id ) {
			$this->db->insert( $this->table, $data, $formats );
			return (int) $this->db->insert_id;
		}

		$this->db->update(
			$this->table,
			$data,
			array( 'id' => $entry->id ),
			$formats,
			array( '%d' )
		);

		return (int) $entry->id;
	}

	/**
	 * Mappt eine DB-Zeile auf einen Eintrag.
	 *
	 * @param object $row DB-Zeile.
	 * @return Entry
	 */
	protected function map_row_to_entity( object $row ): Entry {
		return new Entry(
			(int) $row->id,
			(int) $row->module_id,
			(string) $row->title,
			(string) $row->status,
			(int) $row->sort_order,
			(string) $row->created_at,
			(string) $row->updated_at
		);
	}
}

