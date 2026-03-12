<?php

namespace BS\ModularFramework\Core;

defined( 'ABSPATH' ) || exit;

/**
 * Deaktivierungslogik des Plugins.
 */
class Deactivator {

	/**
	 * Wird bei Plugin-Deaktivierung aufgerufen.
	 *
	 * @return void
	 */
	public static function deactivate(): void {
		// Reserviert für spätere Logik (z. B. Cronjobs entfernen).
	}
}

