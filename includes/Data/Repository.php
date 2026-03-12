<?php

namespace BS\ModularFramework\Data;

use wpdb;

defined( 'ABSPATH' ) || exit;

/**
 * Abstraktes Repository mit Basiszugriff auf $wpdb.
 */
abstract class Repository {

	/**
	 * WordPress-Datenbank-Instanz.
	 *
	 * @var wpdb
	 */
	protected wpdb $db;

	/**
	 * Tabellenname (inkl. Prefix).
	 *
	 * @var string
	 */
	protected string $table;

	/**
	 * Konstruktor.
	 *
	 * @param wpdb  $db    DB-Instanz.
	 * @param string $table Tabellenname (inklusive Prefix).
	 */
	public function __construct( wpdb $db, string $table ) {
		$this->db    = $db;
		$this->table = $table;
	}

	/**
	 * Holt einen Datensatz per ID.
	 *
	 * @param int $id ID.
	 * @return object|null
	 */
	protected function get_row_by_id( int $id ): ?object {
		$sql = "SELECT * FROM {$this->table} WHERE id = %d";

		$row = $this->db->get_row(
			$this->db->prepare( $sql, $id ) // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		);

		return $row ?: null;
	}

	/**
	 * Löscht einen Datensatz per ID.
	 *
	 * @param int $id ID.
	 * @return bool
	 */
	public function delete_by_id( int $id ): bool {
		$deleted = $this->db->delete(
			$this->table,
			array( 'id' => $id ),
			array( '%d' )
		);

		return ( false !== $deleted );
	}
}

