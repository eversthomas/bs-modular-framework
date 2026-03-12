<?php

namespace BS\ModularFramework\Core;

defined( 'ABSPATH' ) || exit;

/**
 * Aktivierungslogik des Plugins.
 */
class Activator {

	/**
	 * Wird bei Plugin-Aktivierung aufgerufen.
	 *
	 * @return void
	 */
	public static function activate(): void {
		Migrator::migrate();

		update_option( Plugin::OPTION_DB_VERSION, Plugin::DB_VERSION );
	}
}

