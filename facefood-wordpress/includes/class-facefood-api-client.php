<?php

if (! defined('ABSPATH')) {
    exit;
}

class Facefood_Api_Client
{
    private string $baseUrl;
    private string $syncKey;
    private ?string $token = null;

    public function __construct()
    {
        $this->baseUrl = Facefood_Links::normalize_api_base_url(
            (string) get_option('facefood_api_base_url', 'https://app.facefood.cafe/api')
        );
        $this->syncKey = (string) get_option('facefood_sync_key', '');
    }

    public function set_token(?string $token): void
    {
        $this->token = $token;
    }

    public function login(string $email, string $password): array|WP_Error
    {
        return $this->request('POST', '/login', [
            'email' => $email,
            'password' => $password,
        ], false);
    }

    public function register(string $name, string $email, string $password, string $passwordConfirmation, string $phone = ''): array|WP_Error
    {
        $payload = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $passwordConfirmation,
        ];

        if ($phone !== '') {
            $payload['phone'] = $phone;
        }

        return $this->request('POST', '/register', $payload, false);
    }

    public function logout(): array|WP_Error
    {
        return $this->request('POST', '/logout', null, false, true);
    }

    public function get_catalog(): array|WP_Error
    {
        return $this->request('GET', '/wordpress/catalog');
    }

    public function link_entity(string $entity, int $laravelId, int $wordpressId, ?string $sku = null): array|WP_Error
    {
        return $this->request('POST', '/wordpress/link', [
            'entity' => $entity,
            'laravel_id' => $laravelId,
            'wordpress_id' => $wordpressId,
            'sku' => $sku,
        ]);
    }

    public function upsert_product(array $payload): array|WP_Error
    {
        return $this->request('POST', '/wordpress/products/upsert', $payload);
    }

    public function get_public(string $path): array|WP_Error
    {
        $path = '/' . ltrim($path, '/');
        $result = $this->request('GET', $path, null, false, false);

        if (is_wp_error($result)) {
            return $result;
        }

        if (isset($result['data']) && is_array($result['data'])) {
            return $result['data'];
        }

        return is_array($result) ? $result : [];
    }

    private function request(string $method, string $path, ?array $body = null, bool $useSyncKey = true, bool $useBearer = false): array|WP_Error
    {
        $path = '/' . ltrim($path, '/');
        $url = $this->baseUrl . $path;
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        if ($useSyncKey && $this->syncKey !== '') {
            $headers['X-Facefood-Sync-Key'] = $this->syncKey;
        }

        if ($useBearer && $this->token) {
            $headers['Authorization'] = 'Bearer ' . $this->token;
        }

        $args = [
            'method' => $method,
            'headers' => $headers,
            'timeout' => 30,
        ];

        if ($body !== null) {
            $args['body'] = wp_json_encode($body);
        }

        $response = wp_remote_request($url, $args);

        if (is_wp_error($response)) {
            return new WP_Error(
                'facefood_api_error',
                Facefood_Security::sanitize_api_message($response->get_error_message())
            );
        }

        $code = (int) wp_remote_retrieve_response_code($response);
        $raw = (string) wp_remote_retrieve_body($response);
        $decoded = json_decode($raw, true);

        if ($code >= 400) {
            $message = is_array($decoded) ? (string) ($decoded['message'] ?? '') : $raw;

            return new WP_Error(
                'facefood_api_error',
                Facefood_Security::sanitize_api_message($message),
                ['status' => $code]
            );
        }

        return is_array($decoded) ? $decoded : ['raw' => $raw];
    }
}
