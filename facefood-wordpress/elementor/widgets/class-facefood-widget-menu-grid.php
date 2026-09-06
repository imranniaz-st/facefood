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
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $query = ! empty($settings['category_slug']) ? '?category=' . rawurlencode($settings['category_slug']) : '';
        $products = $this->fetch_api('/products' . $query);
        $columns = max(1, (int) ($settings['columns'] ?? 3));

        echo '<div class="facefood-menu-grid" style="--facefood-cols:' . esc_attr((string) $columns) . '">';

        if (empty($products)) {
            echo '<p class="facefood-empty">' . esc_html__('No menu items found. Run Facefood Sync first.', 'facefood-integration') . '</p>';
        } else {
            foreach ($products as $product) {
                $this->render_card($product);
            }
        }

        echo '</div>';
    }

    private function render_card(array $product): void
    {
        $name = esc_html($product['name'] ?? '');
        $price = $this->format_price((float) ($product['price'] ?? 0));
        $image = esc_url($product['image_url'] ?? '');
        $desc = esc_html(wp_trim_words($product['description'] ?? '', 12));

        echo '<article class="facefood-card">';
        if ($image) {
            echo '<img class="facefood-card__image" src="' . $image . '" alt="' . $name . '" loading="lazy">';
        }
        echo '<div class="facefood-card__body">';
        echo '<h3 class="facefood-card__title">' . $name . '</h3>';
        if ($desc) {
            echo '<p class="facefood-card__desc">' . $desc . '</p>';
        }
        echo '<div class="facefood-card__price">' . esc_html($price) . '</div>';
        echo '</div></article>';
    }
}
