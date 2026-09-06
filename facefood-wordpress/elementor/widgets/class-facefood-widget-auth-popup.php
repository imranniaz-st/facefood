<?php

if (! defined('ABSPATH')) {
    exit;
}

class Facefood_Widget_Auth_Popup extends \Elementor\Widget_Base
{
    public function get_name(): string
    {
        return 'facefood_auth_popup';
    }

    public function get_title(): string
    {
        return esc_html__('Facefood Sign Up Popup', 'facefood-integration');
    }

    public function get_icon(): string
    {
        return 'eicon-lightbox';
    }

    public function get_categories(): array
    {
        return ['facefood'];
    }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Popup', 'facefood-integration')]);

        $this->add_control('headline', [
            'label' => esc_html__('Headline', 'facefood-integration'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => esc_html__('Join Facefood', 'facefood-integration'),
        ]);

        $this->add_control('subtitle', [
            'label' => esc_html__('Subtitle', 'facefood-integration'),
            'type' => \Elementor\Controls_Manager::TEXTAREA,
            'default' => esc_html__('Create an account to order faster, save favourites, and get exclusive deals.', 'facefood-integration'),
        ]);

        $this->add_control('default_tab', [
            'label' => esc_html__('Default tab', 'facefood-integration'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'signup',
            'options' => [
                'signup' => esc_html__('Sign up', 'facefood-integration'),
                'login' => esc_html__('Log in', 'facefood-integration'),
            ],
        ]);

        $this->end_controls_section();

        $this->start_controls_section('behavior', ['label' => esc_html__('Behaviour (guests only)', 'facefood-integration')]);

        $this->add_control('auto_show', [
            'label' => esc_html__('Auto-show popup', 'facefood-integration'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
            'label_on' => esc_html__('Yes', 'facefood-integration'),
            'label_off' => esc_html__('No', 'facefood-integration'),
        ]);

        $this->add_control('delay_seconds', [
            'label' => esc_html__('Delay (seconds)', 'facefood-integration'),
            'type' => \Elementor\Controls_Manager::NUMBER,
            'default' => 3,
            'min' => 0,
            'max' => 60,
            'condition' => ['auto_show' => 'yes'],
        ]);

        $this->add_control('show_once', [
            'label' => esc_html__('Show once per browser session', 'facefood-integration'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
            'label_on' => esc_html__('Yes', 'facefood-integration'),
            'label_off' => esc_html__('No', 'facefood-integration'),
            'condition' => ['auto_show' => 'yes'],
        ]);

        $this->add_control('show_trigger_button', [
            'label' => esc_html__('Show floating button', 'facefood-integration'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
            'label_on' => esc_html__('Yes', 'facefood-integration'),
            'label_off' => esc_html__('No', 'facefood-integration'),
        ]);

        $this->add_control('trigger_text', [
            'label' => esc_html__('Button text', 'facefood-integration'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => esc_html__('Sign up', 'facefood-integration'),
            'condition' => ['show_trigger_button' => 'yes'],
        ]);

        $this->end_controls_section();
    }

    protected function render(): void
    {
        if (is_user_logged_in()) {
            return;
        }

        $settings = $this->get_settings_for_display();
        $auth = new Facefood_Auth();

        echo $auth->render_auth_popup([
            'id' => 'facefood-auth-popup-' . $this->get_id(),
            'headline' => sanitize_text_field($settings['headline'] ?? ''),
            'subtitle' => sanitize_text_field($settings['subtitle'] ?? ''),
            'default_tab' => sanitize_key($settings['default_tab'] ?? 'signup'),
            'auto_show' => ($settings['auto_show'] ?? 'yes') === 'yes',
            'delay_seconds' => (int) ($settings['delay_seconds'] ?? 3),
            'show_once' => ($settings['show_once'] ?? 'yes') === 'yes',
            'show_trigger_button' => ($settings['show_trigger_button'] ?? 'yes') === 'yes',
            'trigger_text' => sanitize_text_field($settings['trigger_text'] ?? __('Sign up', 'facefood-integration')),
        ]);
    }
}
