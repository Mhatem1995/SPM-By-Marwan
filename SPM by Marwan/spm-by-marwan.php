<?php
/**
 * Plugin Name: SPM by Marwan
 * Support URI: https://ko-fi.com/marwanhatem31477
 * Description: Tracks load time and PHP errors for each active plugin, storing logs in a custom database table.
 * Version:     1.2.0
 * Author:      Marwan hatem
 * Author URI: https://github.com/Mhatem1995/SPM-By-Marwan
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: spm-by-marwan
 * Requires at least: 5.6
 * Requires PHP: 7.4
 */

// Prevent direct file access.
if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants.
define('SPMBYMA_VERSION', '1.2.0');
define('SPMBYMA_PLUGIN_FILE', __FILE__);
define('SPMBYMA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SPMBYMA_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SPMBYMA_DB_TABLE', 'spmbyma_plugin_logs');

// Autoload plugin classes.
require_once SPMBYMA_PLUGIN_DIR . 'includes/class-spmbyma-database.php';
require_once SPMBYMA_PLUGIN_DIR . 'includes/class-spmbyma-error-handler.php';
require_once SPMBYMA_PLUGIN_DIR . 'includes/class-spmbyma-monitor.php';
require_once SPMBYMA_PLUGIN_DIR . 'includes/class-spmbyma-analyzer.php';
require_once SPMBYMA_PLUGIN_DIR . 'includes/class-spmbyma-performance-analyzer.php';
require_once SPMBYMA_PLUGIN_DIR . 'includes/class-spmbyma-license-detector.php';
require_once SPMBYMA_PLUGIN_DIR . 'includes/class-spmbyma-security-scanner.php';
require_once SPMBYMA_PLUGIN_DIR . 'includes/class-spmbyma-data-service.php';
require_once SPMBYMA_PLUGIN_DIR . 'includes/class-spmbyma-action-controller.php';
require_once SPMBYMA_PLUGIN_DIR . 'includes/class-spmbyma-rollback-analyzer.php';
require_once SPMBYMA_PLUGIN_DIR . 'includes/class-spmbyma-export-service.php';
require_once SPMBYMA_PLUGIN_DIR . 'includes/class-spmbyma-detail-provider.php';
require_once SPMBYMA_PLUGIN_DIR . 'includes/class-spmbyma-rest-controller.php';
require_once SPMBYMA_PLUGIN_DIR . 'includes/class-spmbyma-admin.php';
require_once SPMBYMA_PLUGIN_DIR . 'includes/class-spmbyma-dashboard.php';
require_once SPMBYMA_PLUGIN_DIR . 'includes/class-spmbyma-details-controller.php';

/**
 * Run on plugin activation — create the database table.
 */
function spmbyma_activate()
{
    SPMBYMA_Database::create_table();
}
register_activation_hook(__FILE__, 'spmbyma_activate');

/**
 * Bootstrap the plugin after all plugins have loaded.
 *
 * We use a singleton pattern so the monitor only initialises once.
 */
function spmbyma_init()
{
    static $initialised = false;
    if ($initialised) {
        return;
    }
    $initialised = true;

    $database = new SPMBYMA_Database();
    $database->maybe_update_schema();
    $error_handler = new SPMBYMA_Error_Handler();
    $monitor = new SPMBYMA_Monitor($database, $error_handler);
    $monitor->start();

    // Shared services.
    $analyzer = new SPMBYMA_Analyzer($database);
    $perf_analyzer = new SPMBYMA_Performance_Analyzer($database);
    $license_detector = new SPMBYMA_License_Detector();
    $security_scanner = new SPMBYMA_Security_Scanner();
    $data_service = new SPMBYMA_Data_Service($database, $perf_analyzer);
    $action_controller = new SPMBYMA_Action_Controller($database);
    $rollback_analyzer = new SPMBYMA_Rollback_Analyzer($database);
    $export_service = new SPMBYMA_Export_Service($data_service);
    $detail_provider = new SPMBYMA_Detail_Provider($data_service, $license_detector, $security_scanner, $rollback_analyzer);

    // REST API (must register outside is_admin for REST requests).
    new SPMBYMA_REST_Controller($detail_provider, $data_service, $action_controller, $export_service, $analyzer, $license_detector, $security_scanner);

    // Admin UI (only loaded in wp-admin).
    if (is_admin()) {
        new SPMBYMA_Admin($data_service, $detail_provider, $analyzer, $license_detector, $security_scanner);
    }
}
add_action('plugins_loaded', 'spmbyma_init', PHP_INT_MAX);
