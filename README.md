# WooCommerce Google Customer Reviews

A WooCommerce plugin to integrate Google Customer Reviews survey opt-in script.

## Description

This plugin adds the Google Customer Reviews survey opt-in script to the WooCommerce order confirmation page. It automatically calculates the estimated delivery date based on the store's shipping zones and order destination. The plugin also supports dynamic language configuration and ensures the script runs only on valid, completed orders.

## Features

- **Google Merchant Center ID**: Easily configure your Merchant ID via the WordPress admin.
- **Dynamic Delivery Date**: Automatically calculates delivery dates based on handling and shipping days.
- **GTIN Support**: Supports product GTINs for better survey relevance.
- **Language Configuration**: Uses the customer’s locale or site locale for the survey language.
- **Order Validation**: Ensures the script runs only on valid, completed orders.

## Installation

1. Download the plugin ZIP file from the [latest release](https://github.com/aforarif/woocommerce-google-customer-reviews/releases/latest).
2. Go to your WordPress admin dashboard.
3. Navigate to **Plugins > Add New > Upload Plugin**.
4. Click **Choose File** and select the downloaded ZIP file.
5. Click **Install Now**.
6. After installation, click **Activate Plugin**.

## Configuration

1. Go to **WooCommerce > Google Customer Reviews** in the WordPress admin.
2. Enter the following settings:
   - **Google Merchant Center ID**: Your Google Merchant Center ID (must be numeric).
   - **GTIN Field**: The custom field where GTINs are stored (default: `_gtin`).
   - **Handling Days**: Number of days to process the order (default: `1`).
   - **Domestic Shipping Days**: Number of days for domestic shipping (default: `3`).
   - **International Shipping Days**: Number of days for international shipping (default: `7`).
3. Save the settings.

## Frequently Asked Questions

### What is a Google Merchant Center ID?
Your Google Merchant Center ID is a unique identifier for your Google Merchant Center account. You can find it in your Google Merchant Center dashboard.

### Can I customize the delivery date calculation?
Yes, you can configure handling and shipping days in the plugin settings.

### How does the plugin handle language?
The plugin uses the customer’s locale (if available) or falls back to the site’s locale. For example, if the customer’s locale is `fr_FR`, the survey language will be `fr`.

### Does the plugin work with all WooCommerce versions?
The plugin is compatible with WooCommerce 6.x and 7.x. It has been tested with WordPress 5.9 and above.

## Screenshots

1. Plugin settings page in WordPress admin.
2. Google Customer Reviews survey on the order confirmation page.

## Changelog

### 1.0.0
- Initial release.
- Added Merchant ID validation.
- Added dynamic language configuration.
- Added order validation.

## Upgrade Notice

### 1.0.0
- Initial release.

## Support

For support, please [open an issue](https://github.com/aforarif/woocommerce-google-customer-reviews/issues) on GitHub.

## License

This project is licensed under the GPL-2.0 License. See the [LICENSE](LICENSE) file for details.
