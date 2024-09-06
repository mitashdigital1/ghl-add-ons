<?php
/*
 * Activation Check
 * This file ensures the plugin only activates if WooCommerce is present.
 */

function ghl_woocommerce_sync_check_woocommerce_active() {
    if (!class_exists('WooCommerce')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(__('GHL WooCommerce Sync requires WooCommerce to be installed and activated.', 'ghl-woocommerce-sync'));
    }
}
register_activation_hook(__FILE__, 'ghl_woocommerce_sync_check_woocommerce_active');
