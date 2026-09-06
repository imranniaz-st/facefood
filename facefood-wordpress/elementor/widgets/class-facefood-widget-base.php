<?php

if (! defined('ABSPATH')) {
    exit;
}

abstract class Facefood_Elementor_Widget_Base extends \Elementor\Widget_Base
{
    protected function fetch_api(string $path): array|WP_Error
    {
        $client = new Facefood_Api_Client();
        $response = $client->get_public($path);

        if (is_wp_error($response)) {
            return $response;
        }

        if (isset($response['data']) && is_array($response['data'])) {
            return $response['data'];
        }

        return is_array($response) ? $response : [];
    }

    protected function format_price(float $amount): string
    {
        return Facefood_Render::format_price($amount);
    }
}
