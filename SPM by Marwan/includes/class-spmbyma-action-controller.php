<?php
/**
 * Safe Actions Framework Controller for Smart Plugin Monitor.
 *
 * Handles administrative actions such as enabling/disabling plugins
 * and performing scans. Ensures capability checks, nonce validation,
 * and comprehensive audit logging.
 *
 * @package SmartPluginMonitor
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SPMBYMA_Action_Controller {

    private SPMBYMA_Database $database;

    public function __construct( SPMBYMA_Database $database ) {
        $this->database = $database;
    }

    /**
     * Disable a plugin securely.
     */
    public function disable_plugin( string $basename ): array {
        $this->check_permissions();

        if ( ! function_exists( 'deactivate_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        if ( ! is_plugin_active( $basename ) ) {
            return [ 'success' => false, 'message' => __( 'Plugin is already inactive.', 'spm-by-marwan' ) ];
        }

        // Prevent suicide
        if ( plugin_basename( SPMBYMA_PLUGIN_FILE ) === $basename ) {
             $this->database->log_action( $basename, 'disable', 'failed', 'Self-deactivation attempt blocked.' );
             return [ 'success' => false, 'message' => __( 'Cannot disable the monitor itself.', 'spm-by-marwan' ) ];
        }

        deactivate_plugins( $basename );
        
        $this->database->log_action( $basename, 'disable', 'success' );

        return [ 'success' => true, 'message' => __( 'Plugin deactivated successfully.', 'spm-by-marwan' ) ];
    }

    /**
     * Isolate a plugin for temporary testing.
     */
    public function isolate_plugin( string $basename ): array {
        $this->check_permissions();

        if ( ! function_exists( 'deactivate_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        if ( ! is_plugin_active( $basename ) ) {
            return [ 'success' => false, 'message' => __( 'Plugin must be active to be isolated.', 'spm-by-marwan' ) ];
        }

        // Save state before deactivating
        update_option( 'spmbyma_isolation_target', $basename );
        update_option( 'spmbyma_isolation_timestamp', time() );

        deactivate_plugins( $basename );
        
        $this->database->log_action( $basename, 'isolate', 'success', 'Plugin isolated for performance testing.' );

        return [ 'success' => true, 'message' => __( 'Plugin isolated. Run your tests now.', 'spm-by-marwan' ) ];
    }

    /**
     * Restore the site from isolation mode.
     */
    public function restore_state(): array {
        $this->check_permissions();

        $target = get_option( 'spmbyma_isolation_target' );
        if ( ! $target ) {
            return [ 'success' => false, 'message' => __( 'No active isolation found.', 'spm-by-marwan' ) ];
        }

        if ( ! function_exists( 'activate_plugin' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $result = activate_plugin( $target );
        
        delete_option( 'spmbyma_isolation_target' );
        delete_option( 'spmbyma_isolation_timestamp' );

        if ( is_wp_error( $result ) ) {
            $this->database->log_action( $target, 'restore', 'failed', $result->get_error_message() );
            return [ 'success' => false, 'message' => $result->get_error_message() ];
        }

        $this->database->log_action( $target, 'restore', 'success' );

        return [ 'success' => true, 'message' => __( 'Isolation ended. Plugin restored.', 'spm-by-marwan' ) ];
    }

    /**
     * Enable a plugin securely.
     */
    public function enable_plugin( string $basename ): array {
        $this->check_permissions();

        if ( ! function_exists( 'activate_plugin' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        if ( is_plugin_active( $basename ) ) {
            return [ 'success' => false, 'message' => __( 'Plugin is already active.', 'spm-by-marwan' ) ];
        }

        $result = activate_plugin( $basename );
        
        if ( is_wp_error( $result ) ) {
            $this->database->log_action( $basename, 'enable', 'failed', $result->get_error_message() );
            return [ 'success' => false, 'message' => $result->get_error_message() ];
        }

        $this->database->log_action( $basename, 'enable', 'success' );

        return [ 'success' => true, 'message' => __( 'Plugin activated successfully.', 'spm-by-marwan' ) ];
    }

    /**
     * Log a manual scan action.
     */
    public function log_scan( string $basename, string $type = 'quick' ): void {
        $this->database->log_action( $basename, "scan_{$type}", 'success' );
    }

    /**
     * Centralized permission check.
     */
    private function check_permissions(): void {
        if ( ! current_user_can( 'activate_plugins' ) ) {
            wp_die( esc_html__( 'Unauthorized: You do not have permission to manage plugins.', 'spm-by-marwan' ) );
        }
    }
}
