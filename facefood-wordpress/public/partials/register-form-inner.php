<?php

if (! defined('ABSPATH')) {
    exit;
}
?>
<form class="facefood-auth-form" data-action="register" novalidate>
    <?php wp_nonce_field('facefood_public_nonce', 'facefood_nonce'); ?>
    <label>
        <span><?php esc_html_e('Full name', 'facefood-integration'); ?></span>
        <input type="text" name="name" required autocomplete="name" placeholder="<?php esc_attr_e('Your name', 'facefood-integration'); ?>">
    </label>
    <label>
        <span><?php esc_html_e('Email', 'facefood-integration'); ?></span>
        <input type="email" name="email" required autocomplete="email" placeholder="<?php esc_attr_e('you@example.com', 'facefood-integration'); ?>">
    </label>
    <label>
        <span><?php esc_html_e('Phone (optional)', 'facefood-integration'); ?></span>
        <input type="tel" name="phone" autocomplete="tel" placeholder="<?php esc_attr_e('+92 300 1234567', 'facefood-integration'); ?>">
    </label>
    <label>
        <span><?php esc_html_e('Password', 'facefood-integration'); ?></span>
        <input type="password" name="password" required autocomplete="new-password" minlength="8" placeholder="<?php esc_attr_e('Min. 8 characters', 'facefood-integration'); ?>">
    </label>
    <label>
        <span><?php esc_html_e('Confirm password', 'facefood-integration'); ?></span>
        <input type="password" name="password_confirmation" required autocomplete="new-password" minlength="8" placeholder="<?php esc_attr_e('Repeat password', 'facefood-integration'); ?>">
    </label>
    <button type="submit" class="facefood-btn facefood-btn--primary"><?php esc_html_e('Create account', 'facefood-integration'); ?></button>
    <p class="facefood-auth__message" aria-live="polite"></p>
</form>
