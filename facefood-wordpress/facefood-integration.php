<?php
/**
 * Plugin Name: Facefood Integration
 * Plugin URI: https://facefood.cafe
 * Description: Sync Facefood menu with WooCommerce and display menu/deals via Elementor widgets. Connects to the Facefood Laravel API for the mobile app.
 * Version: 1.1.0
 * Author: Facefood
 * Author URI: https://facefood.cafe
 * Text Domain: facefood-integration
 * Requires at least: 6.0
 * Requires PHP: 8.1
 * WC requires at least: 8.0
 * WC tested up to: 9.0
 * Elementor tested up to: 3.24
 */

if (! defined('ABSPATH')) {
    exit;
}

define('FACEFOOD_VERSION', '1.1.0');
define('FACEFOOD_PLUGIN_FILE', __FILE__);
define('FACEFOOD_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FACEFOOD_PLUGIN_URL', plugin_dir_url(__FILE__));
define('FACEFOOD_PLUGIN_BASENAME', plugin_basename(__FILE__));

require_once FACEFOOD_PLUGIN_DIR . 'includes/class-facefood-autoloader.php';
Facefood_Autoloader::register();

register_activation_hook(__FILE__, ['Facefood_Activator', 'activate']);
register_deactivation_hook(__FILE__, ['Facefood_Deactivator', 'deactivate']);

add_action('plugins_loaded', static function (): void {
    if (! class_exists('WooCommerce')) {
        add_action('admin_notices', static function (): void {
            echo '<div class="notice notice-warning"><p><strong>Facefood Integration</strong> requires WooCommerce to be installed and active.</p></div>';
        });
    }

    Facefood_Plugin::instance()->init();
});
