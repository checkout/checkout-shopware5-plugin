# CkoCheckoutPayment
## About CkoCheckoutPayment
Checkout.com Payment Provider for Shopware 5.6
## Supported Payment Methods
- Giropay
- Sofort
- Klarna
- EPS
- iDEAL
- Bancontact
- SEPA
- Paypal
- gPay

## Installation
- Switch to the plugin directory 
- `composer install` 
- **this will install all dependencies that are required**
- Activate & Install the plugin and configure it with your secret & public key
- Test the configuration for validation
- Register webhooks
- Enable the payment methods you need for shop(s)

## Running tests
- Switch to the plugin directory
- Execute composer test to run all tests

## Capturing Payments
###### If auto capture is enabled capture will done automatically for the supported payment methods
- Goto Backend -> Orders and select a order
- There is a new tab called "Checkout.com"
- Click on the capture button to trigger capturing of the order
- You can capture the complete order or just a few order positions
- Total Remaining possible capture amount will be shown above

## Refunding Payments
###### Refunding order positions is only possible with Pickware ERP

- Goto Backend -> Orders and select a order
- There is a new tab called "Checkout.com"
- Click on the refund button to trigger refunding of the order
- You can refund the complete order or just a few order positions
- Total Remaining possible refund amount will be shown above

## Voiding Payments
- Goto Backend -> Orders and select a order
- There is a new tab called "Checkout.com"
- Click on the void button to trigger voiding of the order

## Checking Logs
- You can check the logs via the backend or the cli (tail -f var/log/cko_checkout_payment_dev-Y-m-d.log)

## License

Please see [License File](LICENSE) for more information.