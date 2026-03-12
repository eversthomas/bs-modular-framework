<?php

namespace BS\ModularFramework\Support;

use BS\ModularFramework\Domain\FieldDefinition;
use BS\ModularFramework\Registry\FieldTypeRegistry;

defined( 'ABSPATH' ) || exit;

/**
 * Verantwortlich für Validierungslogik von Feldwerten.
 */
class Validator {

	public function __construct(
		private FieldTypeRegistry $registry
	) {
	}

	/**
	 * Validiert einen Wert gegen eine Felddefinition.
	 *
	 * @param FieldDefinition $field Felddefinition.
	 * @param mixed           $value Wert.
	 * @return true|string true bei Erfolg, ansonsten Fehlermeldung.
	 */
	public function validate_field( FieldDefinition $field, $value ) {
		if ( $field->is_required && ( null === $value || '' === $value ) ) {
			return __( 'Dieses Feld ist erforderlich.', 'bs-modular-framework' );
		}

		$type = $this->registry->get( $field->field_type );
		if ( ! $type ) {
			return __( 'Unbekannter Feldtyp.', 'bs-modular-framework' );
		}

		$config = $field->config ?? array();

		if ( ! $type->is_valid( $value, $config ) ) {
			return __( 'Der eingegebene Wert ist ungültig.', 'bs-modular-framework' );
		}

		return true;
	}
}

