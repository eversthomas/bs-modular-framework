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
		return 'manage_bs_mf_modules';
	}

	/**
	 * Capability für Eintragsverwaltung.
	 *
	 * @return string
	 */
	public static function manage_entries(): string {
		return 'manage_bs_mf_entries';
	}

	/**
	 * Capability für reine Redaktion (später ausbaubar).
	 *
	 * @return string
	 */
	public static function edit_entries(): string {
		return 'edit_bs_mf_entries';
	}
}

