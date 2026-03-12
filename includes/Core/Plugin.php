<?php

namespace BS\ModularFramework\Core;

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
		if ( ! current_user_can( Capabilities::manage_modules() ) ) {
			return;
		}

		add_menu_page(
			__( 'Modular Framework', 'bs-modular-framework' ),
			__( 'Modular Framework', 'bs-modular-framework' ),
			Capabilities::manage_modules(),
			'bs-modular-framework',
			array( $this, 'render_stub_admin_page' ),
			'dashicons-index-card',
			60
		);
	}

	/**
	 * Einfache Platzhalter-Seite für das Admin-Menü.
	 *
	 * @return void
	 */
	public function render_stub_admin_page(): void {
		if ( ! current_user_can( Capabilities::manage_modules() ) ) {
			wp_die( esc_html__( 'Du hast keine Berechtigung, diese Seite zu sehen.', 'bs-modular-framework' ) );
		}

		echo '<div class="wrap">';
		echo '<h1>' . esc_html__( 'BS_Modular-Framework', 'bs-modular-framework' ) . '</h1>';
		echo '<p>' . esc_html__( 'Grundgerüst des Plugins ist aktiv. Modul- und Feldverwaltung folgen in späteren Phasen.', 'bs-modular-framework' ) . '</p>';
		echo '</div>';
	}
}

