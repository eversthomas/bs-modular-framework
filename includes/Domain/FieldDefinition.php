<?php

namespace BS\ModularFramework\Domain;

defined( 'ABSPATH' ) || exit;

/**
 * Repräsentiert eine Felddefinition innerhalb eines Moduls.
 */
class FieldDefinition {

	public function __construct(
		public readonly ?int $id,
		public int $module_id,
		public string $label,
		public string $field_key,
		public string $field_type,
		public bool $is_required,
		public ?array $config,
		public int $sort_order,
		public string $status,
		public string $created_at,
		public string $updated_at
	) {
	}
}

