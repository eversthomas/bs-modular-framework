<?php

namespace BS\ModularFramework\Data;

use BS\ModularFramework\Domain\Module;
use wpdb;

defined( 'ABSPATH' ) || exit;

/**
 * Repository für Module.
 */
class ModuleRepository extends Repository {

	public function __construct( wpdb $db ) {
		parent::__construct( $db, $db->prefix . 'bs_mf_modules' );
	}

	public function find_by_id( int $id ): ?Module {
		$row = $this->get_row_by_id( $id );
		return $row ? $this->map_row_to_entity( $row ) : null;
	}

	/**
	 * Erstellt oder aktualisiert ein Modul.
	 *
	 * @param Module $module Modul-Entität.
	 * @return int ID.
	 */
	public function save( Module $module ): int {
		$data = array(
			'name'        => $module->name,
			'slug'        => $module->slug,
			'description' => $module->description,
			'status'      => $module->status,
			'sort_order'  => $module->sort_order,
		);

		if ( null === $module->id ) {
			$this->db->insert(
				$this->table,
				$data,
				array( '%s', '%s', '%s', '%s', '%d' )
			);

			return (int) $this->db->insert_id;
		}

		$this->db->update(
			$this->table,
			$data,
			array( 'id' => $module->id ),
			array( '%s', '%s', '%s', '%s', '%d' ),
			array( '%d' )
		);

		return (int) $module->id;
	}

	/**
	 * Mappt eine DB-Zeile auf ein Modul.
	 *
	 * @param object $row DB-Zeile.
	 * @return Module
	 */
	protected function map_row_to_entity( object $row ): Module {
		return new Module(
			(int) $row->id,
			(string) $row->name,
			(string) $row->slug,
			$row->description !== null ? (string) $row->description : null,
			(string) $row->status,
			(int) $row->sort_order,
			(string) $row->created_at,
			(string) $row->updated_at
		);
	}
}

