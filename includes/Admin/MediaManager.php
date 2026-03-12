<?php

namespace BS\ModularFramework\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Kümmert sich um das Laden der Assets für das Bildfeld.
 */
class MediaManager {

	/**
	 * Registriert Hooks zum Laden der Assets.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Lädt Media-Assets auf unseren Plugin-Seiten.
	 *
	 * @param string $hook Aktueller Hook.
	 * @return void
	 */
	public function enqueue_assets( string $hook ): void {
		// Nur auf unseren Seiten laden.
		if ( false === strpos( $hook, 'bs-modular-framework' ) ) {
			return;
		}

		wp_enqueue_media();

		wp_enqueue_script(
			'bs-mF-media-manager',
			plugins_url( 'admin/js/media-manager.js', dirname( __DIR__, 1 ) . '/bs-modular-framework.php' ),
			array( 'jquery' ),
			false,
			true
		);
	}
}

