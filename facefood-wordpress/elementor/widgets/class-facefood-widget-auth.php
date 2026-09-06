<?php

if (! defined('ABSPATH')) {
    exit;
}

class Facefood_Widget_Auth extends \Elementor\Widget_Base
{
    public function get_name(): string
    {
        return 'facefood_auth';
    }

    public function get_title(): string
    {
        return esc_html__('Facefood Login / Register', 'facefood-integration');
    }

    public function get_icon(): string
    {
        return 'eicon-lock-user';
    }

    public function get_categories(): array
    {
        return ['facefood'];
    }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Content', 'facefood-integration')]);
        $this->add_control('mode', [
            'label' => esc_html__('Mode', 'facefood-integration'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'login',
            'options' => [
                'login' => esc_html__('Login', 'facefood-integration'),
                'register' => esc_html__('Register', 'facefood-integration'),
                'account' => esc_html__('Account (auto)', 'facefood-integration'),
            ],
        ]);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $auth = new Facefood_Auth();
        $mode = $this->get_settings_for_display()['mode'] ?? 'login';

        if ($mode === 'register') {
            echo $auth->render_register_shortcode();

            return;
        }

        if ($mode === 'account') {
            echo $auth->render_account_shortcode();

            return;
        }

        echo $auth->render_login_shortcode();
    }
}
