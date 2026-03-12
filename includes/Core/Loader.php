<?php

namespace BS\ModularFramework\Core;

defined( 'ABSPATH' ) || exit;

/**
 * Verwaltet das Registrieren von Actions und Filtern.
 */
class Loader {

	/**
	 * Gesammelte Hooks.
	 *
	 * @var array<int, array<string, mixed>>
	 */
	protected array $hooks = array();

	/**
	 * Registriert eine Action.
	 *
	 * @param string   $hook_name Hook-Name.
	 * @param object   $component Objekt.
	 * @param string   $callback  Methodenname.
	 * @param int      $priority  Priorität.
	 * @param int      $accepted_args Anzahl Argumente.
	 *
	 * @return void
	 */
	public function add_action( string $hook_name, object $component, string $callback, int $priority = 10, int $accepted_args = 1 ): void {
		$this->hooks[] = array(
			'type'          => 'action',
			'hook'          => $hook_name,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);
	}

	/**
	 * Registriert einen Filter.
	 *
	 * @param string   $hook_name Hook-Name.
	 * @param object   $component Objekt.
	 * @param string   $callback  Methodenname.
	 * @param int      $priority  Priorität.
	 * @param int      $accepted_args Anzahl Argumente.
	 *
	 * @return void
	 */
	public function add_filter( string $hook_name, object $component, string $callback, int $priority = 10, int $accepted_args = 1 ): void {
		$this->hooks[] = array(
			'type'          => 'filter',
			'hook'          => $hook_name,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);
	}

	/**
	 * Registriert alle gespeicherten Hooks bei WordPress.
	 *
	 * @return void
	 */
	public function run(): void {
		foreach ( $this->hooks as $hook ) {
			if ( 'action' === $hook['type'] ) {
				add_action(
					$hook['hook'],
					array( $hook['component'], $hook['callback'] ),
					$hook['priority'],
					$hook['accepted_args']
				);
			} elseif ( 'filter' === $hook['type'] ) {
				add_filter(
					$hook['hook'],
					array( $hook['component'], $hook['callback'] ),
					$hook['priority'],
					$hook['accepted_args']
				);
			}
		}
	}
}

