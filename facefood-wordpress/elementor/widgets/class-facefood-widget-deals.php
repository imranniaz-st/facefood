<?php

if (! defined('ABSPATH')) {
    exit;
}

class Facefood_Widget_Deals extends Facefood_Elementor_Widget_Base
{
    public function get_name(): string
    {
        return 'facefood_deals';
    }

    public function get_title(): string
    {
        return esc_html__('Facefood Deals', 'facefood-integration');
    }

    public function get_icon(): string
    {
        return 'eicon-price-table';
    }

    public function get_categories(): array
    {
        return ['facefood'];
    }

    protected function render(): void
    {
        $this->render_if_logged_in_or_gate(function (): void {
            $deals = $this->fetch_api('/deals');

            echo '<div class="facefood-deals">';

        if (is_wp_error($deals)) {
            echo '<p class="facefood-empty">' . esc_html(Facefood_Security::sanitize_api_message($deals->get_error_message())) . '</p>';
            echo '</div>';

            return;
        }

        if (empty($deals)) {
            echo '<p class="facefood-empty">' . esc_html__('No active deals.', 'facefood-integration') . '</p>';
        } else {
            foreach ($deals as $deal) {
                if (! is_array($deal)) {
                    continue;
                }

                $productId = (int) ($deal['product_id'] ?? 0);
                $productUrl = Facefood_Links::product_url($productId);
                $cartUrl = Facefood_Links::add_to_cart_url($productId);
                $title = esc_html($deal['title'] ?? '');
                $price = esc_html($this->format_price((float) ($deal['deal_price'] ?? 0)));
                $original = isset($deal['original_price']) ? esc_html($this->format_price((float) $deal['original_price'])) : '';
                $color = esc_attr($deal['badge_color'] ?? '#0D47A1');
                $image = esc_url($deal['image_url'] ?? '');

                echo '<article class="facefood-deal" style="--facefood-deal-color:' . $color . '">';

                if ($image && $productUrl) {
                    echo '<a href="' . esc_url($productUrl) . '">';
                    echo '<img class="facefood-deal__image" src="' . $image . '" alt="' . $title . '" loading="lazy">';
                    echo '</a>';
                } elseif ($image) {
                    echo '<img class="facefood-deal__image" src="' . $image . '" alt="' . $title . '" loading="lazy">';
                }

                echo '<div class="facefood-deal__body">';
                echo '<h3>';
                if ($productUrl) {
                    echo '<a href="' . esc_url($productUrl) . '">' . $title . '</a>';
                } else {
                    echo $title;
                }
                echo '</h3>';
                echo '<p>' . esc_html(wp_strip_all_tags($deal['description'] ?? '')) . '</p>';
                echo '<div class="facefood-deal__prices">';
                echo '<strong>' . $price . '</strong>';
                if ($original) {
                    echo '<del>' . $original . '</del>';
                }
                echo '</div>';

                if ($productUrl) {
                    echo '<div class="facefood-card__actions">';
                    echo '<a class="facefood-btn facefood-btn--outline" href="' . esc_url($productUrl) . '">';
                    echo esc_html__('View deal', 'facefood-integration');
                    echo '</a>';
                    if ($cartUrl) {
                        echo '<a class="facefood-btn facefood-btn--primary" href="' . esc_url($cartUrl) . '">';
                        echo esc_html__('Order now', 'facefood-integration');
                        echo '</a>';
                    }
                    echo '</div>';
                }

                echo '</div></article>';
            }
        }

        echo '</div>';
        });
    }
}
