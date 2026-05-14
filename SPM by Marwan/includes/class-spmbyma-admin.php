<?php
/**
 * Admin UI for Smart Plugin Monitor.
 *
 * Registers a top-level admin menu page and delegates rendering
 * to the modular SPMBYMA_Dashboard class. Enqueues CSS and JS assets.
 *
 * @package SmartPluginMonitor
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SPMBYMA_Admin {

    private SPMBYMA_Data_Service $data_service;
    private SPMBYMA_Detail_Provider $detail_provider;
    private ?SPMBYMA_Dashboard $dashboard = null;
    private ?SPMBYMA_Details_Controller $details_controller = null;

    public function __construct( SPMBYMA_Data_Service $data_service, SPMBYMA_Detail_Provider $detail_provider ) {
        $this->data_service   = $data_service;
        $this->detail_provider = $detail_provider;
        add_action( 'admin_menu', [ $this, 'register_menu' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        
        // Explicitly set the global title for the hidden page to prevent strip_tags(null) warning in admin-header.php
        add_action( 'load-admin_page_plugin-monitor-details', function() {
            global $title;
            $title = __( 'Plugin Details', 'spm-by-marwan' );
        });
    }

    /**
     * Register a top-level admin menu page.
     */
    public function register_menu(): void {
        add_menu_page(
            __( 'Plugin Monitor', 'spm-by-marwan' ),
            __( 'Plugin Monitor', 'spm-by-marwan' ),
            'manage_options',
            'spm-dashboard',
            [ $this, 'render_page' ],
            'dashicons-chart-bar',
            80
        );

        // Hidden Details Page
        add_submenu_page(
            'spm-hidden',
            __( 'Plugin Details', 'spm-by-marwan' ),
            __( 'Plugin Details', 'spm-by-marwan' ),
            'manage_options',
            'plugin-monitor-details',
            [ $this, 'render_details_page' ]
        );
    }

    /**
     * Enqueue admin CSS and JS on our dashboard page only.
     */
    public function enqueue_assets( string $hook ): void {
        $allowed_hooks = [
            'toplevel_page_spm-dashboard',
            'admin_page_plugin-monitor-details'
        ];

        if ( ! in_array( $hook, $allowed_hooks, true ) ) {
            return;
        }

        wp_enqueue_style(
            'spmbyma-admin',
            SPMBYMA_PLUGIN_URL . 'assets/css/admin.css',
            [],
            SPMBYMA_VERSION
        );

        wp_enqueue_script(
            'spmbyma-dashboard',
            SPMBYMA_PLUGIN_URL . 'assets/js/dashboard.js',
            [],
            SPMBYMA_VERSION,
            true
        );

        wp_localize_script( 'spmbyma-dashboard', 'spmbymaConfig', [
            'restUrl' => esc_url_raw( rest_url( 'spmbyma/v1' ) ),
            'nonce'   => wp_create_nonce( 'wp_rest' ),
        ] );
    }

    /**
     * Render the dashboard page via the dashboard class.
     */
    public function render_page(): void {
        // Handle Print View
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only navigation parameter.
        $spmbyma_view = isset( $_GET['spmbyma_view'] ) ? sanitize_text_field( wp_unslash( $_GET['spmbyma_view'] ) ) : '';
        if ( 'print' === $spmbyma_view ) {
            $this->render_print_view();
            return;
        }

        if ( null === $this->dashboard ) {
            $this->dashboard = new SPMBYMA_Dashboard( $this->data_service );
        }

        $this->dashboard->render();
    }

    /**
     * Render a clean, printable report view.
     */
    private function render_print_view(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized.', 'spm-by-marwan' ) );
        }

        $snap = $this->data_service->get_dashboard_snapshot( 30 );
        
        include SPMBYMA_PLUGIN_DIR . 'templates/print-report.php';
        exit;
    }

    /**
     * Render the hidden details page.
     */
    public function render_details_page(): void {
        if ( null === $this->details_controller ) {
            $this->details_controller = new SPMBYMA_Details_Controller( $this->detail_provider );
        }

        $this->details_controller->render_page();
    }
}
