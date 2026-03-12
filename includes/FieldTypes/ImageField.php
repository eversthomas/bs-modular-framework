<?php

namespace BS\ModularFramework\FieldTypes;

defined( 'ABSPATH' ) || exit;

class ImageField extends AbstractFieldType {

	public function get_key(): string {
		return 'image';
	}

	public function is_valid( $value, array $config = array() ): bool {
		if ( null === $value || '' === $value ) {
			return true;
		}

		return is_numeric( $value ) && (int) $value >= 0;
	}

	public function sanitize( $value, array $config = array() ) {
		if ( null === $value || '' === $value ) {
			return null;
		}

		$id = (int) $value;
		return $id > 0 ? $id : null;
	}

	public function to_storage( $value, array $config = array() ): array {
		$sanitized = $this->sanitize( $value, $config );

		return array(
			'value_longtext' => null,
			'value_varchar'  => null,
			'value_int'      => $sanitized,
			'value_date'     => null,
		);
	}

	public function from_storage( array $stored, array $config = array() ) {
		if ( empty( $stored['value_int'] ) ) {
			return null;
		}

		return (int) $stored['value_int'];
	}
}

