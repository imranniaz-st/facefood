<?php

if (! defined('ABSPATH')) {
    exit;
}

global $wpdb;

$message = isset($_GET['message']) ? sanitize_text_field(wp_unslash($_GET['message'])) : '';
$status = isset($_GET['sync']) ? sanitize_text_field(wp_unslash($_GET['sync'])) : '';
$importStatus = isset($_GET['import']) ? sanitize_text_field(wp_unslash($_GET['import'])) : '';

$logs = $wpdb->get_results(
    "SELECT * FROM {$wpdb->prefix}facefood_sync_log ORDER BY id DESC LIMIT 20",
    ARRAY_A
);
?>
<div class="wrap">
    <h1>Facefood Sync</h1>

    <?php if ($message !== '') : ?>
        <div class="notice notice-<?php echo ($status === 'ok' || $importStatus === 'ok') ? 'success' : 'error'; ?> is-dismissible">
            <p><?php echo esc_html($message); ?></p>
        </div>
    <?php endif; ?>

    <div class="card" style="max-width:720px;padding:16px;margin-bottom:20px;">
        <h2>Pull from Facefood API → WooCommerce</h2>
        <p>Downloads categories, products and deals from your Laravel API and creates/updates WooCommerce products.</p>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('facefood_run_sync'); ?>
            <input type="hidden" name="action" value="facefood_run_sync">
            <?php submit_button('Sync Now', 'primary', 'submit', false); ?>
        </form>
    </div>

    <div class="card" style="max-width:720px;padding:16px;margin-bottom:20px;">
        <h2>Push PDF Menu → Facefood API</h2>
        <p>Imports the built-in Facefood 2026 menu (from PDF) into the Laravel API so the mobile app gets all items.</p>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('facefood_import_menu'); ?>
            <input type="hidden" name="action" value="facefood_import_menu">
            <?php submit_button('Import Menu to API', 'secondary', 'submit', false); ?>
        </form>
    </div>

    <h2>Recent Sync Logs</h2>
    <table class="widefat striped">
        <thead>
            <tr>
                <th>When</th>
                <th>Direction</th>
                <th>Status</th>
                <th>Message</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($logs)) : ?>
                <tr><td colspan="4">No sync logs yet.</td></tr>
            <?php else : ?>
                <?php foreach ($logs as $log) : ?>
                    <tr>
                        <td><?php echo esc_html($log['created_at']); ?></td>
                        <td><?php echo esc_html($log['direction']); ?></td>
                        <td><?php echo esc_html($log['status']); ?></td>
                        <td><?php echo esc_html($log['message']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
