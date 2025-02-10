# WooCommerce Google Customer Reviews

A WooCommerce plugin to integrate Google Customer Reviews survey opt-in script.

## Description

This plugin adds the Google Customer Reviews survey opt-in script to the WooCommerce order confirmation page. It automatically calculates the estimated delivery date based on the store's shipping zones and order destination.

## Features

- Automatically calculates the estimated delivery date.
- Supports domestic and international shipping.
- Configurable handling and shipping days via filters.
- Easy-to-use settings page for entering the Google Merchant Center ID.

## Installation

1. Download the plugin ZIP file from the [latest release](https://github.com/aforarif/woocommerce-google-customer-reviews/releases/latest).
2. Go to your WordPress admin dashboard.
3. Navigate to **Plugins > Add New > Upload Plugin**.
4. Click **Choose File** and select the downloaded ZIP file.
5. Click **Install Now**.
6. After installation, click **Activate Plugin**.

## Configuration

1. Go to **WooCommerce > Google Customer Reviews** in the WordPress admin.
2. Enter your **Google Merchant Center ID** and save the settings.

## Customization

You can customize the handling and shipping days using WordPress filters:

```php
// Example: Change handling time to 2 days
add_filter('wc_gcr_handling_days', function() { return 2; });

// Example: Change domestic shipping days to 2
add_filter('wc_gcr_domestic_days', function() { return 2; });

// Example: Change international shipping days to 10
add_filter('wc_gcr_international_days', function() { return 10; });