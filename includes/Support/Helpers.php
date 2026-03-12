<?php

namespace BS\ModularFramework\Support;

defined( 'ABSPATH' ) || exit;

/**
 * Sammlung kleiner Hilfsfunktionen.
 */
class Helpers {

	/**
	 * Liefert das aktuelle Datum/Zeit-Format für DB-Speicherung.
	 *
	 * @return string
	 */
	public static function now(): string {
		return current_time( 'mysql', true );
	}
}

