<?php

if (! defined('ABSPATH')) {
    exit;
}

class Facefood_Deactivator
{
    public static function deactivate(): void
    {
        wp_clear_scheduled_hook('facefood_scheduled_sync');
        flush_rewrite_rules();
    }
}
