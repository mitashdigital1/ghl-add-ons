<?php
/*
Plugin Name: GHL WooCommerce Sync by Mitash
Description: "GHL WooCommerce Sync by Mitash" is a powerful plugin designed to seamlessly integrate your WooCommerce store with Go High Level (GHL). This plugin ensures that your customer data, orders, and product information are automatically synced between WooCommerce and GHL, allowing you to manage your e-commerce operations and marketing efforts from a single platform. Enhance your customer relationship management and streamline your workflows with this essential tool for online retailers using WooCommerce and GHL.
Version: 1.0
Author: Mitash Digital
*/

// Include necessary files
include_once plugin_dir_path(__FILE__) . 'includes/activation-check.php';
include_once plugin_dir_path(__FILE__) . 'includes/admin-menu.php';
include_once plugin_dir_path(__FILE__) . 'includes/functions.php';

// Check WooCommerce status on plugin activation
function ghl_woocommerce_sync_activate() {
    if (!class_exists('WooCommerce')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(__('GHL WooCommerce Sync requires WooCommerce to be installed and activated.', 'ghl-woocommerce-sync'));
    }
}
register_activation_hook(__FILE__, 'ghl_woocommerce_sync_activate');

// Deactivate the plugin if WooCommerce is deactivated
function ghl_woocommerce_sync_deactivate() {
    if (!class_exists('WooCommerce')) {
        deactivate_plugins(plugin_basename(__FILE__));
        add_action('admin_notices', 'ghl_woocommerce_sync_admin_notice');
    }
}
add_action('admin_init', 'ghl_woocommerce_sync_deactivate');

// Show an admin notice if WooCommerce is not active
function ghl_woocommerce_sync_admin_notice() {
    ?>
    <div class="error">
        <p><?php _e('GHL WooCommerce Sync requires WooCommerce to be installed and activated.', 'ghl-woocommerce-sync'); ?></p>
    </div>
    <?php
}
