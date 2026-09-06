<?php

if (! defined('ABSPATH')) {
    exit;
}

class Facefood_Autoloader
{
    public static function register(): void
    {
        spl_autoload_register(static function (string $class): void {
            if (strpos($class, 'Facefood_') !== 0) {
                return;
            }

            $map = [
                'Facefood_Plugin' => 'class-facefood-plugin.php',
                'Facefood_Activator' => 'class-facefood-activator.php',
                'Facefood_Deactivator' => 'class-facefood-deactivator.php',
                'Facefood_Admin' => 'class-facefood-admin.php',
                'Facefood_Api_Client' => 'class-facefood-api-client.php',
                'Facefood_Sync' => 'class-facefood-sync.php',
                'Facefood_WooCommerce' => 'class-facefood-woocommerce.php',
                'Facefood_Menu_Data' => 'class-facefood-menu-data.php',
                'Facefood_Security' => 'class-facefood-security.php',
                'Facefood_Render' => 'class-facefood-render.php',
                'Facefood_Auth' => 'class-facefood-auth.php',
                'Facefood_Elementor' => '../elementor/class-facefood-elementor.php',
                'Facefood_Elementor_Widget_Base' => '../elementor/widgets/class-facefood-widget-base.php',
                'Facefood_Widget_Menu_Grid' => '../elementor/widgets/class-facefood-widget-menu-grid.php',
                'Facefood_Widget_Category_Menu' => '../elementor/widgets/class-facefood-widget-category-menu.php',
                'Facefood_Widget_Deals' => '../elementor/widgets/class-facefood-widget-deals.php',
                'Facefood_Widget_App_Button' => '../elementor/widgets/class-facefood-widget-app-button.php',
                'Facefood_Widget_Auth' => '../elementor/widgets/class-facefood-widget-auth.php',
            ];

            if (! isset($map[$class])) {
                return;
            }

            $file = FACEFOOD_PLUGIN_DIR . 'includes/' . $map[$class];
            if (file_exists($file)) {
                require_once $file;
            }
        });
    }
}
