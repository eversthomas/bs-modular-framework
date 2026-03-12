<?php

namespace BS\ModularFramework\FieldTypes;

defined( 'ABSPATH' ) || exit;

class DateField extends AbstractFieldType {

	public function get_key(): string {
		return 'date';
	}

	public function is_valid( $value, array $config = array() ): bool {
		if ( null === $value || '' === $value ) {
			return true;
		}

		$d = \DateTime::createFromFormat( 'Y-m-d', (string) $value );
		return $d && $d->format( 'Y-m-d' ) === (string) $value;
	}

	public function sanitize( $value, array $config = array() ) {
		if ( null === $value || '' === $value ) {
			return null;
		}

		$d = \DateTime::createFromFormat( 'Y-m-d', (string) $value );
		if ( ! $d ) {
			return null;
		}

		return $d->format( 'Y-m-d' );
	}

	public function to_storage( $value, array $config = array() ): array {
		$sanitized = $this->sanitize( $value, $config );

		return array(
			'value_longtext' => null,
			'value_varchar'  => null,
			'value_int'      => null,
			'value_date'     => $sanitized,
		);
	}

	public function from_storage( array $stored, array $config = array() ) {
		return $stored['value_date'] ?? null;
	}
}

