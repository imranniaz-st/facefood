<?php

if (! defined('ABSPATH')) {
    exit;
}

class Facefood_Widget_Menu_Grid extends Facefood_Elementor_Widget_Base
{
    public function get_name(): string
    {
        return 'facefood_menu_grid';
    }

    public function get_title(): string
    {
        return esc_html__('Facefood Menu Grid', 'facefood-integration');
    }

    public function get_icon(): string
    {
        return 'eicon-gallery-grid';
    }

    public function get_categories(): array
    {
        return ['facefood'];
    }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Content', 'facefood-integration')]);
        $this->add_control('category_slug', [
            'label' => esc_html__('Category slug (optional)', 'facefood-integration'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'placeholder' => 'burgers',
        ]);
        $this->add_control('columns', [
            'label' => esc_html__('Columns', 'facefood-integration'),
            'type' => \Elementor\Controls_Manager::NUMBER,
            'default' => 3,
            'min' => 1,
            'max' => 6,
        ]);
        $this->add_control('show_toppings', [
            'label' => esc_html__('Show extra toppings', 'facefood-integration'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
            'label_on' => esc_html__('Yes', 'facefood-integration'),
            'label_off' => esc_html__('No', 'facefood-integration'),
        ]);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $query = ! empty($settings['category_slug']) ? '?category=' . rawurlencode(sanitize_title($settings['category_slug'])) : '';
        $products = $this->fetch_api('/products' . $query);
        $columns = max(1, (int) ($settings['columns'] ?? 3));
        $showToppings = ($settings['show_toppings'] ?? 'yes') === 'yes';

        echo '<div class="facefood-menu-grid" style="--facefood-cols:' . esc_attr((string) $columns) . '">';

        if (empty($products) || is_wp_error($products)) {
            $message = is_wp_error($products)
                ? Facefood_Security::sanitize_api_message($products->get_error_message())
                : __('No menu items found. Run Facefood Sync first.', 'facefood-integration');
            echo '<p class="facefood-empty">' . esc_html($message) . '</p>';
        } else {
            foreach ($products as $product) {
                if (is_array($product)) {
                    Facefood_Render::product_card($product, $showToppings);
                }
            }
        }

        echo '</div>';
    }
}
