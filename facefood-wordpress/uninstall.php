<?php

if (! defined('ABSPATH')) {
    exit;
}

if (! defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

delete_option('facefood_api_base_url');
delete_option('facefood_sync_key');
delete_option('facefood_app_store_url');
delete_option('facefood_app_play_url');
delete_option('facefood_auto_sync');
delete_option('facefood_last_sync_at');
delete_option('facefood_db_version');

global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}facefood_sync_map");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}facefood_sync_log");

wp_clear_scheduled_hook('facefood_scheduled_sync');
