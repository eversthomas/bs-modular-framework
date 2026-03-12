<?php
/**
 * Uninstall-Routine für BS_Modular-Framework.
 *
 * Entfernt Plugin-Daten aus der Datenbank.
 *
 * @package BS_ModularFramework
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;
defined( 'ABSPATH' ) || exit;

global $wpdb;

if ( ! $wpdb instanceof wpdb ) {
	return;
}

$modules_table      = $wpdb->prefix . 'bs_mf_modules';
$fields_table       = $wpdb->prefix . 'bs_mf_fields';
$entries_table      = $wpdb->prefix . 'bs_mf_entries';
$field_values_table = $wpdb->prefix . 'bs_mf_field_values';

// Tabellen entfernen.
$wpdb->query( "DROP TABLE IF EXISTS {$modules_table}" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
$wpdb->query( "DROP TABLE IF EXISTS {$fields_table}" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
$wpdb->query( "DROP TABLE IF EXISTS {$entries_table}" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
$wpdb->query( "DROP TABLE IF EXISTS {$field_values_table}" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

// Options entfernen.
delete_option( 'bs_mf_db_version' );

