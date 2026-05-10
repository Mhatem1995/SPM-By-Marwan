<?php
/**
 * Controller for the Plugin Details page.
 *
 * Handles data fetching and rendering for the dedicated plugin diagnostic page.
 *
 * @package SmartPluginMonitor
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SPMBYMA_Details_Controller {

    /**
     * Detail provider instance.
     *
     * @var SPMBYMA_Detail_Provider
     */
    private SPMBYMA_Detail_Provider $detail_provider;

    /**
     * Constructor.
     *
     * @param SPMBYMA_Detail_Provider $detail_provider Detail provider service.
     */
    public function __construct( SPMBYMA_Detail_Provider $detail_provider ) {
        $this->detail_provider = $detail_provider;
    }

    /**
     * Render the details page.
     */
    public function render_page(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized access.', 'smart-performance-monitor' ) );
        }

        // 1. Get and sanitize plugin slug (basename).
        $plugin_slug = isset( $_GET['plugin'] ) ? sanitize_text_field( wp_unslash( $_GET['plugin'] ) ) : '';

        if ( empty( $plugin_slug ) ) {
            wp_die( esc_html__( 'No plugin specified.', 'smart-performance-monitor' ) );
        }

        // 2. Fetch plugin data.
        $data = $this->detail_provider->get_detail( $plugin_slug );

        if ( ! $data ) {
            wp_die( esc_html__( 'Invalid plugin specified or plugin not found.', 'smart-performance-monitor' ) );
        }

        // 3. Render the template.
        include SPMBYMA_PLUGIN_DIR . 'templates/details-page.php';
    }
}
