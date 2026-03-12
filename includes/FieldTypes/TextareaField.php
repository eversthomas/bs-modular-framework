<?php

namespace BS\ModularFramework\FieldTypes;

defined( 'ABSPATH' ) || exit;

class TextareaField extends AbstractFieldType {

	public function get_key(): string {
		return 'textarea';
	}

	public function is_valid( $value, array $config = array() ): bool {
		return null === $value || is_scalar( $value );
	}

	public function sanitize( $value, array $config = array() ) {
		if ( null === $value ) {
			return null;
		}

		// Mehrzeiliger, aber sicherer Text – kein freies HTML.
		$text = wp_check_invalid_utf8( (string) $value );
		$text = wp_strip_all_tags( $text );
		return $text;
	}

	public function to_storage( $value, array $config = array() ): array {
		return array(
			'value_longtext' => ( null === $value ) ? null : (string) $value,
			'value_varchar'  => null,
			'value_int'      => null,
			'value_date'     => null,
		);
	}

	public function from_storage( array $stored, array $config = array() ) {
		return $stored['value_longtext'] ?? null;
	}
}

