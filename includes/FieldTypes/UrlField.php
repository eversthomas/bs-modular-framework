<?php

namespace BS\ModularFramework\FieldTypes;

defined( 'ABSPATH' ) || exit;

class UrlField extends AbstractFieldType {

	public function get_key(): string {
		return 'url';
	}

	public function is_valid( $value, array $config = array() ): bool {
		if ( null === $value || '' === $value ) {
			return true;
		}

		return false !== filter_var( $value, FILTER_VALIDATE_URL );
	}

	public function sanitize( $value, array $config = array() ) {
		if ( null === $value || '' === $value ) {
			return null;
		}

		$sanitized = esc_url_raw( (string) $value );
		return '' === $sanitized ? null : $sanitized;
	}
}

