
# Simplify Commerce payment module for OpenCart

This plugin adds Simplify Commerce as a payment option on your OpenCart checkout page.

Please note: The Standard payment form in some of the previous releases is now deprecated and out of support.

## Support

For any issues or enhancement requests you have with this plugin, please raise them with the bank's support team. Please make sure you also provide your plugin & opencart version as well as your merchant ID where applicable. This will help to speed up the troubleshooting of any issues you are having.

## Compatibility
Versions v1.0.0 to v1.0.4
- Compatible with OpenCart versions up to 1.5.6.4.

Versions v1.0.5 
- Compatible with OpenCart 2.0 (tested with v2.0.1.1)

Version v1.1.0
- Adds Hosted Payments mode.

Version v1.2.0
- Compatible with OpenCart 2.3.0.2

Version v1.2.1
- Enhancements and compatible with OpenCart 2.3.0.2

Version v2.0.0
- Compatiblity with OpenCart 3.0.3.1
- Removed standard (form) integration

Version v2.1.0
- Adds Authorize + Capture transaction modes

Version v2.2.0
- Added embedded option on checkout
- Changed branding and some terminology 

Version v2.3.0
- Updated a way of adding necessary Styles and Scripts to the Checkout Page

## Installation
1. Make a backup of your site before applying new mods etc.
2. Download .ocmod.zip file of the latest release of the extension from https://github.com/simplifycom/simplify-opencart-module/releases/latest
3. Go to Admin → Extensions → Installer and upload the downloaded .ocmod.zip file.
4. Once it will be uploaded successfully, Go to Admin → Extensions → Extensions.
5. From the extension type filter, choose the Payments type.
6. Scroll down until you find the Simplify Commerce extension and then click on +(Install) button.

## Configuration
Please proceed with the following actions to configure the payment method:

1. Log in to your OpenCart administration application.
2. Go to Extension > Extensions
3. From the extension type filter, choose Payments
4. Scroll down until you find 'Simplify Commerce' extension, click on Edit button
5. Enter your public and private API keys into the appropriate fields for the live and sandbox mode. For information on your API keys go to https://www.simplify.com/commerce/docs/misc/index. NOTE: When using Hosted Payments, you must create and use an API key pair which has Hosted Payments enabled. 
6. Enter a Payment Title. This will be the name shown to your users on the checkout form.
7. Choose between Modal and Embedded Integration Model.   
8. Map the Successful and Declined status to suit your own workflow. This does not affect the Simplify Commerce configuration.
9. If you use multiple Payment Providers you can use Sort Order to configure how they're shown on the checkout form.
19. Don't forget to Enable the extension to activate it.

## Hosted Payments 

### Modal Integration Mode

The customer will be presented with a button to confirm the order, which, when clicked, will launch a secure form where the customer can input their card details.

![Hosted Payments Button](docs/hp1.png "Hosted Payments Button")

If your website has an SSL certificate with HTTPS enabled, then the form will be overlayed on top of the existing webpage. Otherwise user will be taken to a secured page in a new window. When the customer completes the payment, he will be taken back to the success page.

![Hosted Payments Checkout View](docs/hp2.png "Hosted Payments Checkout View")

### Embedded Integration Mode

A secure payment form will be presented right on the checkout page. The customer can input their card details into that form and submit it to place an order.

![Embedded Payments Checkout View](docs/ep1.png "Embedded Payments Checkout View")
