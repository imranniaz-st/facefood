<?php

if (! defined('ABSPATH')) {
    exit;
}
?>
<div class="facefood-auth facefood-auth--register">
    <h2><?php esc_html_e('Create Facefood Account', 'facefood-integration'); ?></h2>
    <p class="facefood-auth__note"><?php esc_html_e('One account for the website and mobile app.', 'facefood-integration'); ?></p>
    <?php include FACEFOOD_PLUGIN_DIR . 'public/partials/register-form-inner.php'; ?>
</div>
