<?php

namespace BS\ModularFramework\Admin;

use BS\ModularFramework\Data\EntryRepository;
use BS\ModularFramework\Domain\Module;
use WP_List_Table;
use wpdb;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_List_Table', false ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Tabellenansicht für Einträge eines Moduls.
 */
class EntryListTable extends WP_List_Table {

	protected EntryRepository $entries;
	protected Module $module;

	public function __construct( EntryRepository $entries, Module $module ) {
		parent::__construct(
			array(
				'singular' => 'bs_mf_entry',
				'plural'   => 'bs_mf_entries',
				'ajax'     => false,
			)
		);

		$this->entries = $entries;
		$this->module  = $module;
	}

	public function get_columns(): array {
		return array(
			'cb'         => '<input type="checkbox" />',
			'title'      => __( 'Titel', 'bs-modular-framework' ),
			'status'     => __( 'Status', 'bs-modular-framework' ),
			'updated_at' => __( 'Geändert am', 'bs-modular-framework' ),
		);
	}

	protected function column_cb( $item ): string {
		return sprintf(
			'<input type="checkbox" name="id[]" value="%d" />',
			(int) $item['id']
		);
	}

	public function column_default( $item, $column_name ): string {
		switch ( $column_name ) {
			case 'status':
			case 'updated_at':
				return isset( $item[ $column_name ] ) ? esc_html( (string) $item[ $column_name ] ) : '';
		}

		return '';
	}

	public function column_title( $item ): string {
		$id    = (int) $item['id'];
		$title = esc_html( (string) $item['title'] );

		$edit_url = add_query_arg(
			array(
				'page'      => 'bs-modular-framework-entries',
				'action'    => 'edit',
				'module_id' => $this->module->id,
				'id'        => $id,
			),
			admin_url( 'admin.php' )
		);

		$delete_url = wp_nonce_url(
			add_query_arg(
				array(
					'page'      => 'bs-modular-framework-entries',
					'action'    => 'delete',
					'module_id' => $this->module->id,
					'id'        => $id,
				),
				admin_url( 'admin.php' )
			),
			'bs_mf_delete_entry_' . $id
		);

		$actions = array(
			'edit'   => sprintf( '<a href="%s">%s</a>', esc_url( $edit_url ), esc_html__( 'Bearbeiten', 'bs-modular-framework' ) ),
			'delete' => sprintf( '<a href="%s" onclick="return confirm(\'%s\');">%s</a>', esc_url( $delete_url ), esc_js( __( 'Eintrag wirklich löschen?', 'bs-modular-framework' ) ), esc_html__( 'Löschen', 'bs-modular-framework' ) ),
		);

		return sprintf(
			'<strong><a href="%1$s">%2$s</a></strong> %3$s',
			esc_url( $edit_url ),
			$title,
			$this->row_actions( $actions )
		);
	}

	public function prepare_items(): void {
		global $wpdb;

		if ( ! $wpdb instanceof wpdb ) {
			$this->items = array();
			return;
		}

		$table_name = $wpdb->prefix . 'bs_mf_entries';

		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT id, title, status, updated_at FROM {$table_name} WHERE module_id = %d ORDER BY sort_order ASC, id ASC",
				$this->module->id
			),
			ARRAY_A
		); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		$this->items = $rows ?: array();

		$this->_column_headers = array(
			$this->get_columns(),
			array(),
			array(),
		);
	}
}

