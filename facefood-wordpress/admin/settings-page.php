<?php

if (! defined('ABSPATH')) {
    exit;
}

$lastSync = get_option('facefood_last_sync_at', '');
?>
<div class="wrap">
    <h1>Facefood Settings</h1>
    <form method="post" action="options.php">
        <?php settings_fields('facefood_settings'); ?>
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><label for="facefood_api_base_url">API Base URL</label></th>
                <td>
                    <input type="url" class="regular-text" id="facefood_api_base_url" name="facefood_api_base_url"
                           value="<?php echo esc_attr(get_option('facefood_api_base_url', 'https://app.facefood.cafe/api')); ?>">
                    <p class="description">Laravel API used by the Flutter app and this plugin.</p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="facefood_sync_key">Sync Key</label></th>
                <td>
                    <input type="text" class="regular-text" id="facefood_sync_key" name="facefood_sync_key"
                           value="<?php echo esc_attr(get_option('facefood_sync_key', '')); ?>">
                    <p class="description">Must match <code>WORDPRESS_SYNC_KEY</code> in Laravel <code>.env</code>.</p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="facefood_auto_sync">Auto Sync</label></th>
                <td>
                    <select id="facefood_auto_sync" name="facefood_auto_sync">
                        <option value="yes" <?php selected(get_option('facefood_auto_sync', 'yes'), 'yes'); ?>>Enabled</option>
                        <option value="no" <?php selected(get_option('facefood_auto_sync', 'yes'), 'no'); ?>>Disabled</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="facefood_app_play_url">Google Play URL</label></th>
                <td>
                    <input type="url" class="regular-text" id="facefood_app_play_url" name="facefood_app_play_url"
                           value="<?php echo esc_attr(get_option('facefood_app_play_url', '')); ?>">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="facefood_app_store_url">App Store URL</label></th>
                <td>
                    <input type="url" class="regular-text" id="facefood_app_store_url" name="facefood_app_store_url"
                           value="<?php echo esc_attr(get_option('facefood_app_store_url', '')); ?>">
                </td>
            </tr>
        </table>
        <?php submit_button('Save Settings'); ?>
    </form>

    <?php if ($lastSync) : ?>
        <p><strong>Last sync:</strong> <?php echo esc_html($lastSync); ?></p>
    <?php endif; ?>
</div>
