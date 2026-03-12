<?php

namespace BS\ModularFramework\Domain;

defined( 'ABSPATH' ) || exit;

/**
 * Repräsentiert einen Feldwert zu einem Eintrag und einer Felddefinition.
 */
class FieldValue {

	public function __construct(
		public readonly ?int $id,
		public int $entry_id,
		public int $field_id,
		public ?string $value_longtext,
		public ?string $value_varchar,
		public ?int $value_int,
		public ?string $value_date,
		public string $created_at,
		public string $updated_at
	) {
	}
}

