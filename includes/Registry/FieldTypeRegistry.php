<?php

namespace BS\ModularFramework\Registry;

use BS\ModularFramework\FieldTypes\FieldTypeInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Registry für Feldtypen.
 */
class FieldTypeRegistry {

	/**
	 * @var array<string, FieldTypeInterface>
	 */
	private array $types = array();

	/**
	 * Registriert einen Feldtyp.
	 *
	 * @param FieldTypeInterface $type Feldtyp.
	 * @return void
	 */
	public function register( FieldTypeInterface $type ): void {
		$this->types[ $type->get_key() ] = $type;
	}

	/**
	 * Holt einen Feldtyp anhand seines Keys.
	 *
	 * @param string $key Key.
	 * @return FieldTypeInterface|null
	 */
	public function get( string $key ): ?FieldTypeInterface {
		return $this->types[ $key ] ?? null;
	}

	/**
	 * Gibt alle registrierten Feldtypen zurück.
	 *
	 * @return array<string, FieldTypeInterface>
	 */
	public function all(): array {
		return $this->types;
	}
}

