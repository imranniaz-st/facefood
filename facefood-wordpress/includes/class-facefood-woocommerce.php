<?php

if (! defined('ABSPATH')) {
    exit;
}

class Facefood_WooCommerce
{
    public function ensure_product_category(array $category): int
    {
        $slug = sanitize_title($category['slug'] ?? $category['name']);
        $name = sanitize_text_field($category['name']);
        $existing = get_term_by('slug', $slug, 'product_cat');

        if ($existing instanceof WP_Term) {
            return (int) $existing->term_id;
        }

        $created = wp_insert_term($name, 'product_cat', ['slug' => $slug]);

        if (is_wp_error($created)) {
            return 0;
        }

        return (int) ($created['term_id'] ?? 0);
    }

    public function ensure_product(array $product): int
    {
        $sku = sanitize_text_field($product['woocommerce_sku'] ?? ($product['sku'] ?? ''));
        if ($sku === '') {
            $sku = 'FF-' . (int) ($product['id'] ?? 0);
        }

        $existingId = wc_get_product_id_by_sku($sku);
        $wcProduct = $existingId ? wc_get_product($existingId) : new WC_Product_Simple();

        if (! $wcProduct) {
            return 0;
        }

        $wcProduct->set_name(sanitize_text_field($product['name']));
        $wcProduct->set_sku($sku);
        $wcProduct->set_regular_price((string) ($product['price'] ?? 0));
        $wcProduct->set_description(wp_kses_post($product['description'] ?? ''));
        $wcProduct->set_status(! empty($product['is_available']) ? 'publish' : 'draft');
        $wcProduct->set_catalog_visibility('visible');
        $wcProduct->set_manage_stock(false);
        $wcProduct->set_stock_status('instock');

        if (! empty($product['image_url'])) {
            $this->maybe_set_image($wcProduct, (string) $product['image_url']);
        }

        $categorySlug = $product['category']['slug'] ?? null;
        if ($categorySlug) {
            $termId = $this->ensure_product_category([
                'slug' => $categorySlug,
                'name' => $product['category']['name'] ?? ucfirst($categorySlug),
            ]);
            if ($termId) {
                $wcProduct->set_category_ids([$termId]);
            }
        }

        $productId = $wcProduct->save();
        update_post_meta($productId, '_facefood_laravel_id', (int) ($product['id'] ?? 0));

        return (int) $productId;
    }

    private function maybe_set_image(WC_Product $product, string $imageUrl): void
    {
        if (! filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            return;
        }

        $existing = get_post_meta($product->get_id(), '_facefood_image_url', true);
        if ($existing === $imageUrl && $product->get_image_id()) {
            return;
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $attachmentId = media_sideload_image($imageUrl, $product->get_id(), $product->get_name(), 'id');

        if (! is_wp_error($attachmentId)) {
            $product->set_image_id((int) $attachmentId);
            update_post_meta($product->get_id(), '_facefood_image_url', $imageUrl);
        }
    }
}
