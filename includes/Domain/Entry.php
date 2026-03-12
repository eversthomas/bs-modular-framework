<?php

namespace BS\ModularFramework\Domain;

defined( 'ABSPATH' ) || exit;

/**
 * Repräsentiert einen Eintrag zu einem Modul.
 */
class Entry {

	public function __construct(
		public readonly ?int $id,
		public int $module_id,
		public string $title,
		public string $status,
		public int $sort_order,
		public string $created_at,
		public string $updated_at
	) {
	}
}

