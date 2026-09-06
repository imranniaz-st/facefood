<?php

if (! defined('ABSPATH')) {
    exit;
}
?>
<div class="facefood-auth facefood-auth--login">
    <h2><?php esc_html_e('Login to Facefood', 'facefood-integration'); ?></h2>
    <p class="facefood-auth__note"><?php esc_html_e('Use the same email and password as the mobile app.', 'facefood-integration'); ?></p>
    <?php include FACEFOOD_PLUGIN_DIR . 'public/partials/login-form-inner.php'; ?>
</div>
