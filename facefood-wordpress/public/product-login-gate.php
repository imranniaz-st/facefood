<?php

if (! defined('ABSPATH')) {
    exit;
}

$popup_id = esc_attr($popup_settings['popup_id'] ?? 'facefood-product-gate-popup');
?>
<div class="facefood-product-gate-wrap">
    <div class="facefood-product-gate">
        <div class="facefood-product-gate__icon" aria-hidden="true">🍔</div>
        <h2><?php esc_html_e('Login to view our menu', 'facefood-integration'); ?></h2>
        <p><?php esc_html_e('Create a free Facefood account or log in to browse products, add toppings, and order.', 'facefood-integration'); ?></p>
        <div class="facefood-product-gate__actions">
            <button type="button" class="facefood-btn facefood-btn--primary facefood-popup-trigger" data-target="<?php echo $popup_id; ?>" data-open-tab="signup">
                <?php esc_html_e('Create account', 'facefood-integration'); ?>
            </button>
            <button type="button" class="facefood-btn facefood-btn--outline facefood-popup-trigger" data-target="<?php echo $popup_id; ?>" data-open-tab="login">
                <?php esc_html_e('Log in', 'facefood-integration'); ?>
            </button>
        </div>
        <p class="facefood-product-gate__note"><?php esc_html_e('Same account works on the Facefood mobile app.', 'facefood-integration'); ?></p>
    </div>
</div>
