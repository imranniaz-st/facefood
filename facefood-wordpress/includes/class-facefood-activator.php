<?php

if (! defined('ABSPATH')) {
    exit;
}

class Facefood_Activator
{
    public static function activate(): void
    {
        self::run_migrations();
        self::seed_default_options();
        flush_rewrite_rules();
    }

    private static function run_migrations(): void
    {
        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset = $wpdb->get_charset_collate();
        $map = $wpdb->prefix . 'facefood_sync_map';
        $log = $wpdb->prefix . 'facefood_sync_log';

        $sqlMap = "CREATE TABLE {$map} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            entity_type varchar(32) NOT NULL,
            laravel_id bigint(20) unsigned NOT NULL,
            wordpress_id bigint(20) unsigned NOT NULL,
            sku varchar(100) DEFAULT NULL,
            last_synced datetime NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY entity_laravel (entity_type, laravel_id),
            UNIQUE KEY entity_wordpress (entity_type, wordpress_id),
            KEY sku (sku)
        ) {$charset};";

        $sqlLog = "CREATE TABLE {$log} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            direction varchar(16) NOT NULL,
            status varchar(16) NOT NULL,
            message text NOT NULL,
            payload longtext NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY status (status),
            KEY created_at (created_at)
        ) {$charset};";

        dbDelta($sqlMap);
        dbDelta($sqlLog);
    }

    private static function seed_default_options(): void
    {
        add_option('facefood_api_base_url', 'https://app.facefood.cafe/api');
        add_option('facefood_sync_key', '');
        add_option('facefood_app_store_url', '');
        add_option('facefood_app_play_url', '');
        add_option('facefood_auto_sync', 'yes');
        add_option('facefood_last_sync_at', '');
        add_option('facefood_db_version', '1.0.0');
    }
}
