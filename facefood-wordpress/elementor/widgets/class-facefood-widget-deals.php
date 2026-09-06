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
                $title = esc_html($deal['title'] ?? '');
                $price = esc_html($this->format_price((float) ($deal['deal_price'] ?? 0)));
                $original = isset($deal['original_price']) ? esc_html($this->format_price((float) $deal['original_price'])) : '';
                $color = esc_attr($deal['badge_color'] ?? '#0D47A1');
                $image = esc_url($deal['image_url'] ?? '');

                echo '<article class="facefood-deal" style="--facefood-deal-color:' . $color . '">';
                if ($image) {
                    echo '<img class="facefood-deal__image" src="' . $image . '" alt="' . $title . '" loading="lazy">';
                }
                echo '<div class="facefood-deal__body">';
                echo '<h3>' . $title . '</h3>';
                echo '<p>' . esc_html(wp_strip_all_tags($deal['description'] ?? '')) . '</p>';
                echo '<div class="facefood-deal__prices">';
                echo '<strong>' . $price . '</strong>';
                if ($original) {
                    echo '<del>' . $original . '</del>';
                }
                echo '</div></div></article>';
            }
        }

        echo '</div>';
    }
}
