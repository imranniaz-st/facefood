<?php

if (! defined('ABSPATH')) {
    exit;
}

class Facefood_Security
{
    public static function esc_text(string $value): string
    {
        return esc_html(sanitize_text_field($value));
    }

    public static function esc_url(string $value): string
    {
        return esc_url($value);
    }

    public static function esc_attr(string $value): string
    {
        return esc_attr(sanitize_text_field($value));
    }

    public static function esc_description(string $value): string
    {
        return wp_kses_post($value);
    }

    public static function sanitize_api_message(string $message): string
    {
        $message = wp_strip_all_tags($message);
        $message = preg_replace('#/[\\w./\\\\-]+\\.(php|env|json|xml|yml|yaml)#i', '', $message) ?? $message;
        $message = preg_replace('#/home/[\\w./-]+#i', '', $message) ?? $message;

        if (preg_match('/(Symfony|Illuminate|Stack trace|vendor|SQLSTATE|could not be found\\.)/i', $message)) {
            if (stripos($message, 'could not be found') !== false) {
                return __('API endpoint not available. Please contact the site administrator.', 'facefood-integration');
            }

            return __('Something went wrong. Please try again.', 'facefood-integration');
        }

        return trim($message) !== '' ? trim($message) : __('Something went wrong. Please try again.', 'facefood-integration');
    }

    public static function verify_ajax_nonce(): void
    {
        if (! check_ajax_referer('facefood_public_nonce', 'nonce', false)) {
            wp_send_json_error([
                'message' => __('Security check failed. Refresh the page and try again.', 'facefood-integration'),
            ], 403);
        }
    }

    public static function rate_limit(string $action, int $maxAttempts = 10, int $windowSeconds = 300): bool
    {
        $ip = self::client_ip();
        $key = 'facefood_rl_' . md5($action . '|' . $ip);
        $count = (int) get_transient($key);

        if ($count >= $maxAttempts) {
            return false;
        }

        set_transient($key, $count + 1, $windowSeconds);

        return true;
    }

    public static function client_ip(): string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        return sanitize_text_field((string) $ip);
    }
}
