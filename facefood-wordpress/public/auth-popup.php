<?php

if (! defined('ABSPATH')) {
    exit;
}

$popup_id = esc_attr($popup_settings['id'] ?? 'facefood-auth-popup');
$headline = esc_html($popup_settings['headline'] ?? __('Join Facefood', 'facefood-integration'));
$subtitle = esc_html($popup_settings['subtitle'] ?? __('Create an account to order faster, save favourites, and get exclusive deals.', 'facefood-integration'));
$default_tab = ($popup_settings['default_tab'] ?? 'signup') === 'login' ? 'login' : 'signup';
$auto_show = ! empty($popup_settings['auto_show']) ? '1' : '0';
$delay = max(0, (int) ($popup_settings['delay_seconds'] ?? 3));
$show_once = ! empty($popup_settings['show_once']) ? '1' : '0';
$show_trigger = ! empty($popup_settings['show_trigger_button']);
$trigger_text = esc_html($popup_settings['trigger_text'] ?? __('Sign up', 'facefood-integration'));
?>
<?php if ($show_trigger) : ?>
    <button type="button" class="facefood-popup-trigger" data-target="<?php echo $popup_id; ?>">
        <?php echo $trigger_text; ?>
    </button>
<?php endif; ?>

<div
    class="facefood-popup"
    id="<?php echo $popup_id; ?>"
    data-auto-show="<?php echo esc_attr($auto_show); ?>"
    data-delay="<?php echo esc_attr((string) $delay); ?>"
    data-show-once="<?php echo esc_attr($show_once); ?>"
    data-default-tab="<?php echo esc_attr($default_tab); ?>"
    hidden
    aria-hidden="true"
>
    <div class="facefood-popup__overlay" tabindex="-1"></div>
    <div class="facefood-popup__dialog" role="dialog" aria-modal="true" aria-labelledby="<?php echo $popup_id; ?>-title">
        <button type="button" class="facefood-popup__close" aria-label="<?php esc_attr_e('Close', 'facefood-integration'); ?>">&times;</button>

        <div class="facefood-popup__hero">
            <div class="facefood-popup__badge"><?php esc_html_e('Facefood', 'facefood-integration'); ?></div>
            <div class="facefood-popup__icons" aria-hidden="true">
                <span>🍔</span><span>🌯</span><span>🍕</span>
            </div>
            <h2 id="<?php echo $popup_id; ?>-title"><?php echo $headline; ?></h2>
            <p><?php echo $subtitle; ?></p>
        </div>

        <div class="facefood-popup__tabs" role="tablist">
            <button type="button" class="facefood-popup__tab<?php echo $default_tab === 'signup' ? ' is-active' : ''; ?>" data-tab="signup" role="tab">
                <?php esc_html_e('Sign up', 'facefood-integration'); ?>
            </button>
            <button type="button" class="facefood-popup__tab<?php echo $default_tab === 'login' ? ' is-active' : ''; ?>" data-tab="login" role="tab">
                <?php esc_html_e('Log in', 'facefood-integration'); ?>
            </button>
        </div>

        <div class="facefood-popup__panel<?php echo $default_tab === 'signup' ? ' is-active' : ''; ?>" data-panel="signup" role="tabpanel">
            <p class="facefood-popup__hint"><?php esc_html_e('One account for website & mobile app.', 'facefood-integration'); ?></p>
            <?php include FACEFOOD_PLUGIN_DIR . 'public/partials/register-form-inner.php'; ?>
        </div>

        <div class="facefood-popup__panel<?php echo $default_tab === 'login' ? ' is-active' : ''; ?>" data-panel="login" role="tabpanel">
            <p class="facefood-popup__hint"><?php esc_html_e('Welcome back! Use your Facefood app credentials.', 'facefood-integration'); ?></p>
            <?php include FACEFOOD_PLUGIN_DIR . 'public/partials/login-form-inner.php'; ?>
        </div>

        <p class="facefood-popup__footer"><?php esc_html_e('Fresh food. Fast delivery. Made for you.', 'facefood-integration'); ?></p>
    </div>
</div>
