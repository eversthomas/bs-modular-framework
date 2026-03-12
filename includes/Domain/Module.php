<?php

namespace BS\ModularFramework\Domain;

defined( 'ABSPATH' ) || exit;

/**
 * Repräsentiert ein Modul (Datentyp).
 */
class Module {

	public function __construct(
		public readonly ?int $id,
		public string $name,
		public string $slug,
		public ?string $description,
		public string $status,
		public int $sort_order,
		public string $created_at,
		public string $updated_at
	) {
	}
}

