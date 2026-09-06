<?php

if (! defined('ABSPATH')) {
    exit;
}

class Facefood_Widget_App_Button extends \Elementor\Widget_Base
{
    public function get_name(): string
    {
        return 'facefood_app_button';
    }

    public function get_title(): string
    {
        return esc_html__('Facefood App Download', 'facefood-integration');
    }

    public function get_icon(): string
    {
        return 'eicon-download-button';
    }

    public function get_categories(): array
    {
        return ['facefood'];
    }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Content', 'facefood-integration')]);
        $this->add_control('headline', [
            'label' => esc_html__('Headline', 'facefood-integration'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'Order on the Facefood App',
        ]);
        $this->add_control('subheadline', [
            'label' => esc_html__('Subheadline', 'facefood-integration'),
            'type' => \Elementor\Controls_Manager::TEXTAREA,
            'default' => 'Download our app for faster ordering, deals, and delivery tracking.',
        ]);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $playUrl = get_option('facefood_app_play_url', '');
        $storeUrl = get_option('facefood_app_store_url', '');

        echo '<div class="facefood-app-cta">';
        echo '<h2>' . esc_html($settings['headline'] ?? '') . '</h2>';
        echo '<p>' . esc_html($settings['subheadline'] ?? '') . '</p>';
        echo '<div class="facefood-app-cta__buttons">';

        if ($playUrl) {
            echo '<a class="facefood-btn facefood-btn--primary" href="' . esc_url($playUrl) . '" target="_blank" rel="noopener">Google Play</a>';
        }
        if ($storeUrl) {
            echo '<a class="facefood-btn facefood-btn--secondary" href="' . esc_url($storeUrl) . '" target="_blank" rel="noopener">App Store</a>';
        }
        if (! $playUrl && ! $storeUrl) {
            echo '<span class="facefood-empty">' . esc_html__('Add app store URLs in Facefood → Settings.', 'facefood-integration') . '</span>';
        }

        echo '</div></div>';
    }
}
