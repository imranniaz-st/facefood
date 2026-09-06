<?php

if (! defined('ABSPATH')) {
    exit;
}

class Facefood_Sync
{
    private Facefood_Api_Client $api;

    public function __construct()
    {
        $this->api = new Facefood_Api_Client();

        add_action('admin_post_facefood_run_sync', [$this, 'handle_manual_sync']);
        add_action('admin_post_facefood_import_menu', [$this, 'handle_import_menu']);
    }

    public static function run_scheduled_sync(): void
    {
        if (get_option('facefood_auto_sync', 'yes') !== 'yes') {
            return;
        }

        (new self())->sync_from_api();
    }

    public function handle_manual_sync(): void
    {
        if (! current_user_can('manage_woocommerce')) {
            wp_die('Unauthorized');
        }

        check_admin_referer('facefood_run_sync');

        $result = $this->sync_from_api();

        $redirect = add_query_arg([
            'page' => 'facefood-sync',
            'sync' => $result['success'] ? 'ok' : 'fail',
            'message' => rawurlencode($result['message']),
        ], admin_url('admin.php'));

        wp_safe_redirect($redirect);
        exit;
    }

    public function handle_import_menu(): void
    {
        if (! current_user_can('manage_woocommerce')) {
            wp_die('Unauthorized');
        }

        check_admin_referer('facefood_import_menu');

        $result = $this->import_local_menu_to_api();

        $redirect = add_query_arg([
            'page' => 'facefood-sync',
            'import' => $result['success'] ? 'ok' : 'fail',
            'message' => rawurlencode($result['message']),
        ], admin_url('admin.php'));

        wp_safe_redirect($redirect);
        exit;
    }

    public function sync_from_api(): array
    {
        if (! class_exists('WooCommerce')) {
            return ['success' => false, 'message' => 'WooCommerce is required.'];
        }

        $catalog = $this->api->get_catalog();
        if (is_wp_error($catalog)) {
            $this->log('pull', 'error', $catalog->get_error_message());

            return ['success' => false, 'message' => $catalog->get_error_message()];
        }

        $data = $catalog['data'] ?? [];
        $categories = $data['categories'] ?? [];
        $products = $data['products'] ?? [];

        $woo = new Facefood_WooCommerce();
        $linked = 0;

        foreach ($categories as $category) {
            $termId = $woo->ensure_product_category($category);
            if ($termId) {
                $this->save_map('category', (int) $category['id'], (int) $termId);
                $this->api->link_entity('category', (int) $category['id'], (int) $termId);
                $linked++;
            }
        }

        foreach ($products as $product) {
            $productId = $woo->ensure_product($product);
            if ($productId) {
                $sku = $product['woocommerce_sku'] ?? ($product['sku'] ?? null);
                $this->save_map('product', (int) $product['id'], (int) $productId, $sku);
                $this->api->link_entity('product', (int) $product['id'], (int) $productId, $sku);
                $linked++;
            }
        }

        update_option('facefood_last_sync_at', current_time('mysql'));
        $message = sprintf('Synced %d items from Facefood API.', $linked);
        $this->log('pull', 'success', $message, ['count' => $linked]);

        return ['success' => true, 'message' => $message];
    }

    public function import_local_menu_to_api(): array
    {
        $count = 0;

        foreach (Facefood_Menu_Data::products() as $item) {
            $response = $this->api->upsert_product([
                'sku' => $item['sku'],
                'category_slug' => $item['category'],
                'name' => $item['name'],
                'description' => 'Facefood menu item — ' . $item['name'],
                'price' => $item['price'],
                'is_available' => true,
            ]);

            if (is_wp_error($response)) {
                $this->log('push', 'error', $response->get_error_message(), $item);

                return ['success' => false, 'message' => $response->get_error_message()];
            }

            $count++;
        }

        $message = sprintf('Imported %d menu items to Facefood API.', $count);
        $this->log('push', 'success', $message, ['count' => $count]);

        return ['success' => true, 'message' => $message];
    }

    private function save_map(string $entityType, int $laravelId, int $wordpressId, ?string $sku = null): void
    {
        global $wpdb;

        $table = $wpdb->prefix . 'facefood_sync_map';

        $wpdb->replace(
            $table,
            [
                'entity_type' => $entityType,
                'laravel_id' => $laravelId,
                'wordpress_id' => $wordpressId,
                'sku' => $sku,
                'last_synced' => current_time('mysql'),
            ],
            ['%s', '%d', '%d', '%s', '%s']
        );
    }

    private function log(string $direction, string $status, string $message, ?array $payload = null): void
    {
        global $wpdb;

        $wpdb->insert(
            $wpdb->prefix . 'facefood_sync_log',
            [
                'direction' => $direction,
                'status' => $status,
                'message' => $message,
                'payload' => $payload ? wp_json_encode($payload) : null,
                'created_at' => current_time('mysql'),
            ],
            ['%s', '%s', '%s', '%s', '%s']
        );
    }
}
