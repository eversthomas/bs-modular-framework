<?php

namespace BS\ModularFramework\FieldTypes;

defined( 'ABSPATH' ) || exit;

class TextField extends AbstractFieldType {

	public function get_key(): string {
		return 'text';
	}

	public function is_valid( $value, array $config = array() ): bool {
		return null === $value || is_scalar( $value );
	}

	public function sanitize( $value, array $config = array() ) {
		if ( null === $value ) {
			return null;
		}

		return sanitize_text_field( (string) $value );
	}
}

