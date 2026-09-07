<?php

if (! defined('ABSPATH')) {
    exit;
}

class Facefood_Links
{
    public static function normalize_api_base_url(string $url): string
    {
        $url = rtrim(trim($url), '/');

        if ($url === '') {
            return 'https://app.facefood.cafe/api';
        }

        if (! preg_match('#/api$#i', $url)) {
            $url .= '/api';
        }

        return $url;
    }

    public static function laravel_to_wc_product_id(int $laravelId, ?string $sku = null): int
    {
        if ($laravelId <= 0) {
            return 0;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'facefood_sync_map';

        $mapped = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT wordpress_id FROM {$table} WHERE entity_type = %s AND laravel_id = %d LIMIT 1",
            'product',
            $laravelId
        ));

        if ($mapped > 0) {
            return $mapped;
        }

        if ($sku && function_exists('wc_get_product_id_by_sku')) {
            $bySku = (int) wc_get_product_id_by_sku(sanitize_text_field($sku));
            if ($bySku > 0) {
                return $bySku;
            }
        }

        $byMeta = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %d LIMIT 1",
            '_facefood_laravel_id',
            $laravelId
        ));

        return $byMeta > 0 ? $byMeta : 0;
    }

    public static function product_url(int $laravelId, ?string $sku = null): string
    {
        $wcId = self::laravel_to_wc_product_id($laravelId, $sku);

        if ($wcId > 0) {
            $permalink = get_permalink($wcId);
            if (is_string($permalink) && $permalink !== '') {
                return $permalink;
            }
        }

        return self::shop_url();
    }

    public static function add_to_cart_url(int $laravelId, ?string $sku = null): string
    {
        $wcId = self::laravel_to_wc_product_id($laravelId, $sku);

        if ($wcId > 0 && function_exists('wc_get_cart_url')) {
            return add_query_arg('add-to-cart', $wcId, wc_get_cart_url());
        }

        return self::shop_url();
    }

    public static function shop_url(): string
    {
        if (function_exists('wc_get_page_permalink')) {
            $shop = wc_get_page_permalink('shop');
            if (is_string($shop) && $shop !== '') {
                return $shop;
            }
        }

        $custom = (string) get_option('facefood_shop_url', '');
        if ($custom !== '') {
            return esc_url_raw($custom);
        }

        return home_url('/shop/');
    }

    public static function order_url(): string
    {
        if (function_exists('wc_get_checkout_url')) {
            return wc_get_checkout_url();
        }

        $custom = (string) get_option('facefood_order_url', '');
        if ($custom !== '') {
            return esc_url_raw($custom);
        }

        return home_url('/checkout/');
    }
}
