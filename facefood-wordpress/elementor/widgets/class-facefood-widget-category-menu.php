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

    protected function render(): void
    {
        $categories = $this->fetch_api('/categories');
        $products = $this->fetch_api('/products');

        $byCategory = [];
        foreach ($products as $product) {
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
            $slug = $category['slug'] ?? '';
            $active = $index === 0 ? ' is-active' : '';
            echo '<button type="button" class="facefood-tab' . esc_attr($active) . '" data-target="ff-cat-' . esc_attr($slug) . '">';
            echo esc_html($category['name'] ?? $slug);
            echo '</button>';
        }
        echo '</div>';

        foreach ($categories as $index => $category) {
            $slug = $category['slug'] ?? '';
            $active = $index === 0 ? ' is-active' : '';
            echo '<div id="ff-cat-' . esc_attr($slug) . '" class="facefood-tab-panel' . esc_attr($active) . '">';
            echo '<div class="facefood-menu-grid" style="--facefood-cols:3">';

            foreach ($byCategory[$slug] ?? [] as $product) {
                $name = esc_html($product['name'] ?? '');
                $price = esc_html($this->format_price((float) ($product['price'] ?? 0)));
                echo '<article class="facefood-card"><div class="facefood-card__body">';
                echo '<h3 class="facefood-card__title">' . $name . '</h3>';
                echo '<div class="facefood-card__price">' . $price . '</div>';
                echo '</div></article>';
            }

            echo '</div></div>';
        }

        echo '</div>';
    }
}
