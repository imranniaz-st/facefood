<?php

if (! defined('ABSPATH')) {
    exit;
}
?>
<form class="facefood-auth-form" data-action="login" novalidate>
    <?php wp_nonce_field('facefood_public_nonce', 'facefood_nonce'); ?>
    <label>
        <span><?php esc_html_e('Email', 'facefood-integration'); ?></span>
        <input type="email" name="email" required autocomplete="email" placeholder="<?php esc_attr_e('you@example.com', 'facefood-integration'); ?>">
    </label>
    <label>
        <span><?php esc_html_e('Password', 'facefood-integration'); ?></span>
        <input type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
    </label>
    <button type="submit" class="facefood-btn facefood-btn--primary"><?php esc_html_e('Log in', 'facefood-integration'); ?></button>
    <p class="facefood-auth__message" aria-live="polite"></p>
</form>
