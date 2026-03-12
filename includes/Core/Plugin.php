<?php

namespace BS\ModularFramework\Core;

use BS\ModularFramework\Admin\AdminMenu;

defined( 'ABSPATH' ) || exit;

/**
 * Zentrale Plugin-Klasse.
 */
class Plugin {

	public const VERSION     = '0.1.0';
	public const DB_VERSION  = '0.1.0';
	public const OPTION_DB_VERSION = 'bs_mf_db_version';

	/**
	 * Loader-Instanz.
	 *
	 * @var Loader
	 */
	protected Loader $loader;

	/**
	 * Initialisiert das Plugin.
	 */
	public function __construct() {
		$this->loader = new Loader();
		$this->register_hooks();
	}

	/**
	 * Registriert alle globalen Hooks.
	 *
	 * @return void
	 */
	protected function register_hooks(): void {
		$this->loader->add_action( 'admin_menu', $this, 'register_admin_menu' );
	}

	/**
	 * Führt den Loader aus.
	 *
	 * @return void
	 */
	public function run(): void {
		$this->loader->run();
	}

	/**
	 * Registriert die Basisstruktur für das Admin-Menü.
	 *
	 * @return void
	 */
	public function register_admin_menu(): void {
		$menu = new AdminMenu();
		$menu->register();
	}
}

