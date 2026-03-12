<?php

namespace BS\ModularFramework\FieldTypes;

defined( 'ABSPATH' ) || exit;

class CheckboxField extends AbstractFieldType {

	public function get_key(): string {
		return 'checkbox';
	}

	public function is_valid( $value, array $config = array() ): bool {
		return null === $value || is_bool( $value ) || $value === '0' || $value === '1' || $value === 0 || $value === 1;
	}

	public function sanitize( $value, array $config = array() ) {
		return (bool) $value;
	}

	public function to_storage( $value, array $config = array() ): array {
		$bool = (bool) $value;

		return array(
			'value_longtext' => null,
			'value_varchar'  => null,
			'value_int'      => $bool ? 1 : 0,
			'value_date'     => null,
		);
	}

	public function from_storage( array $stored, array $config = array() ) {
		return ! empty( $stored['value_int'] );
	}
}

