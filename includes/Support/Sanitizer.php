<?php

namespace BS\ModularFramework\Support;

use BS\ModularFramework\Domain\FieldDefinition;
use BS\ModularFramework\Registry\FieldTypeRegistry;

defined( 'ABSPATH' ) || exit;

/**
 * Verantwortlich für Sanitization von Feldwerten.
 */
class Sanitizer {

	public function __construct(
		private FieldTypeRegistry $registry
	) {
	}

	/**
	 * Sanitized einen Wert und gibt die Speicherstruktur zurück.
	 *
	 * @param FieldDefinition $field Felddefinition.
	 * @param mixed           $value Wert.
	 * @return array{value_longtext: ?string, value_varchar: ?string, value_int: ?int, value_date: ?string}
	 */
	public function sanitize_for_storage( FieldDefinition $field, $value ): array {
		$type = $this->registry->get( $field->field_type );
		if ( ! $type ) {
			return array(
				'value_longtext' => null,
				'value_varchar'  => null,
				'value_int'      => null,
				'value_date'     => null,
			);
		}

		$config    = $field->config ?? array();
		$sanitized = $type->sanitize( $value, $config );

		return $type->to_storage( $sanitized, $config );
	}

	/**
	 * Baut aus einer Speicherstruktur wieder den domänennahen Wert.
	 *
	 * @param FieldDefinition $field Felddefinition.
	 * @param array           $stored Speicherstruktur.
	 * @return mixed
	 */
	public function value_from_storage( FieldDefinition $field, array $stored ) {
		$type = $this->registry->get( $field->field_type );
		if ( ! $type ) {
			return null;
		}

		$config = $field->config ?? array();

		return $type->from_storage( $stored, $config );
	}
}

