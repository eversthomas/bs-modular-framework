<?php

namespace BS\ModularFramework\Admin;

use BS\ModularFramework\Data\ModuleRepository;
use WP_List_Table;
use wpdb;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_List_Table', false ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Tabellenansicht für Module.
 */
class ModuleListTable extends WP_List_Table {

	/**
	 * @var ModuleRepository
	 */
	protected ModuleRepository $modules;

	/**
	 * @param ModuleRepository $modules Repository.
	 */
	public function __construct( ModuleRepository $modules ) {
		parent::__construct(
			array(
				'singular' => 'bs_mf_module',
				'plural'   => 'bs_mf_modules',
				'ajax'     => false,
			)
		);

		$this->modules = $modules;
	}

	/**
	 * Spalten definieren.
	 *
	 * @return array
	 */
	public function get_columns(): array {
		return array(
			'cb'          => '<input type="checkbox" />',
			'name'        => __( 'Name', 'bs-modular-framework' ),
			'slug'        => __( 'Slug', 'bs-modular-framework' ),
			'status'      => __( 'Status', 'bs-modular-framework' ),
			'sort_order'  => __( 'Sortierung', 'bs-modular-framework' ),
			'created_at'  => __( 'Erstellt', 'bs-modular-framework' ),
		);
	}

	/**
	 * Checkbox-Spalte.
	 *
	 * @param array $item Datensatz.
	 * @return string
	 */
	protected function column_cb( $item ): string {
		return sprintf(
			'<input type="checkbox" name="id[]" value="%d" />',
			(int) $item['id']
		);
	}

	/**
	 * Standardspaltendarstellung.
	 *
	 * @param array  $item  Datensatz.
	 * @param string $column_name Spaltenname.
	 * @return string
	 */
	public function column_default( $item, $column_name ): string {
		switch ( $column_name ) {
			case 'slug':
			case 'status':
			case 'sort_order':
			case 'created_at':
				return isset( $item[ $column_name ] ) ? esc_html( (string) $item[ $column_name ] ) : '';
		}

		return '';
	}

	/**
	 * Name-Spalte mit Aktionen.
	 *
	 * @param array $item Datensatz.
	 * @return string
	 */
	public function column_name( $item ): string {
		$id    = (int) $item['id'];
		$name  = esc_html( (string) $item['name'] );

		$edit_url = add_query_arg(
			array(
				'page'   => AdminMenu::MENU_SLUG_MODULES,
				'action' => 'edit',
				'id'     => $id,
			),
			admin_url( 'admin.php' )
		);

		$fields_url = add_query_arg(
			array(
				'page'      => 'bs-modular-framework-fields',
				'module_id' => $id,
			),
			admin_url( 'admin.php' )
		);

		$entries_url = add_query_arg(
			array(
				'page'      => 'bs-modular-framework-entries',
				'module_id' => $id,
			),
			admin_url( 'admin.php' )
		);

		$delete_url = wp_nonce_url(
			add_query_arg(
				array(
					'page'   => AdminMenu::MENU_SLUG_MODULES,
					'action' => 'delete',
					'id'     => $id,
				),
				admin_url( 'admin.php' )
			),
			'bs_mf_delete_module_' . $id
		);

		$actions = array(
			'edit'    => sprintf( '<a href="%s">%s</a>', esc_url( $edit_url ), esc_html__( 'Bearbeiten', 'bs-modular-framework' ) ),
			'fields'  => sprintf( '<a href="%s">%s</a>', esc_url( $fields_url ), esc_html__( 'Felder', 'bs-modular-framework' ) ),
			'entries' => sprintf( '<a href="%s">%s</a>', esc_url( $entries_url ), esc_html__( 'Einträge', 'bs-modular-framework' ) ),
			'delete'  => sprintf( '<a href="%s" onclick="return confirm(\'%s\');">%s</a>', esc_url( $delete_url ), esc_js( __( 'Modul wirklich löschen?', 'bs-modular-framework' ) ), esc_html__( 'Löschen', 'bs-modular-framework' ) ),
		);

		return sprintf(
			'<strong><a href="%1$s">%2$s</a></strong> %3$s',
			esc_url( $edit_url ),
			$name,
			$this->row_actions( $actions )
		);
	}

	/**
	 * Bereitet die Elemente für die Tabelle vor.
	 *
	 * @return void
	 */
	public function prepare_items(): void {
		global $wpdb;

		if ( ! $wpdb instanceof wpdb ) {
			$this->items = array();
			return;
		}

		$table_name = $wpdb->prefix . 'bs_mf_modules';

		$rows = $wpdb->get_results(
			"SELECT id, name, slug, status, sort_order, created_at FROM {$table_name} ORDER BY sort_order ASC, id ASC",
			ARRAY_A
		);

		$this->items = $rows ?: array();

		$this->_column_headers = array(
			$this->get_columns(),
			array(),
			array(),
		);
	}
}

