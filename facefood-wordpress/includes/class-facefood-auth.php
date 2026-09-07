<?php

if (! defined('ABSPATH')) {
    exit;
}

class Facefood_Auth
{
    public const META_TOKEN = '_facefood_api_token';
    public const META_LARAVEL_ID = '_facefood_laravel_user_id';
    public const META_USER_JSON = '_facefood_user_json';

    public function __construct()
    {
        add_shortcode('facefood_login', [$this, 'render_login_shortcode']);
        add_shortcode('facefood_register', [$this, 'render_register_shortcode']);
        add_shortcode('facefood_account', [$this, 'render_account_shortcode']);
        add_shortcode('facefood_auth_popup', [$this, 'render_auth_popup_shortcode']);

        add_action('wp_ajax_facefood_login', [$this, 'ajax_login']);
        add_action('wp_ajax_nopriv_facefood_login', [$this, 'ajax_login']);
        add_action('wp_ajax_facefood_register', [$this, 'ajax_register']);
        add_action('wp_ajax_nopriv_facefood_register', [$this, 'ajax_register']);
        add_action('wp_ajax_facefood_logout', [$this, 'ajax_logout']);
        add_action('wp_ajax_nopriv_facefood_logout', [$this, 'ajax_logout']);

        add_action('wp_logout', [$this, 'clear_session_meta']);
        add_action('wp', [$this, 'maybe_gate_woocommerce_products']);
        add_filter('body_class', [$this, 'add_body_class']);
    }

    public static function must_login_for_products(): bool
    {
        return get_option('facefood_require_login_for_products', 'yes') === 'yes';
    }

    public static function should_show_product_gate(): bool
    {
        return self::must_login_for_products() && ! is_user_logged_in();
    }

    public function add_body_class(array $classes): array
    {
        if ($this->is_gated_wc_page()) {
            $classes[] = 'facefood-wc-gated';
        }

        return $classes;
    }

    public function maybe_gate_woocommerce_products(): void
    {
        if (! self::should_show_product_gate() || ! class_exists('WooCommerce')) {
            return;
        }

        if (! $this->is_gated_wc_page()) {
            return;
        }

        add_action('woocommerce_before_main_content', [$this, 'echo_product_gate'], 2);
        add_action('woocommerce_before_single_product', [$this, 'echo_product_gate'], 2);
    }

    private function is_gated_wc_page(): bool
    {
        if (! self::should_show_product_gate()) {
            return false;
        }

        return function_exists('is_shop') && (
            is_shop()
            || is_singular('product')
            || is_product_taxonomy()
        );
    }

    public function echo_product_gate(): void
    {
        static $shown = false;
        if ($shown) {
            return;
        }
        $shown = true;

        echo self::render_product_gate('facefood-wc-gate-popup');
    }

    public static function render_product_gate(string $popupId = 'facefood-product-gate-popup'): string
    {
        if (! self::should_show_product_gate()) {
            return '';
        }

        ob_start();
        $popup_settings = ['popup_id' => $popupId];
        include FACEFOOD_PLUGIN_DIR . 'public/product-login-gate.php';
        echo self::render_auth_popup([
            'id' => $popupId,
            'headline' => __('Join Facefood', 'facefood-integration'),
            'subtitle' => __('Create an account to browse our menu and place orders.', 'facefood-integration'),
            'default_tab' => 'signup',
            'auto_show' => true,
            'delay_seconds' => 0,
            'show_once' => false,
            'show_trigger_button' => false,
            'force_open' => true,
        ]);

        return (string) ob_get_clean();
    }

    public static function current_laravel_user(): ?array
    {
        $userId = get_current_user_id();
        if (! $userId) {
            return null;
        }

        $json = get_user_meta($userId, self::META_USER_JSON, true);
        if (! is_string($json) || $json === '') {
            return null;
        }

        $decoded = json_decode($json, true);

        return is_array($decoded) ? $decoded : null;
    }

    public static function current_token(): ?string
    {
        $userId = get_current_user_id();
        if (! $userId) {
            return null;
        }

        $token = get_user_meta($userId, self::META_TOKEN, true);

        return is_string($token) && $token !== '' ? $token : null;
    }

    public function render_login_shortcode(): string
    {
        if (is_user_logged_in()) {
            return $this->render_account_panel();
        }

        ob_start();
        include FACEFOOD_PLUGIN_DIR . 'public/login-form.php';

        return (string) ob_get_clean();
    }

    public function render_register_shortcode(): string
    {
        if (is_user_logged_in()) {
            return $this->render_account_panel();
        }

        ob_start();
        include FACEFOOD_PLUGIN_DIR . 'public/register-form.php';

        return (string) ob_get_clean();
    }

    public function render_account_shortcode(): string
    {
        if (! is_user_logged_in()) {
            ob_start();
            include FACEFOOD_PLUGIN_DIR . 'public/login-form.php';

            return (string) ob_get_clean();
        }

        return $this->render_account_panel();
    }

    public function render_auth_popup_shortcode($atts = []): string
    {
        $atts = shortcode_atts([
            'headline' => __('Join Facefood', 'facefood-integration'),
            'subtitle' => __('Create an account to order faster, save favourites, and get exclusive deals.', 'facefood-integration'),
            'default_tab' => 'signup',
            'auto_show' => 'yes',
            'delay' => '3',
            'show_once' => 'yes',
            'trigger' => 'yes',
            'trigger_text' => __('Sign up', 'facefood-integration'),
        ], $atts, 'facefood_auth_popup');

        return self::render_auth_popup([
            'id' => 'facefood-auth-popup-shortcode',
            'headline' => sanitize_text_field($atts['headline']),
            'subtitle' => sanitize_text_field($atts['subtitle']),
            'default_tab' => sanitize_key($atts['default_tab']),
            'auto_show' => $atts['auto_show'] === 'yes',
            'delay_seconds' => (int) $atts['delay'],
            'show_once' => $atts['show_once'] === 'yes',
            'show_trigger_button' => $atts['trigger'] === 'yes',
            'trigger_text' => sanitize_text_field($atts['trigger_text']),
        ]);
    }

    public static function render_auth_popup(array $settings = []): string
    {
        if (is_user_logged_in()) {
            return '';
        }

        $popup_settings = wp_parse_args($settings, [
            'id' => 'facefood-auth-popup',
            'headline' => __('Join Facefood', 'facefood-integration'),
            'subtitle' => __('Create an account to order faster, save favourites, and get exclusive deals.', 'facefood-integration'),
            'default_tab' => 'signup',
            'auto_show' => true,
            'delay_seconds' => 3,
            'show_once' => true,
            'show_trigger_button' => true,
            'trigger_text' => __('Sign up', 'facefood-integration'),
            'force_open' => false,
        ]);

        ob_start();
        include FACEFOOD_PLUGIN_DIR . 'public/auth-popup.php';

        return (string) ob_get_clean();
    }

    public function ajax_login(): void
    {
        Facefood_Security::verify_ajax_nonce();

        if (! Facefood_Security::rate_limit('login', 8, 300)) {
            wp_send_json_error(['message' => __('Too many attempts. Please wait a few minutes.', 'facefood-integration')], 429);
        }

        $email = sanitize_email(wp_unslash($_POST['email'] ?? ''));
        $password = (string) wp_unslash($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            wp_send_json_error(['message' => __('Email and password are required.', 'facefood-integration')], 422);
        }

        $api = new Facefood_Api_Client();
        $response = $api->login($email, $password);

        if (is_wp_error($response)) {
            wp_send_json_error(['message' => Facefood_Security::sanitize_api_message($response->get_error_message())], 401);
        }

        $this->establish_wp_session($response);

        wp_send_json_success([
            'message' => __('Logged in successfully.', 'facefood-integration'),
            'redirect' => esc_url_raw(home_url('/')),
        ]);
    }

    public function ajax_register(): void
    {
        Facefood_Security::verify_ajax_nonce();

        if (! Facefood_Security::rate_limit('register', 5, 600)) {
            wp_send_json_error(['message' => __('Too many registration attempts. Please try again later.', 'facefood-integration')], 429);
        }

        $name = sanitize_text_field(wp_unslash($_POST['name'] ?? ''));
        $email = sanitize_email(wp_unslash($_POST['email'] ?? ''));
        $phone = sanitize_text_field(wp_unslash($_POST['phone'] ?? ''));
        $password = (string) wp_unslash($_POST['password'] ?? '');
        $passwordConfirmation = (string) wp_unslash($_POST['password_confirmation'] ?? '');

        if ($name === '' || $email === '' || $password === '') {
            wp_send_json_error(['message' => __('Name, email and password are required.', 'facefood-integration')], 422);
        }

        if ($password !== $passwordConfirmation) {
            wp_send_json_error(['message' => __('Passwords do not match.', 'facefood-integration')], 422);
        }

        $api = new Facefood_Api_Client();
        $response = $api->register($name, $email, $password, $passwordConfirmation, $phone);

        if (is_wp_error($response)) {
            wp_send_json_error(['message' => Facefood_Security::sanitize_api_message($response->get_error_message())], 422);
        }

        $this->establish_wp_session($response);

        wp_send_json_success([
            'message' => __('Account created successfully.', 'facefood-integration'),
            'redirect' => esc_url_raw(home_url('/')),
        ]);
    }

    public function ajax_logout(): void
    {
        Facefood_Security::verify_ajax_nonce();

        $token = self::current_token();
        if ($token) {
            $api = new Facefood_Api_Client();
            $api->set_token($token);
            $api->logout();
        }

        $this->clear_session_meta();
        wp_logout();

        wp_send_json_success([
            'message' => __('Logged out.', 'facefood-integration'),
            'redirect' => esc_url_raw(home_url('/')),
        ]);
    }

    public function clear_session_meta(): void
    {
        $userId = get_current_user_id();
        if (! $userId) {
            return;
        }

        delete_user_meta($userId, self::META_TOKEN);
        delete_user_meta($userId, self::META_LARAVEL_ID);
        delete_user_meta($userId, self::META_USER_JSON);
    }

    private function establish_wp_session(array $response): void
    {
        $user = $response['user'] ?? null;
        $token = $response['token'] ?? '';

        if (! is_array($user) || ! is_string($token) || $token === '') {
            wp_send_json_error(['message' => __('Invalid login response.', 'facefood-integration')], 500);
        }

        $email = sanitize_email((string) ($user['email'] ?? ''));
        $name = sanitize_text_field((string) ($user['name'] ?? 'Facefood User'));

        if ($email === '') {
            wp_send_json_error(['message' => __('Invalid user email.', 'facefood-integration')], 500);
        }

        $wpUser = get_user_by('email', $email);
        if (! $wpUser) {
            $username = sanitize_user(current(explode('@', $email)), true);
            if ($username === '' || username_exists($username)) {
                $username = 'facefood_' . wp_generate_password(8, false, false);
            }

            $wpUserId = wp_create_user($username, wp_generate_password(24, true, true), $email);
            if (is_wp_error($wpUserId)) {
                wp_send_json_error(['message' => Facefood_Security::sanitize_api_message($wpUserId->get_error_message())], 500);
            }

            wp_update_user([
                'ID' => $wpUserId,
                'display_name' => $name,
                'first_name' => $name,
                'role' => 'customer',
            ]);

            $wpUser = get_user_by('id', $wpUserId);
        }

        if (! $wpUser instanceof WP_User) {
            wp_send_json_error(['message' => __('Could not create WordPress session.', 'facefood-integration')], 500);
        }

        wp_set_current_user($wpUser->ID);
        wp_set_auth_cookie($wpUser->ID, true, is_ssl());

        update_user_meta($wpUser->ID, self::META_TOKEN, $token);
        update_user_meta($wpUser->ID, self::META_LARAVEL_ID, (int) ($user['id'] ?? 0));
        update_user_meta($wpUser->ID, self::META_USER_JSON, wp_json_encode($user));
    }

    private function render_account_panel(): string
    {
        $laravelUser = self::current_laravel_user();
        $name = Facefood_Security::esc_text((string) ($laravelUser['name'] ?? wp_get_current_user()->display_name));
        $email = Facefood_Security::esc_text((string) ($laravelUser['email'] ?? wp_get_current_user()->user_email));

        ob_start();
        ?>
        <div class="facefood-auth facefood-auth--account">
            <h2><?php echo esc_html__('My Facefood Account', 'facefood-integration'); ?></h2>
            <p><?php echo esc_html__('Signed in as', 'facefood-integration'); ?> <strong><?php echo esc_html($name); ?></strong></p>
            <p class="facefood-auth__email"><?php echo esc_html($email); ?></p>
            <p class="facefood-auth__note"><?php echo esc_html__('Same account works in the Facefood mobile app.', 'facefood-integration'); ?></p>
            <button type="button" class="facefood-btn facefood-btn--secondary facefood-logout-btn"><?php echo esc_html__('Log out', 'facefood-integration'); ?></button>
        </div>
        <?php

        return (string) ob_get_clean();
    }
}
