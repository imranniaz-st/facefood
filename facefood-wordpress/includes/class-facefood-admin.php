<?php

if (! defined('ABSPATH')) {
    exit;
}

class Facefood_Admin
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'register_menus']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function register_menus(): void
    {
        add_menu_page(
            'Facefood',
            'Facefood',
            'manage_woocommerce',
            'facefood-settings',
            [$this, 'render_settings_page'],
            'dashicons-food',
            56
        );

        add_submenu_page(
            'facefood-settings',
            'Settings',
            'Settings',
            'manage_woocommerce',
            'facefood-settings',
            [$this, 'render_settings_page']
        );

        add_submenu_page(
            'facefood-settings',
            'Sync',
            'Sync',
            'manage_woocommerce',
            'facefood-sync',
            [$this, 'render_sync_page']
        );
    }

    public function register_settings(): void
    {
        register_setting('facefood_settings', 'facefood_api_base_url', ['sanitize_callback' => 'esc_url_raw']);
        register_setting('facefood_settings', 'facefood_sync_key', ['sanitize_callback' => 'sanitize_text_field']);
        register_setting('facefood_settings', 'facefood_app_store_url', ['sanitize_callback' => 'esc_url_raw']);
        register_setting('facefood_settings', 'facefood_app_play_url', ['sanitize_callback' => 'esc_url_raw']);
        register_setting('facefood_settings', 'facefood_auto_sync', ['sanitize_callback' => 'sanitize_text_field']);
    }

    public function render_settings_page(): void
    {
        include FACEFOOD_PLUGIN_DIR . 'admin/settings-page.php';
    }

    public function render_sync_page(): void
    {
        include FACEFOOD_PLUGIN_DIR . 'admin/sync-page.php';
    }
}
