<?php

if (! defined('ABSPATH')) {
    exit;
}

class Facefood_Plugin
{
    private static ?self $instance = null;

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function init(): void
    {
        load_plugin_textdomain('facefood-integration', false, dirname(FACEFOOD_PLUGIN_BASENAME) . '/languages');

        new Facefood_Admin();
        new Facefood_Sync();
        new Facefood_Auth();

        if (class_exists('WooCommerce')) {
            new Facefood_WooCommerce();
        }

        add_action('wp_enqueue_scripts', [$this, 'enqueue_public_assets']);
        add_action('facefood_scheduled_sync', [Facefood_Sync::class, 'run_scheduled_sync']);

        if (! wp_next_scheduled('facefood_scheduled_sync')) {
            wp_schedule_event(time() + HOUR_IN_SECONDS, 'twicedaily', 'facefood_scheduled_sync');
        }

        if (did_action('elementor/loaded')) {
            new Facefood_Elementor();
        } else {
            add_action('elementor/loaded', static function (): void {
                new Facefood_Elementor();
            });
        }
    }

    public function enqueue_public_assets(): void
    {
        wp_enqueue_style(
            'facefood-public',
            FACEFOOD_PLUGIN_URL . 'assets/css/facefood-public.css',
            [],
            FACEFOOD_VERSION
        );

        wp_enqueue_script(
            'facefood-public',
            FACEFOOD_PLUGIN_URL . 'assets/js/facefood-public.js',
            ['jquery'],
            FACEFOOD_VERSION,
            true
        );

        wp_localize_script('facefood-public', 'facefoodPublic', [
            'apiBase' => esc_url_raw(get_option('facefood_api_base_url', 'https://app.facefood.cafe/api')),
            'currency' => 'Rs.',
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('facefood_public_nonce'),
        ]);
    }
}
