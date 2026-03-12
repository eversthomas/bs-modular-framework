<?php

namespace BS\ModularFramework\FieldTypes;

defined( 'ABSPATH' ) || exit;

class EmailField extends AbstractFieldType {

	public function get_key(): string {
		return 'email';
	}

	public function is_valid( $value, array $config = array() ): bool {
		if ( null === $value || '' === $value ) {
			return true;
		}

		return false !== filter_var( $value, FILTER_VALIDATE_EMAIL );
	}

	public function sanitize( $value, array $config = array() ) {
		if ( null === $value || '' === $value ) {
			return null;
		}

		$sanitized = sanitize_email( (string) $value );
		return '' === $sanitized ? null : $sanitized;
	}
}

