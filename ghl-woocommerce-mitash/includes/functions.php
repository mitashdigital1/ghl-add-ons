<?php
/*
 * Functions
 * This file contains the core functions of the plugin, including API interactions and WooCommerce hooks.
 */

function ghl_woocommerce_sync_update_contact_field($order_id) {
    if (!$order_id) {
        return;
    }

    $order = wc_get_order($order_id);
    $order_total = $order->get_total();
    $customer_email = $order->get_billing_email();
    $contact_id = ghl_woocommerce_sync_get_contact_id_by_email($customer_email);

    if ($contact_id) {
        ghl_woocommerce_sync_update_custom_field($contact_id, $order_total, true);
    } else {
        ghl_woocommerce_sync_create_contact($customer_email, $order_total);
    }
}
add_action('woocommerce_thankyou', 'ghl_woocommerce_sync_update_contact_field', 10, 1);

function ghl_woocommerce_sync_get_contact_id_by_email($email) {
    $bearer_token = get_option('ghl_woocommerce_sync_bearer_token');
    $url = "https://rest.gohighlevel.com/v1/contacts/lookup?email={$email}";

    $response = wp_remote_get($url, array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $bearer_token,
            'Content-Type'  => 'application/json',
        ),
    ));

    if (is_wp_error($response)) {
        error_log('GHL API error: ' . $response->get_error_message());
        return false;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    return $body['contacts'][0]['id'] ?? false;
}

function ghl_woocommerce_sync_get_contact_data($contact_id) {
    $bearer_token = get_option('ghl_woocommerce_sync_bearer_token');
    $url = "https://rest.gohighlevel.com/v1/contacts/{$contact_id}";

    $response = wp_remote_get($url, array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $bearer_token,
            'Content-Type'  => 'application/json',
        ),
    ));

    if (is_wp_error($response)) {
        error_log('GHL API error: ' . $response->get_error_message());
        return [];
    }

    return json_decode(wp_remote_retrieve_body($response), true);
}

function ghl_woocommerce_sync_update_custom_field($contact_id, $order_total, $add_to_existing = false) {
    $bearer_token = get_option('ghl_woocommerce_sync_bearer_token');
    $url = "https://rest.gohighlevel.com/v1/contacts/{$contact_id}";

    $current_amount = 0;
    if ($add_to_existing) {
        $contact_data = ghl_woocommerce_sync_get_contact_data($contact_id);
        $current_amount = $contact_data['customField']['FM1S2ftFTu0SiOY3HsB5'] ?? 0;
    }

    $new_total = $current_amount + $order_total;

    $args = array(
        'method'    => 'PUT',
        'headers'   => array(
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $bearer_token,
        ),
        'body'      => json_encode(array(
            'customField' => array(
                'FM1S2ftFTu0SiOY3HsB5' => $new_total
            )
        )),
    );

    $response = wp_remote_request($url, $args);

    if (is_wp_error($response)) {
        error_log('GHL API error: ' . $response->get_error_message());
    } else {
        error_log('GHL contact updated successfully.');
    }
}

function ghl_woocommerce_sync_create_contact($email, $order_total) {
    $bearer_token = get_option('ghl_woocommerce_sync_bearer_token');
    $url = "https://rest.gohighlevel.com/v1/contacts/";

    $args = array(
        'method'    => 'POST',
        'headers'   => array(
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $bearer_token,
        ),
        'body'      => json_encode(array(
            'email' => $email,
            'customField' => array(
                'FM1S2ftFTu0SiOY3HsB5' => $order_total
            )
        )),
    );

    $response = wp_remote_request($url, $args);

    if (is_wp_error($response)) {
        error_log('GHL API error: ' . $response->get_error_message());
    } else {
        error_log('GHL contact created successfully.');
    }
}
