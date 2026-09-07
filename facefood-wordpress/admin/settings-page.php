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
            <tr>
                <th scope="row"><label for="facefood_shop_url">Shop page URL (fallback)</label></th>
                <td>
                    <input type="url" class="regular-text" id="facefood_shop_url" name="facefood_shop_url"
                           value="<?php echo esc_attr(get_option('facefood_shop_url', function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : '')); ?>">
                    <p class="description">Used when a product link cannot be resolved. Usually your WooCommerce shop page.</p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="facefood_order_url">Checkout URL (fallback)</label></th>
                <td>
                    <input type="url" class="regular-text" id="facefood_order_url" name="facefood_order_url"
                           value="<?php echo esc_attr(get_option('facefood_order_url', function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : '')); ?>">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="facefood_require_login_for_products">Require login for menu</label></th>
                <td>
                    <select id="facefood_require_login_for_products" name="facefood_require_login_for_products">
                        <option value="yes" <?php selected(get_option('facefood_require_login_for_products', 'yes'), 'yes'); ?>>Yes — show login/signup before products</option>
                        <option value="no" <?php selected(get_option('facefood_require_login_for_products', 'yes'), 'no'); ?>>No — show products to everyone</option>
                    </select>
                </td>
            </tr>
        </table>
        <?php submit_button('Save Settings'); ?>
    </form>

    <?php if ($lastSync) : ?>
        <p><strong>Last sync:</strong> <?php echo esc_html($lastSync); ?></p>
    <?php endif; ?>

    <hr>
    <h2>Link checklist</h2>
    <ul style="list-style:disc;padding-left:20px;">
        <li>API URL must be <code>https://app.facefood.cafe/api</code> (with <code>/api</code>)</li>
        <li>Run <strong>Facefood → Sync → Sync Now</strong> so products link to WooCommerce</li>
        <li>WooCommerce → Settings → Products → Shop page must be set</li>
        <li>Use Elementor widget <strong>Facefood WooCommerce Products</strong> if API menu is empty</li>
    </ul>
</div>
