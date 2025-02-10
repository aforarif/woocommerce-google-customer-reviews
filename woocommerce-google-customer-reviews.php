<?php
/**
 * Plugin Name: WooCommerce Google Customer Reviews
 * Description: Adds Google Customer Reviews survey opt-in script to WooCommerce order confirmation page.
 * Version: 1.0
 * Author: Mohammad Ariful Islam
 * Mail: ariful698@gmail.com
 * License: GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Add settings page for Merchant ID
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

// Register settings
add_action('admin_init', 'wc_gcr_register_settings');
function wc_gcr_register_settings() {
    register_setting('wc_gcr_settings_group', 'wc_gcr_merchant_id');
    add_settings_section('wc_gcr_main_section', 'Main Settings', null, 'wc-google-customer-reviews');
    add_settings_field('wc_gcr_merchant_id', 'Google Merchant Center ID', 'wc_gcr_merchant_id_callback', 'wc-google-customer-reviews', 'wc_gcr_main_section');
}

// Merchant ID field callback
function wc_gcr_merchant_id_callback() {
    $merchant_id = get_option('wc_gcr_merchant_id', '');
    echo '<input type="text" name="wc_gcr_merchant_id" value="' . esc_attr($merchant_id) . '" class="regular-text">';
}

// Calculate delivery date based on shipping zones and destination
function wc_gcr_calculate_delivery_date($order) {
    // Default handling time (1 day)
    $handling_days = apply_filters('wc_gcr_handling_days', 1);

    // Get store's base country
    $store_country = WC()->countries->get_base_country();

    // Get order's shipping country
    $shipping_country = $order->get_shipping_country();

    // Determine if the order is domestic or international
    $is_domestic = ($shipping_country === $store_country);

    // Default delivery days (configurable via filters)
    $domestic_days = apply_filters('wc_gcr_domestic_days', 3); // 3 days for domestic
    $international_days = apply_filters('wc_gcr_international_days', 7); // 7 days for international

    // Total days = handling time + shipping days
    $total_days = $handling_days + ($is_domestic ? $domestic_days : $international_days);

    // Calculate the estimated delivery date
    $order_date = $order->get_date_created()->date('Y-m-d');
    return date('Y-m-d', strtotime($order_date . " +{$total_days} days"));
}

// Add Google Customer Reviews script to WooCommerce order confirmation page
add_action('woocommerce_thankyou', 'wc_gcr_add_script', 10, 1);
function wc_gcr_add_script($order_id) {
    $order = wc_get_order($order_id);
    if (!$order || !$order->get_id()) return;

    $merchant_id = get_option('wc_gcr_merchant_id', '');
    if (empty($merchant_id)) return;

    // Get order details
    $order_number = $order->get_order_number();
    $customer_email = $order->get_billing_email();
    $shipping_country = $order->get_shipping_country();
    $estimated_delivery_date = wc_gcr_calculate_delivery_date($order);

    // Get product GTINs (if available)
    $products = [];
    foreach ($order->get_items() as $item) {
        $product = $item->get_product();
        $gtin = $product->get_meta('_gtin');
        if ($gtin) $products[] = ['gtin' => $gtin];
    }

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
      window.___gcfg = { lang: '<?php echo esc_js(get_locale()); ?>' };
    </script>
    <?php
}