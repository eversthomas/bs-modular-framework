<?php

namespace BS\ModularFramework\Core;

defined( 'ABSPATH' ) || exit;

/**
 * Definiert Plugin-spezifische Capabilities.
 */
class Capabilities {

	/**
	 * Capability für Modulverwaltung.
	 *
	 * @return string
	 */
	public static function manage_modules(): string {
		// MVP: an Administratoren koppeln.
		return 'manage_options';
	}

	/**
	 * Capability für Eintragsverwaltung.
	 *
	 * @return string
	 */
	public static function manage_entries(): string {
		// MVP: ebenfalls Administratoren (später feiner granulieren).
		return 'manage_options';
	}

	/**
	 * Capability für reine Redaktion (später ausbaubar).
	 *
	 * @return string
	 */
	public static function edit_entries(): string {
		// MVP: Redaktionsrechte können später separat gemappt werden.
		return 'edit_posts';
	}
}

