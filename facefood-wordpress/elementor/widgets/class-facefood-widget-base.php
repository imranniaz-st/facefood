<?php

if (! defined('ABSPATH')) {
    exit;
}

abstract class Facefood_Elementor_Widget_Base extends \Elementor\Widget_Base
{
    protected function fetch_api(string $path): array
    {
        $client = new Facefood_Api_Client();
        $response = $client->get_public($path);

        if (is_wp_error($response)) {
            return [];
        }

        return $response['data'] ?? $response;
    }

    protected function format_price(float $amount): string
    {
        return 'Rs. ' . number_format($amount, 0);
    }
}
