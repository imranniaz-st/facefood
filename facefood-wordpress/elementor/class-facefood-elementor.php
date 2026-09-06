<?php

if (! defined('ABSPATH')) {
    exit;
}

class Facefood_Elementor
{
    public function __construct()
    {
        add_action('elementor/widgets/register', [$this, 'register_widgets']);
        add_action('elementor/elements/categories_registered', [$this, 'register_category']);
    }

    public function register_category($elements_manager): void
    {
        $elements_manager->add_category('facefood', [
            'title' => esc_html__('Facefood', 'facefood-integration'),
            'icon' => 'fa fa-cutlery',
        ]);
    }

    public function register_widgets($widgets_manager): void
    {
        $widgets_manager->register(new Facefood_Widget_Menu_Grid());
        $widgets_manager->register(new Facefood_Widget_Category_Menu());
        $widgets_manager->register(new Facefood_Widget_Deals());
        $widgets_manager->register(new Facefood_Widget_App_Button());
        $widgets_manager->register(new Facefood_Widget_Auth());
        $widgets_manager->register(new Facefood_Widget_Auth_Popup());
    }
}
