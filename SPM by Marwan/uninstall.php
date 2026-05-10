<?php
/**
 * Uninstall handler for Smart Plugin Monitor.
 *
 * Fired when the plugin is deleted via the WordPress admin.
 * Drops the custom table and removes all stored options.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

require_once plugin_dir_path( __FILE__ ) . 'includes/class-spmbyma-database.php';

// Define constant required by the database class.
if ( ! defined( 'SPMBYMA_DB_TABLE' ) ) {
    define( 'SPMBYMA_DB_TABLE', 'spmbyma_plugin_logs' );
}

if ( ! defined( 'SPMBYMA_VERSION' ) ) {
    define( 'SPMBYMA_VERSION', '1.2.0' );
}

SPMBYMA_Database::drop_table();
