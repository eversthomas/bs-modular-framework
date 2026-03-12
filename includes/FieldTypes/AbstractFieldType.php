<?php

namespace BS\ModularFramework\FieldTypes;

defined( 'ABSPATH' ) || exit;

/**
 * Basisklasse für Feldtypen mit Standardimplementierungen.
 */
abstract class AbstractFieldType implements FieldTypeInterface {

	/**
	 * {@inheritdoc}
	 */
	public function to_storage( $value, array $config = array() ): array {
		// Standard: alles als varchar speichern.
		return array(
			'value_longtext' => null,
			'value_varchar'  => ( null === $value ) ? null : (string) $value,
			'value_int'      => null,
			'value_date'     => null,
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function from_storage( array $stored, array $config = array() ) {
		if ( isset( $stored['value_varchar'] ) ) {
			return $stored['value_varchar'];
		}

		return null;
	}
}

