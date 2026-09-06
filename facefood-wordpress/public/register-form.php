<?php

if (! defined('ABSPATH')) {
    exit;
}
?>
<div class="facefood-auth facefood-auth--register">
    <h2><?php esc_html_e('Create Facefood Account', 'facefood-integration'); ?></h2>
    <p class="facefood-auth__note"><?php esc_html_e('One account for the website and mobile app.', 'facefood-integration'); ?></p>
    <form class="facefood-auth-form" data-action="register" novalidate>
        <?php wp_nonce_field('facefood_public_nonce', 'facefood_nonce'); ?>
        <label>
            <span><?php esc_html_e('Full name', 'facefood-integration'); ?></span>
            <input type="text" name="name" required autocomplete="name">
        </label>
        <label>
            <span><?php esc_html_e('Email', 'facefood-integration'); ?></span>
            <input type="email" name="email" required autocomplete="email">
        </label>
        <label>
            <span><?php esc_html_e('Phone (optional)', 'facefood-integration'); ?></span>
            <input type="tel" name="phone" autocomplete="tel">
        </label>
        <label>
            <span><?php esc_html_e('Password', 'facefood-integration'); ?></span>
            <input type="password" name="password" required autocomplete="new-password" minlength="8">
        </label>
        <label>
            <span><?php esc_html_e('Confirm password', 'facefood-integration'); ?></span>
            <input type="password" name="password_confirmation" required autocomplete="new-password" minlength="8">
        </label>
        <button type="submit" class="facefood-btn facefood-btn--primary"><?php esc_html_e('Create account', 'facefood-integration'); ?></button>
        <p class="facefood-auth__message" aria-live="polite"></p>
    </form>
</div>
