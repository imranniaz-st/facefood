<?php

if (! defined('ABSPATH')) {
    exit;
}
?>
<div class="facefood-auth facefood-auth--login">
    <h2><?php esc_html_e('Login to Facefood', 'facefood-integration'); ?></h2>
    <p class="facefood-auth__note"><?php esc_html_e('Use the same email and password as the mobile app.', 'facefood-integration'); ?></p>
    <form class="facefood-auth-form" data-action="login" novalidate>
        <?php wp_nonce_field('facefood_public_nonce', 'facefood_nonce'); ?>
        <label>
            <span><?php esc_html_e('Email', 'facefood-integration'); ?></span>
            <input type="email" name="email" required autocomplete="email">
        </label>
        <label>
            <span><?php esc_html_e('Password', 'facefood-integration'); ?></span>
            <input type="password" name="password" required autocomplete="current-password">
        </label>
        <button type="submit" class="facefood-btn facefood-btn--primary"><?php esc_html_e('Log in', 'facefood-integration'); ?></button>
        <p class="facefood-auth__message" aria-live="polite"></p>
    </form>
</div>
