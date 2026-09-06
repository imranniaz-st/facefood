<?php

if (! defined('ABSPATH')) {
    exit;
}

class Facefood_Render
{
    public static function format_price(float $amount): string
    {
        return 'Rs. ' . number_format($amount, 0);
    }

    public static function product_card(array $product, bool $showToppings = true): void
    {
        $id = (int) ($product['id'] ?? 0);
        $name = Facefood_Security::esc_text((string) ($product['name'] ?? ''));
        $price = self::format_price((float) ($product['price'] ?? 0));
        $image = Facefood_Security::esc_url((string) ($product['image_url'] ?? ''));
        $desc = Facefood_Security::esc_text(wp_trim_words((string) ($product['description'] ?? ''), 14));
        $extras = is_array($product['extras'] ?? null) ? $product['extras'] : [];

        echo '<article class="facefood-card" data-product-id="' . esc_attr((string) $id) . '">';

        if ($image !== '') {
            echo '<img class="facefood-card__image" src="' . $image . '" alt="' . esc_attr($name) . '" loading="lazy">';
        }

        echo '<div class="facefood-card__body">';
        echo '<h3 class="facefood-card__title">' . esc_html($name) . '</h3>';

        if ($desc !== '') {
            echo '<p class="facefood-card__desc">' . esc_html($desc) . '</p>';
        }

        echo '<div class="facefood-card__price" data-base-price="' . esc_attr((string) ($product['price'] ?? 0)) . '">';
        echo esc_html($price);
        echo '</div>';

        if ($showToppings && ! empty($extras)) {
            self::toppings_block($id, $extras, (float) ($product['price'] ?? 0));
        }

        echo '</div></article>';
    }

    public static function toppings_block(int $productId, array $extras, float $basePrice): void
    {
        echo '<div class="facefood-toppings" data-product-id="' . esc_attr((string) $productId) . '">';
        echo '<p class="facefood-toppings__label">' . esc_html__('Extra toppings', 'facefood-integration') . '</p>';
        echo '<ul class="facefood-toppings__list">';

        foreach ($extras as $extra) {
            $extraId = (int) ($extra['id'] ?? 0);
            $extraName = Facefood_Security::esc_text((string) ($extra['name'] ?? ''));
            $extraPrice = (float) ($extra['price'] ?? 0);
            $inputId = 'ff-extra-' . $productId . '-' . $extraId;

            echo '<li class="facefood-toppings__item">';
            echo '<label for="' . esc_attr($inputId) . '">';
            echo '<input type="checkbox" class="facefood-extra-toggle" id="' . esc_attr($inputId) . '"';
            echo ' data-product-id="' . esc_attr((string) $productId) . '"';
            echo ' data-extra-id="' . esc_attr((string) $extraId) . '"';
            echo ' data-extra-price="' . esc_attr((string) $extraPrice) . '"';
            echo ' value="' . esc_attr((string) $extraId) . '">';
            echo '<span class="facefood-toppings__name">' . esc_html($extraName) . '</span>';
            echo '<span class="facefood-toppings__price">+' . esc_html(self::format_price($extraPrice)) . '</span>';
            echo '</label></li>';
        }

        echo '</ul>';
        echo '<div class="facefood-toppings__total">';
        echo esc_html__('Total:', 'facefood-integration') . ' ';
        echo '<strong class="facefood-total-price" data-base="' . esc_attr((string) $basePrice) . '">';
        echo esc_html(self::format_price($basePrice));
        echo '</strong></div>';
        echo '</div>';
    }
}
