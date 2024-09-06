<?php
/*
 * Admin Menu
 * This file creates an admin menu for plugin settings.
 */

function ghl_woocommerce_sync_add_admin_menu() {
    add_menu_page(
        'GHL WooCommerce Sync Settings',
        'GHL WooCommerce Sync',
        'manage_options',
        'ghl-woocommerce-sync',
        'ghl_woocommerce_sync_settings_page'
    );
}
add_action('admin_menu', 'ghl_woocommerce_sync_add_admin_menu');

function ghl_woocommerce_sync_settings_page() {
    ?>
    <div class="wrap">
        <h1>GHL WooCommerce Sync Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('ghl_woocommerce_sync_settings_group');
            do_settings_sections('ghl_woocommerce_sync_settings_group');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Bearer Token</th>
                    <td><input type="text" name="ghl_woocommerce_sync_bearer_token" value="<?php echo esc_attr(get_option('ghl_woocommerce_sync_bearer_token')); ?>" style="width: 100%;" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
