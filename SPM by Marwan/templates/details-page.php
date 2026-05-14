<?php
/**
 * Template for the Plugin Details page.
 *
 * @var array $data Plugin detail data from SPMBYMA_Detail_Provider.
 * @package SmartPluginMonitor
 */

if (!defined('ABSPATH')) {
    exit;
}

$meta = $data['meta'];
$perf = $data['performance'];
$logs = $data['error_logs'];
$trust = $data['trust'];
$security = $data['security'];
$history = $data['performance_history'] ?? [];

$status_html = $meta['is_active']
    ? '<span class="spm-status-badge spm-status-badge--active">' . esc_html__('Active', 'spm-by-marwan') . '</span>'
    : '<span class="spm-status-badge spm-status-badge--inactive">' . esc_html__('Inactive', 'spm-by-marwan') . '</span>';

// Trust Badge Logic
$trust_status = 'unknown';
$trust_label = __('Unknown', 'spm-by-marwan');
$trust_class = 'neutral';

if ($trust['verification'] === 'verified' && $security['risk_level'] === 'clean') {
    $trust_status = 'verified';
    $trust_label = __('Verified', 'spm-by-marwan');
    $trust_class = 'success';
} elseif ($trust['verification'] === 'needs-review' || $security['risk_level'] === 'high' || $security['risk_level'] === 'medium') {
    $trust_status = 'suspicious';
    $trust_label = __('Suspicious', 'spm-by-marwan');
    $trust_class = 'danger';
} elseif ($trust['verification'] === 'unverified') {
    $trust_status = 'unknown';
    $trust_label = __('Unknown', 'spm-by-marwan');
    $trust_class = 'warning';
}

$isolation_target = get_option('spmbyma_isolation_target');
?>

<div class="spm spm-details-page" id="spm-details-root">

    <?php if ($isolation_target): ?>
        <div class="spm-isolation-banner">
            <div class="spm-isolation-banner__icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                    <line x1="12" y1="9" x2="12" y2="13" />
                    <line x1="12" y1="17" x2="12.01" y2="17" />
                </svg>
            </div>
            <div class="spm-isolation-banner__text">
                <strong><?php esc_html_e('Isolation Mode Active:', 'spm-by-marwan'); ?></strong>
                <?php printf(esc_html__('"%s" is currently deactivated for performance testing.', 'spm-by-marwan'), esc_html($isolation_target ?? '')); ?>
            </div>
            <button class="spm-btn spm-btn--white spm-btn--sm" data-spm-action="restore">
                <?php esc_html_e('End Isolation & Restore', 'spm-by-marwan'); ?>
            </button>
        </div>
    <?php endif; ?>

    <!-- Header -->
    <header class="spm-header">
        <div class="spm-header__inner">
            <div class="spm-header__left">
                <a href="<?php echo esc_url(admin_url('admin.php?page=spm-dashboard')); ?>" class="spm-back-link"
                    title="<?php esc_attr_e('Back to Dashboard', 'spm-by-marwan'); ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5" />
                        <polyline points="12 19 5 12 12 5" />
                    </svg>
                </a>
                <div>
                    <h1 class="spm-header__title"><?php echo esc_html($meta['name'] ?? ''); ?></h1>
                    <div class="spm-header__meta">
                        <?php echo wp_kses_post($status_html); ?>
                        <span class="spm-header__sep">·</span>
                        <span class="spm-header__version">v<?php echo esc_html($meta['version'] ?? ''); ?></span>
                        <span class="spm-header__sep">·</span>
                        <span class="spm-trust-badge spm-trust-badge--<?php echo esc_attr($trust_class); ?>">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                            </svg>
                            <?php echo esc_html($trust_label); ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="spm-header__right">
                <div class="spm-header__actions">
                    <button class="spm-hdr-btn" data-spm-basename="<?php echo esc_attr($meta['basename'] ?? ''); ?>"
                        data-spm-action="scan">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M21 12a9 9 0 1 1-9-9" />
                            <polyline points="21 3 21 9 15 9" />
                        </svg>
                        <?php esc_html_e('Refresh Now', 'spm-by-marwan'); ?>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Tabs Navigation -->
    <div class="spm-details-tabs">
        <button class="spm-details-tab spm-details-tab--active" data-tab-id="performance">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
            </svg>
            <?php esc_html_e('Performance', 'spm-by-marwan'); ?>
        </button>
        <button class="spm-details-tab" data-tab-id="security">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
            </svg>
            <?php esc_html_e('Security & Trust', 'spm-by-marwan'); ?>
        </button>
        <button class="spm-details-tab" data-tab-id="history">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" />
                <polyline points="17 6 23 6 23 12" />
            </svg>
            <?php esc_html_e('History', 'spm-by-marwan'); ?>
        </button>
        <button class="spm-details-tab" data-tab-id="logs">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                <polyline points="14 2 14 8 20 8" />
                <line x1="16" y1="13" x2="8" y2="13" />
                <line x1="16" y1="17" x2="8" y2="17" />
                <polyline points="10 9 9 9 8 9" />
            </svg>
            <?php esc_html_e('Logs', 'spm-by-marwan'); ?>
        </button>
    </div>

    <div class="spm-body-grid">
        <!-- Main Content -->
        <div class="spm-main-col">

            <!-- ── Performance Tab ── -->
            <?php include SPMBYMA_PLUGIN_DIR . 'templates/tabs/performance.php'; ?>

            <!-- ── Security & Trust Tab ── -->
            <?php include SPMBYMA_PLUGIN_DIR . 'templates/tabs/security.php'; ?>

            <!-- ── History Tab ── -->
            <?php include SPMBYMA_PLUGIN_DIR . 'templates/tabs/history.php'; ?>

            <!-- ── Logs Tab ── -->
            <div class="spm-tab-content" id="spm-tab-logs">
                <div class="spm-panel">
                    <div class="spm-panel__header">
                        <h2 class="spm-panel__title">
                            <?php esc_html_e('Recent Activity Logs', 'spm-by-marwan'); ?>
                        </h2>
                        <div style="display:flex; align-items:center; gap: 8px;">
                            <span class="spm-panel__badge"><?php echo esc_html( count($logs) ); ?></span>
                            <?php if ( ! empty( $logs ) ) : ?>
                                <button class="spm-btn spm-btn--sm spm-btn--white" data-spm-action="wipe-logs" title="<?php esc_attr_e( 'Wipe error logs for this plugin', 'spm-by-marwan' ); ?>" style="padding: 2px 6px !important; min-height: 24px; color: #ef4444 !important;">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="spm-panel__body spm-detail-errors">
                        <?php if (empty($logs)): ?>
                            <p class="spm-detail-empty">
                                <?php esc_html_e('No logs available for this plugin.', 'spm-by-marwan'); ?>
                            </p>
                        <?php else: ?>
                            <?php foreach ($logs as $log): ?>
                                <div class="spm-detail-error">
                                    <div class="spm-detail-error__head">
                                        <span
                                            class="spm-dot spm-dot--<?php echo esc_attr( $log['error_message'] ? 'danger' : 'success' ); ?> spm-dot--sm"></span>
                                        <span
                                            class="spm-detail-error__level"><?php echo esc_html( $log['error_message'] ? ( $log['error_level'] ?? 'ERROR' ) : 'OK' ); ?></span>
                                        <span
                                            class="spm-detail-error__time"><?php echo esc_html($log['created_at'] ?? ''); ?></span>
                                        <span class="spm-detail-error__load"><?php echo esc_html($log['load_time_ms'] ?? 0); ?>
                                            ms</span>
                                    </div>
                                    <?php if ($log['error_message']): ?>
                                        <pre
                                            class="spm-detail-error__msg"><?php echo esc_html($log['error_message'] ?? ''); ?></pre>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column / Sidebar -->
        <div class="spm-right-col">

            <!-- Plugin Information Sidebar Card -->
            <div class="spm-panel">
                <div class="spm-panel__header">
                    <h2 class="spm-panel__title"><?php esc_html_e('Plugin Metadata', 'spm-by-marwan'); ?></h2>
                </div>
                <div class="spm-panel__body">
                    <div class="spm-sidebar-info">
                        <div class="spm-sidebar-row">
                            <span
                                class="spm-sidebar-label"><?php esc_html_e('Author', 'spm-by-marwan'); ?></span>
                            <span class="spm-sidebar-value"><?php echo esc_html($meta['author'] ?? ''); ?></span>
                        </div>
                        <div class="spm-sidebar-row">
                            <span
                                class="spm-sidebar-label"><?php esc_html_e('Plugin Path', 'spm-by-marwan'); ?></span>
                            <span
                                class="spm-sidebar-value spm-sidebar-value--code"><code><?php echo esc_html($meta['basename'] ?? ''); ?></code></span>
                        </div>
                        <div class="spm-sidebar-row">
                            <span
                                class="spm-sidebar-label"><?php esc_html_e('Text Domain', 'spm-by-marwan'); ?></span>
                            <span
                                class="spm-sidebar-value"><?php echo esc_html(!empty($meta['text_domain']) ? $meta['text_domain'] : 'N/A'); ?></span>
                        </div>
                        <div class="spm-sidebar-row">
                            <span
                                class="spm-sidebar-label"><?php esc_html_e('Requires WP', 'spm-by-marwan'); ?></span>
                            <span
                                class="spm-sidebar-value"><?php echo esc_html(!empty($meta['requires_wp']) ? $meta['requires_wp'] : 'Not specified'); ?></span>
                        </div>
                        <div class="spm-sidebar-row">
                            <span
                                class="spm-sidebar-label"><?php esc_html_e('Requires PHP', 'spm-by-marwan'); ?></span>
                            <span
                                class="spm-sidebar-value"><?php echo esc_html(!empty($meta['requires_php']) ? $meta['requires_php'] : 'Not specified'); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Full Path (Required) -->
            <div class="spm-panel">
                <div class="spm-panel__header">
                    <h2 class="spm-panel__title"><?php esc_html_e('Full File Path', 'spm-by-marwan'); ?></h2>
                </div>
                <div class="spm-panel__body">
                    <code
                        class="spm-detail-path spm-detail-path--compact"><?php echo esc_html($meta['full_path'] ?? ''); ?></code>
                </div>
            </div>

            <!-- Actions -->
            <div class="spm-panel">
                <div class="spm-panel__header">
                    <h2 class="spm-panel__title"><?php esc_html_e('Management', 'spm-by-marwan'); ?></h2>
                </div>
                <div class="spm-panel__body">
                    <div class="spm-details-actions">
                        <?php if ($meta['is_active']): ?>
                            <button class="spm-btn spm-btn--danger spm-btn--full"
                                data-spm-basename="<?php echo esc_attr($meta['basename'] ?? ''); ?>"
                                data-spm-action="disable">
                                <?php esc_html_e('Deactivate Plugin', 'spm-by-marwan'); ?>
                            </button>
                            <?php if (!$isolation_target): ?>
                                <button class="spm-btn spm-btn--outline spm-btn--full"
                                    data-spm-basename="<?php echo esc_attr($meta['basename'] ?? ''); ?>"
                                    data-spm-action="isolate"
                                    title="<?php esc_attr_e('Temporarily disable to test site performance without this plugin.', 'spm-by-marwan'); ?>">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M12 2v20M2 12h20" />
                                    </svg>
                                    <?php esc_html_e('Isolate for Test', 'spm-by-marwan'); ?>
                                </button>
                            <?php endif; ?>
                        <?php else: ?>
                            <button class="spm-btn spm-btn--success spm-btn--full"
                                data-spm-basename="<?php echo esc_attr($meta['basename'] ?? ''); ?>"
                                data-spm-action="enable">
                                <?php esc_html_e('Activate Plugin', 'spm-by-marwan'); ?>
                            </button>
                        <?php endif; ?>
                        <button class="spm-btn spm-btn--outline spm-btn--full" onclick="window.location.reload();">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M21 2v6h-6M3 12a9 9 0 0 1 15-6.7L21 8M3 22v-6h6M21 12a9 9 0 0 0-15 6.7L3 16" />
                            </svg>
                            <?php esc_html_e('Refresh Page', 'spm-by-marwan'); ?>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>