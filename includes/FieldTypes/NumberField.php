<?php

namespace BS\ModularFramework\FieldTypes;

defined( 'ABSPATH' ) || exit;

class NumberField extends AbstractFieldType {

	public function get_key(): string {
		return 'number';
	}

	public function is_valid( $value, array $config = array() ): bool {
		if ( null === $value || '' === $value ) {
			return true;
		}

		return is_numeric( $value );
	}

	public function sanitize( $value, array $config = array() ) {
		if ( null === $value || '' === $value ) {
			return null;
		}

		return (int) $value;
	}

	public function to_storage( $value, array $config = array() ): array {
		if ( null === $value || '' === $value ) {
			return array(
				'value_longtext' => null,
				'value_varchar'  => null,
				'value_int'      => null,
				'value_date'     => null,
			);
		}

		return array(
			'value_longtext' => null,
			'value_varchar'  => null,
			'value_int'      => (int) $value,
			'value_date'     => null,
		);
	}

	public function from_storage( array $stored, array $config = array() ) {
		return isset( $stored['value_int'] ) ? (int) $stored['value_int'] : null;
	}
}

