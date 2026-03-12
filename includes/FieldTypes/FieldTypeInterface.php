<?php

namespace BS\ModularFramework\FieldTypes;

defined( 'ABSPATH' ) || exit;

/**
 * Interface für alle Feldtypen.
 */
interface FieldTypeInterface {

	/**
	 * Schlüsseleintrag des Feldtyps (z. B. text, number).
	 *
	 * @return string
	 */
	public function get_key(): string;

	/**
	 * Validiert einen rohen Wert.
	 *
	 * @param mixed $value Wert.
	 * @param array $config Feldkonfiguration.
	 * @return bool
	 */
	public function is_valid( $value, array $config = array() ): bool;

	/**
	 * Sanitized einen rohen Wert.
	 *
	 * @param mixed $value Wert.
	 * @param array $config Feldkonfiguration.
	 * @return mixed
	 */
	public function sanitize( $value, array $config = array() );

	/**
	 * Normalisiert den Wert in die Speicherstruktur (value_longtext, value_varchar, value_int, value_date).
	 *
	 * @param mixed $value Wert.
	 * @param array $config Feldkonfiguration.
	 * @return array{value_longtext: ?string, value_varchar: ?string, value_int: ?int, value_date: ?string}
	 */
	public function to_storage( $value, array $config = array() ): array;

	/**
	 * Baut aus der Speicherstruktur wieder den domänennahen Wert.
	 *
	 * @param array $stored Speicherstruktur.
	 * @param array $config Feldkonfiguration.
	 * @return mixed
	 */
	public function from_storage( array $stored, array $config = array() );
}

