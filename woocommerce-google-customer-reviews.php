<?php
/**
 * Plugin Name: WooCommerce Google Customer Reviews
 * Description: Adds Google Customer Reviews survey opt-in script to WooCommerce order confirmation page.
 * Version: 1.0
 * Author: Mohamad Ariful Islam
 * License: GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Add settings page
add_action('admin_menu', 'wc_gcr_add_settings_page');
function wc_gcr_add_settings_page() {
    add_submenu_page(
        'woocommerce',
        'Google Customer Reviews',
        'Google Customer Reviews',
        'manage_options',
        'wc-google-customer-reviews',
        'wc_gcr_render_settings_page'
    );
}

// Render settings page
function wc_gcr_render_settings_page() {
    ?>
    <div class="wrap">
        <h1>Google Customer Reviews Settings</h1>
        <?php settings_errors(); ?>
        <form method="post" action="options.php">
            <?php
            settings_fields('wc_gcr_settings_group');
            do_settings_sections('wc-google-customer-reviews');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register settings and validate Merchant ID
add_action('admin_init', 'wc_gcr_register_settings');
function wc_gcr_register_settings() {
    register_setting('wc_gcr_settings_group', 'wc_gcr_merchant_id', [
        'sanitize_callback' => 'wc_gcr_validate_merchant_id', // Validate Merchant ID
    ]);
    register_setting('wc_gcr_settings_group', 'wc_gcr_gtin_field');
    register_setting('wc_gcr_settings_group', 'wc_gcr_handling_days');
    register_setting('wc_gcr_settings_group', 'wc_gcr_domestic_days');
    register_setting('wc_gcr_settings_group', 'wc_gcr_international_days');

    add_settings_section('wc_gcr_main_section', 'Main Settings', null, 'wc-google-customer-reviews');

    add_settings_field('wc_gcr_merchant_id', 'Google Merchant Center ID', 'wc_gcr_merchant_id_callback', 'wc-google-customer-reviews', 'wc_gcr_main_section');
    add_settings_field('wc_gcr_gtin_field', 'GTIN Field', 'wc_gcr_gtin_field_callback', 'wc-google-customer-reviews', 'wc_gcr_main_section');
    add_settings_field('wc_gcr_handling_days', 'Handling Days', 'wc_gcr_handling_days_callback', 'wc-google-customer-reviews', 'wc_gcr_main_section');
    add_settings_field('wc_gcr_domestic_days', 'Domestic Shipping Days', 'wc_gcr_domestic_days_callback', 'wc-google-customer-reviews', 'wc_gcr_main_section');
    add_settings_field('wc_gcr_international_days', 'International Shipping Days', 'wc_gcr_international_days_callback', 'wc-google-customer-reviews', 'wc_gcr_main_section');
}

// Validate Merchant ID (must be numeric)
function wc_gcr_validate_merchant_id($input) {
    if (!empty($input) {
        if (!is_numeric($input)) {
            add_settings_error(
                'wc_gcr_merchant_id',
                'invalid_merchant_id',
                'Google Merchant Center ID must be a numeric value.'
            );
            return get_option('wc_gcr_merchant_id'); // Return the old value
        }
    }
    return $input;
}

// Merchant ID field callback
function wc_gcr_merchant_id_callback() {
    $merchant_id = get_option('wc_gcr_merchant_id', '');
    echo '<input type="text" name="wc_gcr_merchant_id" value="' . esc_attr($merchant_id) . '" class="regular-text">';
}

// Other settings callbacks remain the same (gtin_field, handling_days, etc.)

// Calculate delivery date (same as before)

// Add Google Customer Reviews script to WooCommerce order confirmation page
add_action('woocommerce_thankyou', 'wc_gcr_add_script', 10, 1);
function wc_gcr_add_script($order_id) {
    $order = wc_get_order($order_id);

    // Validate the order
    if (!$order || !$order->get_id() || !$order->has_status('completed')) {
        return; // Exit if order is invalid or not completed
    }

    $merchant_id = get_option('wc_gcr_merchant_id', '');
    if (empty($merchant_id) || !is_numeric($merchant_id)) {
        return; // Exit if Merchant ID is invalid
    }

    // Get language based on customer's locale or site locale
    $customer_id = $order->get_customer_id();
    $user_locale = $customer_id ? get_user_meta($customer_id, 'locale', true) : '';
    $language = $user_locale ? substr($user_locale, 0, 2) : get_locale();

    // Get order details (same as before)
    $order_number = $order->get_order_number();
    $customer_email = $order->get_billing_email();
    $shipping_country = $order->get_shipping_country();
    $estimated_delivery_date = wc_gcr_calculate_delivery_date($order);

    // Get product GTINs (same as before)

    // Output the script
    ?>
    <script src="https://apis.google.com/js/platform.js?onload=renderOptIn" async defer></script>
    <script>
      window.renderOptIn = function() {
        window.gapi.load('surveyoptin', function() {
          window.gapi.surveyoptin.render({
            "merchant_id": "<?php echo esc_js($merchant_id); ?>",
            "order_id": "<?php echo esc_js($order_number); ?>",
            "email": "<?php echo esc_js($customer_email); ?>",
            "delivery_country": "<?php echo esc_js($shipping_country); ?>",
            "estimated_delivery_date": "<?php echo esc_js($estimated_delivery_date); ?>",
            "products": <?php echo json_encode($products); ?>
          });
        });
      };
      window.___gcfg = { lang: '<?php echo esc_js($language); ?>' }; // Use customer/site language
    </script>
    <?php
}
