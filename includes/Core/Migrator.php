<?php

namespace BS\ModularFramework\Core;

use wpdb;

defined( 'ABSPATH' ) || exit;

/**
 * Verantwortlich für die Datenbankschemata des Plugins.
 */
class Migrator {

	/**
	 * Führt notwendige Migrationen aus (idempotent).
	 *
	 * @return void
	 */
	public static function migrate(): void {
		global $wpdb;

		if ( ! $wpdb instanceof wpdb ) {
			return;
		}

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();

		$modules_table      = $wpdb->prefix . 'bs_mf_modules';
		$fields_table       = $wpdb->prefix . 'bs_mf_fields';
		$entries_table      = $wpdb->prefix . 'bs_mf_entries';
		$field_values_table = $wpdb->prefix . 'bs_mf_field_values';

		$modules_sql = "CREATE TABLE {$modules_table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			name VARCHAR(191) NOT NULL,
			slug VARCHAR(191) NOT NULL,
			description TEXT NULL,
			status VARCHAR(50) NOT NULL DEFAULT 'active',
			sort_order INT NOT NULL DEFAULT 0,
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			UNIQUE KEY slug (slug)
		) {$charset_collate};";

		$fields_sql = "CREATE TABLE {$fields_table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			module_id BIGINT UNSIGNED NOT NULL,
			label VARCHAR(191) NOT NULL,
			field_key VARCHAR(191) NOT NULL,
			field_type VARCHAR(50) NOT NULL,
			is_required TINYINT(1) NOT NULL DEFAULT 0,
			config_json LONGTEXT NULL,
			sort_order INT NOT NULL DEFAULT 0,
			status VARCHAR(50) NOT NULL DEFAULT 'active',
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY module_id (module_id),
			UNIQUE KEY module_field_key (module_id, field_key)
		) {$charset_collate};";

		$entries_sql = "CREATE TABLE {$entries_table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			module_id BIGINT UNSIGNED NOT NULL,
			title VARCHAR(191) NOT NULL,
			status VARCHAR(50) NOT NULL DEFAULT 'draft',
			sort_order INT NOT NULL DEFAULT 0,
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY module_id (module_id),
			KEY status (status)
		) {$charset_collate};";

		$field_values_sql = "CREATE TABLE {$field_values_table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			entry_id BIGINT UNSIGNED NOT NULL,
			field_id BIGINT UNSIGNED NOT NULL,
			value_longtext LONGTEXT NULL,
			value_varchar VARCHAR(191) NULL,
			value_int BIGINT NULL,
			value_date DATE NULL,
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY entry_id (entry_id),
			KEY field_id (field_id)
		) {$charset_collate};";

		dbDelta( $modules_sql );
		dbDelta( $fields_sql );
		dbDelta( $entries_sql );
		dbDelta( $field_values_sql );
	}
}

