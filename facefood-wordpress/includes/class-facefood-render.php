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

    public static function product_card(array $product, bool $showToppings = true, bool $linkToWoo = true): void
    {
        $id = (int) ($product['id'] ?? 0);
        $sku = isset($product['woocommerce_sku']) ? (string) $product['woocommerce_sku'] : null;
        $wcId = (int) ($product['wc_product_id'] ?? 0);
        if ($wcId <= 0) {
            $wcId = Facefood_Links::laravel_to_wc_product_id($id, $sku);
        }

        $name = Facefood_Security::esc_text((string) ($product['name'] ?? ''));
        $price = self::format_price((float) ($product['price'] ?? 0));
        $image = Facefood_Security::esc_url((string) ($product['image_url'] ?? ''));
        $desc = Facefood_Security::esc_text(wp_trim_words((string) ($product['description'] ?? ''), 14));
        $extras = is_array($product['extras'] ?? null) ? $product['extras'] : [];

        $productUrl = '';
        $cartUrl = '';
        if ($linkToWoo) {
            $productUrl = $wcId > 0 ? (string) get_permalink($wcId) : Facefood_Links::product_url($id, $sku);
            $cartUrl = $wcId > 0 && function_exists('wc_get_cart_url')
                ? add_query_arg('add-to-cart', $wcId, wc_get_cart_url())
                : Facefood_Links::add_to_cart_url($id, $sku);
        }

        echo '<article class="facefood-card" data-product-id="' . esc_attr((string) $id) . '">';

        if ($image !== '') {
            if ($productUrl !== '') {
                echo '<a class="facefood-card__image-link" href="' . esc_url($productUrl) . '">';
            }
            echo '<img class="facefood-card__image" src="' . esc_url($image) . '" alt="' . esc_attr($name) . '" loading="lazy">';
            if ($productUrl !== '') {
                echo '</a>';
            }
        }

        echo '<div class="facefood-card__body">';

        if ($productUrl !== '') {
            echo '<h3 class="facefood-card__title"><a href="' . esc_url($productUrl) . '">' . esc_html($name) . '</a></h3>';
        } else {
            echo '<h3 class="facefood-card__title">' . esc_html($name) . '</h3>';
        }

        if ($desc !== '') {
            echo '<p class="facefood-card__desc">' . esc_html($desc) . '</p>';
        }

        echo '<div class="facefood-card__price" data-base-price="' . esc_attr((string) ($product['price'] ?? 0)) . '">';
        echo esc_html($price);
        echo '</div>';

        if ($showToppings && ! empty($extras)) {
            self::toppings_block($id, $extras, (float) ($product['price'] ?? 0));
        }

        if ($linkToWoo && $productUrl !== '') {
            echo '<div class="facefood-card__actions">';
            echo '<a class="facefood-btn facefood-btn--outline" href="' . esc_url($productUrl) . '">';
            echo esc_html__('View', 'facefood-integration');
            echo '</a>';
            if ($cartUrl !== '') {
                echo '<a class="facefood-btn facefood-btn--primary facefood-btn--cart" href="' . esc_url($cartUrl) . '">';
                echo esc_html__('Add to cart', 'facefood-integration');
                echo '</a>';
            }
            echo '</div>';
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
