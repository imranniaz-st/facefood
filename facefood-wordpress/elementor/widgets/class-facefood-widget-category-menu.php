<?php

if (! defined('ABSPATH')) {
    exit;
}

class Facefood_Widget_Category_Menu extends Facefood_Elementor_Widget_Base
{
    public function get_name(): string
    {
        return 'facefood_category_menu';
    }

    public function get_title(): string
    {
        return esc_html__('Facefood Category Menu', 'facefood-integration');
    }

    public function get_icon(): string
    {
        return 'eicon-tabs';
    }

    public function get_categories(): array
    {
        return ['facefood'];
    }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Content', 'facefood-integration')]);
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
        $showToppings = ($settings['show_toppings'] ?? 'yes') === 'yes';
        $categories = $this->fetch_api('/categories');
        $products = $this->fetch_api('/products');

        if (is_wp_error($categories) || is_wp_error($products)) {
            $message = is_wp_error($categories)
                ? $categories->get_error_message()
                : $products->get_error_message();
            echo '<p class="facefood-empty">' . esc_html(Facefood_Security::sanitize_api_message($message)) . '</p>';

            return;
        }

        $byCategory = [];
        foreach ($products as $product) {
            if (! is_array($product)) {
                continue;
            }
            $slug = $product['category']['slug'] ?? 'other';
            $byCategory[$slug][] = $product;
        }

        echo '<div class="facefood-category-menu">';

        if (empty($categories)) {
            echo '<p class="facefood-empty">' . esc_html__('No categories found.', 'facefood-integration') . '</p>';
            echo '</div>';

            return;
        }

        echo '<div class="facefood-tabs">';
        foreach ($categories as $index => $category) {
            if (! is_array($category)) {
                continue;
            }
            $slug = sanitize_title($category['slug'] ?? '');
            $active = $index === 0 ? ' is-active' : '';
            echo '<button type="button" class="facefood-tab' . esc_attr($active) . '" data-target="ff-cat-' . esc_attr($slug) . '">';
            echo esc_html($category['name'] ?? $slug);
            echo '</button>';
        }
        echo '</div>';

        foreach ($categories as $index => $category) {
            if (! is_array($category)) {
                continue;
            }
            $slug = sanitize_title($category['slug'] ?? '');
            $active = $index === 0 ? ' is-active' : '';
            echo '<div id="ff-cat-' . esc_attr($slug) . '" class="facefood-tab-panel' . esc_attr($active) . '">';
            echo '<div class="facefood-menu-grid" style="--facefood-cols:3">';

            foreach ($byCategory[$slug] ?? [] as $product) {
                Facefood_Render::product_card($product, $showToppings);
            }

            echo '</div></div>';
        }

        echo '</div>';
    }
}
