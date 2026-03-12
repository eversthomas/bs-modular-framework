<?php

namespace BS\ModularFramework\FieldTypes;

defined( 'ABSPATH' ) || exit;

class SelectField extends AbstractFieldType {

	public function get_key(): string {
		return 'select';
	}

	/**
	 * Erlaubte Optionen aus der Konfiguration holen.
	 *
	 * @param array $config Konfiguration.
	 * @return string[]
	 */
	protected function get_allowed_options( array $config ): array {
		$options = $config['options'] ?? array();
		if ( ! is_array( $options ) ) {
			return array();
		}

		return array_values(
			array_map(
				static function ( $value ): string {
					return (string) $value;
				},
				$options
			)
		);
	}

	public function is_valid( $value, array $config = array() ): bool {
		if ( null === $value || '' === $value ) {
			return true;
		}

		$allowed = $this->get_allowed_options( $config );
		return in_array( (string) $value, $allowed, true );
	}

	public function sanitize( $value, array $config = array() ) {
		if ( null === $value || '' === $value ) {
			return null;
		}

		$allowed = $this->get_allowed_options( $config );
		$value   = (string) $value;

		return in_array( $value, $allowed, true ) ? $value : null;
	}
}

