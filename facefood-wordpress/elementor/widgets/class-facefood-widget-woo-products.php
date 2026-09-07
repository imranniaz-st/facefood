<?php

if (! defined('ABSPATH')) {
    exit;
}

class Facefood_Widget_Woo_Products extends Facefood_Elementor_Widget_Base
{
    public function get_name(): string
    {
        return 'facefood_woo_products';
    }

    public function get_title(): string
    {
        return esc_html__('Facefood WooCommerce Products', 'facefood-integration');
    }

    public function get_icon(): string
    {
        return 'eicon-products';
    }

    public function get_categories(): array
    {
        return ['facefood', 'woocommerce-elements'];
    }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Products', 'facefood-integration')]);

        $this->add_control('columns', [
            'label' => esc_html__('Columns', 'facefood-integration'),
            'type' => \Elementor\Controls_Manager::NUMBER,
            'default' => 3,
            'min' => 1,
            'max' => 6,
        ]);

        $this->add_control('limit', [
            'label' => esc_html__('Products count', 'facefood-integration'),
            'type' => \Elementor\Controls_Manager::NUMBER,
            'default' => 12,
            'min' => 1,
            'max' => 48,
        ]);

        $this->add_control('category_slug', [
            'label' => esc_html__('Category slug (optional)', 'facefood-integration'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'placeholder' => 'burgers',
        ]);

        $this->end_controls_section();
    }

    protected function render(): void
    {
        if (! class_exists('WooCommerce')) {
            echo '<p class="facefood-empty">' . esc_html__('WooCommerce is required for this widget.', 'facefood-integration') . '</p>';

            return;
        }

        $this->render_if_logged_in_or_gate(function (): void {
            $settings = $this->get_settings_for_display();
            $columns = max(1, (int) ($settings['columns'] ?? 3));
            $limit = max(1, (int) ($settings['limit'] ?? 12));

            $args = [
                'status' => 'publish',
                'limit' => $limit,
                'orderby' => 'title',
                'order' => 'ASC',
                'return' => 'objects',
            ];

            if (! empty($settings['category_slug'])) {
                $args['category'] = [sanitize_title($settings['category_slug'])];
            }

            $products = wc_get_products($args);

            echo '<div class="facefood-menu-grid" style="--facefood-cols:' . esc_attr((string) $columns) . '">';

            if (empty($products)) {
                echo '<p class="facefood-empty">' . esc_html__('No WooCommerce products found. Run Facefood → Sync first.', 'facefood-integration') . '</p>';
            } else {
                foreach ($products as $product) {
                    if (! $product instanceof WC_Product) {
                        continue;
                    }

                    $laravelId = (int) get_post_meta($product->get_id(), '_facefood_laravel_id', true);
                    $payload = [
                        'id' => $laravelId > 0 ? $laravelId : $product->get_id(),
                        'wc_product_id' => $product->get_id(),
                        'woocommerce_sku' => $product->get_sku(),
                        'name' => $product->get_name(),
                        'description' => wp_strip_all_tags($product->get_short_description() ?: $product->get_description()),
                        'price' => (float) $product->get_price(),
                        'image_url' => wp_get_attachment_image_url($product->get_image_id(), 'medium') ?: wc_placeholder_img_src(),
                        'extras' => [],
                    ];

                    Facefood_Render::product_card($payload, false, true);
                }
            }

            echo '</div>';
        });
    }
}
