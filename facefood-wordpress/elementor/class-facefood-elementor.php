<?php

if (! defined('ABSPATH')) {
    exit;
}

class Facefood_Elementor
{
    /** @var bool */
    private static $registered = false;

    public function __construct()
    {
        add_action('elementor/elements/categories_registered', [$this, 'register_category']);
        add_action('elementor/widgets/register', [$this, 'register_widgets']);
        add_action('elementor/widgets/widgets_registered', [$this, 'register_widgets_legacy']);
    }

    public function register_category($elements_manager): void
    {
        $elements_manager->add_category('facefood', [
            'title' => esc_html__('Facefood', 'facefood-integration'),
            'icon' => 'eicon-food',
        ]);
    }

    public function register_widgets($widgets_manager): void
    {
        if (self::$registered) {
            return;
        }

        self::$registered = true;

        $widgets = $this->widget_instances();

        foreach ($widgets as $widget) {
            if (method_exists($widgets_manager, 'register')) {
                $widgets_manager->register($widget);
            } elseif (method_exists($widgets_manager, 'register_widget_type')) {
                $widgets_manager->register_widget_type($widget);
            }
        }
    }

    public function register_widgets_legacy($widgets_manager): void
    {
        $this->register_widgets($widgets_manager);
    }

    /**
     * @return array<int, object>
     */
    private function widget_instances(): array
    {
        return [
            new Facefood_Widget_Menu_Grid(),
            new Facefood_Widget_Category_Menu(),
            new Facefood_Widget_Deals(),
            new Facefood_Widget_App_Button(),
            new Facefood_Widget_Auth(),
            new Facefood_Widget_Auth_Popup(),
            new Facefood_Widget_Woo_Products(),
        ];
    }
}
