<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Tests\Unit\Components\CheckoutApi\RequestBuilder;

use Checkout\Models\Address;
use Checkout\Models\Payments\KlarnaSource;
use Checkout\Models\Product;
use CkoCheckoutPayment\Components\CheckoutApi\Builder\RequestBuilder\KlarnaRequestBuilderService;
use CkoCheckoutPayment\Components\CheckoutApi\Exception\CheckoutApiRequestException;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\KlarnaRequestDataStruct;
use CkoCheckoutPayment\Components\Logger\LoggerService;
use CkoCheckoutPayment\Components\PaymentMethods\KlarnaPaymentMethod;
use CkoCheckoutPayment\Tests\Mocks\CheckoutApiClientServiceMock;
use CkoCheckoutPayment\Tests\Mocks\CountryRepositoryMock;
use CkoCheckoutPayment\Tests\Mocks\DependencyProviderServiceMock;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Country\Country;

class KlarnaRequestBuilderServiceTest extends TestCase
{
    /**
     * @var CheckoutApiClientServiceMock
     */
    private $apiClientServiceMock;

    /**
     * @var KlarnaRequestBuilderService
     */
    private $klarnaRequestBuilderService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->apiClientServiceMock = new CheckoutApiClientServiceMock(KlarnaPaymentMethod::NAME);
        $this->klarnaRequestBuilderService = new KlarnaRequestBuilderService(
            new DependencyProviderServiceMock(),
            $this->apiClientServiceMock,
            $this->createMock(LoggerService::class),
            new CountryRepositoryMock($this->createMock(ModelManager::class), new ClassMetadata(Country::class))
        );
    }

    public function testCreateKlarnaPaymentInitializeData(): void
    {
        $result = $this->klarnaRequestBuilderService->createKlarnaPaymentInitializeData($this->getTestBasket(), $this->getTestUser(), 1);
        static::assertInstanceOf(KlarnaRequestDataStruct::class, $result);
        static::assertSame('testToken', $result->getClientToken());
        static::assertSame('kcs_abcabc', $result->getInstanceId());
        static::assertSame(['pay_later', 'pay_over_time'], $result->getPaymentMethods());

        $expectedRequestData = [
            'purchase_country' => 'DE',
            'purchase_currency' => 'EUR',
            'locale' => 'de-DE',
            'order_amount' => 28194.0,
            'order_tax_amount' => 4500.0,
            'order_lines' => [
                [
                    'type' => 'physical',
                    'reference' => 'SW10002.3',
                    'name' => 'Test Product',
                    'quantity' => 1,
                    'unit_price' => 1999.0,
                    'tax_rate' => 1900.0,
                    'total_amount' => 1999.0,
                    'total_discount_amount' => 0,
                    'total_tax_amount' => 319.0,
                    'product_url' => 'http:///Detail/index/sArticle/0',
                    'image_url' => 'http://testImageUrl.com'
                ],
                [
                    'type' => 'digital',
                    'reference' => 'SW10002.3',
                    'name' => 'Test Downloadable Product',
                    'quantity' => 1,
                    'unit_price' => 19900.0,
                    'tax_rate' => 1900.0,
                    'total_amount' => 19900.0,
                    'total_discount_amount' => 0,
                    'total_tax_amount' => 3177.0,
                    'product_url' => 'http:///Detail/index/sArticle/0',
                    'image_url' => 'http://testImageUrl.com'
                ],
                [
                    'type' => 'discount',
                    'reference' => 'GUTPROZ',
                    'name' => 'Test Voucher',
                    'quantity' => 1,
                    'unit_price' => 2190.0,
                    'tax_rate' => 1900.0,
                    'total_amount' => 2190.0,
                    'total_discount_amount' => 2190.0,
                    'total_tax_amount' => -350.0,
                    'product_url' => 'http:///Detail/index/sArticle/0',
                    'image_url' => 'http://testImageUrl.com'
                ],
                [
                    'type' => 'surcharge',
                    'reference' => 'sw-payment',
                    'name' => 'Test Surcharge',
                    'quantity' => 1,
                    'unit_price' => 985.0,
                    'tax_rate' => 1900.0,
                    'total_amount' => 985.0,
                    'total_discount_amount' => 0,
                    'total_tax_amount' => 157.0,
                    'product_url' => 'http:///Detail/index/sArticle/0',
                    'image_url' => 'http://testImageUrl.com'
                ],
                [
                    'name' => 'Shipping costs',
                    'quantity' => 1,
                    'unit_price' => 7500.0,
                    'tax_rate' => 1900.0,
                    'total_amount' => 7500.0,
                    'total_tax_amount' => 1197.0,
                    'type' => 'shipping_fee'
                ]
            ],
            'billing_address' => [
                'given_name' => 'firstname',
                'family_name' => 'lastname',
                'email' => 'test@email.com',
                'title' => 'mr',
                'street_address' => 'test 123',
                'street_address2' => '',
                'postal_code' => '012345',
                'city' => 'testCity',
                'region' => '',
                'phone' => '',
                'country' => 'DE'
            ],
            'customer' => [
                'gender' => 'male',
            ],
        ];

        static::assertSame($expectedRequestData, $result->getRequestData());
    }

    public function testCreateKlarnaPaymentInitializeDataWhenCheckoutExceptionIsThrown(): void
    {
        $this->apiClientServiceMock->setShouldThrowSourceException(true);

        $result = $this->klarnaRequestBuilderService->createKlarnaPaymentInitializeData($this->getTestBasket(), $this->getTestUser(), 1);
        static::assertInstanceOf(KlarnaRequestDataStruct::class, $result);
        static::assertSame('', $result->getClientToken());
        static::assertSame('', $result->getInstanceId());
        static::assertSame([], $result->getPaymentMethods());

        $expectedRequestData = [
            'purchase_country' => 'DE',
            'purchase_currency' => 'EUR',
            'locale' => 'de-DE',
            'order_amount' => 28194.0,
            'order_tax_amount' => 4500.0,
            'order_lines' => [
                [
                    'type' => 'physical',
                    'reference' => 'SW10002.3',
                    'name' => 'Test Product',
                    'quantity' => 1,
                    'unit_price' => 1999.0,
                    'tax_rate' => 1900.0,
                    'total_amount' => 1999.0,
                    'total_discount_amount' => 0,
                    'total_tax_amount' => 319.0,
                    'product_url' => 'http:///Detail/index/sArticle/0',
                    'image_url' => 'http://testImageUrl.com'
                ],
                [
                    'type' => 'digital',
                    'reference' => 'SW10002.3',
                    'name' => 'Test Downloadable Product',
                    'quantity' => 1,
                    'unit_price' => 19900.0,
                    'tax_rate' => 1900.0,
                    'total_amount' => 19900.0,
                    'total_discount_amount' => 0,
                    'total_tax_amount' => 3177.0,
                    'product_url' => 'http:///Detail/index/sArticle/0',
                    'image_url' => 'http://testImageUrl.com'
                ],
                [
                    'type' => 'discount',
                    'reference' => 'GUTPROZ',
                    'name' => 'Test Voucher',
                    'quantity' => 1,
                    'unit_price' => 2190.0,
                    'tax_rate' => 1900.0,
                    'total_amount' => 2190.0,
                    'total_discount_amount' => 2190.0,
                    'total_tax_amount' => -350.0,
                    'product_url' => 'http:///Detail/index/sArticle/0',
                    'image_url' => 'http://testImageUrl.com'
                ],
                [
                    'type' => 'surcharge',
                    'reference' => 'sw-payment',
                    'name' => 'Test Surcharge',
                    'quantity' => 1,
                    'unit_price' => 985.0,
                    'tax_rate' => 1900.0,
                    'total_amount' => 985.0,
                    'total_discount_amount' => 0,
                    'total_tax_amount' => 157.0,
                    'product_url' => 'http:///Detail/index/sArticle/0',
                    'image_url' => 'http://testImageUrl.com'
                ],
                [
                    'name' => 'Shipping costs',
                    'quantity' => 1,
                    'unit_price' => 7500.0,
                    'tax_rate' => 1900.0,
                    'total_amount' => 7500.0,
                    'total_tax_amount' => 1197.0,
                    'type' => 'shipping_fee'
                ]
            ],
            'billing_address' => [
                'given_name' => 'firstname',
                'family_name' => 'lastname',
                'email' => 'test@email.com',
                'title' => 'mr',
                'street_address' => 'test 123',
                'street_address2' => '',
                'postal_code' => '012345',
                'city' => 'testCity',
                'region' => '',
                'phone' => '',
                'country' => 'DE'
            ],
            'customer' => [
                'gender' => 'male',
            ],
        ];

        static::assertSame($expectedRequestData, $result->getRequestData());
    }

    public function testCreateKlarnaSource(): void
    {
        $user = $this->getTestUser();

        $result = $this->klarnaRequestBuilderService->createKlarnaSource('testToken', 'DE', $user['billingaddress'], $this->getTestBasket(), $user);
        static::assertInstanceOf(KlarnaSource::class, $result);

        static::assertSame('klarna', $result->getValue('type'));
        static::assertSame('testToken', $result->getValue('authorization_token'));

        $billingAddress = $result->getValue('billing_address');
        static::assertInstanceOf(Address::class, $billingAddress);
        static::assertSame('firstname', $billingAddress->getValue('given_name'));
        static::assertSame('lastname', $billingAddress->getValue('family_name'));
        static::assertSame('test@email.com', $billingAddress->getValue('email'));
        static::assertSame('mr', $billingAddress->getValue('title'));
        static::assertSame('test 123', $billingAddress->getValue('street_address'));
        static::assertSame('', $billingAddress->getValue('street_address2'));
        static::assertSame('012345', $billingAddress->getValue('postal_code'));
        static::assertSame('testCity', $billingAddress->getValue('city'));
        static::assertSame('', $billingAddress->getValue('region'));
        static::assertSame('', $billingAddress->getValue('phone'));
        static::assertSame('DE', $billingAddress->getValue('country'));

        static::assertSame('DE', $result->getValue('purchase_country'));
        static::assertSame('de-DE', $result->getValue('locale'));
        static::assertSame(4500.00, $result->getValue('tax_amount'));

        $products = $result->getValue('products');
        static::assertInstanceOf(Product::class, $products[0]);
        static::assertSame('physical', $products[0]->getValue('type'));
        static::assertSame('SW10002.3', $products[0]->getValue('reference'));
        static::assertSame('Test Product', $products[0]->getValue('name'));
        static::assertSame(1, $products[0]->getValue('quantity'));
        static::assertSame(1999.0, $products[0]->getValue('unit_price'));
        static::assertSame(1900.0, $products[0]->getValue('tax_rate'));
        static::assertSame(1999.0, $products[0]->getValue('total_amount'));
        static::assertSame(0, $products[0]->getValue('total_discount_amount'));
        static::assertSame(319.0, $products[0]->getValue('total_tax_amount'));
        static::assertSame('http:///Detail/index/sArticle/0', $products[0]->getValue('product_url'));
        static::assertSame('http://testImageUrl.com', $products[0]->getValue('image_url'));

        static::assertInstanceOf(Product::class, $products[1]);
        static::assertSame('digital', $products[1]->getValue('type'));
        static::assertSame('SW10002.3', $products[1]->getValue('reference'));
        static::assertSame('Test Downloadable Product', $products[1]->getValue('name'));
        static::assertSame(1, $products[1]->getValue('quantity'));
        static::assertSame(19900.0, $products[1]->getValue('unit_price'));
        static::assertSame(1900.0, $products[1]->getValue('tax_rate'));
        static::assertSame(19900.0, $products[1]->getValue('total_amount'));
        static::assertSame(0, $products[1]->getValue('total_discount_amount'));
        static::assertSame(3177.0, $products[1]->getValue('total_tax_amount'));
        static::assertSame('http:///Detail/index/sArticle/0', $products[1]->getValue('product_url'));
        static::assertSame('http://testImageUrl.com', $products[1]->getValue('image_url'));

        static::assertInstanceOf(Product::class, $products[2]);
        static::assertSame('discount', $products[2]->getValue('type'));
        static::assertSame('GUTPROZ', $products[2]->getValue('reference'));
        static::assertSame('Test Voucher', $products[2]->getValue('name'));
        static::assertSame(1, $products[2]->getValue('quantity'));
        static::assertSame(2190.0, $products[2]->getValue('unit_price'));
        static::assertSame(1900.0, $products[2]->getValue('tax_rate'));
        static::assertSame(2190.0, $products[2]->getValue('total_amount'));
        static::assertSame(2190.0, $products[2]->getValue('total_discount_amount'));
        static::assertSame(-350.0, $products[2]->getValue('total_tax_amount'));
        static::assertSame('http:///Detail/index/sArticle/0', $products[2]->getValue('product_url'));
        static::assertSame('http://testImageUrl.com', $products[2]->getValue('image_url'));

        static::assertInstanceOf(Product::class, $products[3]);
        static::assertSame('surcharge', $products[3]->getValue('type'));
        static::assertSame('sw-payment', $products[3]->getValue('reference'));
        static::assertSame('Test Surcharge', $products[3]->getValue('name'));
        static::assertSame(1, $products[3]->getValue('quantity'));
        static::assertSame(985.0, $products[3]->getValue('unit_price'));
        static::assertSame(1900.0, $products[3]->getValue('tax_rate'));
        static::assertSame(985.0, $products[3]->getValue('total_amount'));
        static::assertSame(0, $products[3]->getValue('total_discount_amount'));
        static::assertSame(157.0, $products[3]->getValue('total_tax_amount'));
        static::assertSame('http:///Detail/index/sArticle/0', $products[3]->getValue('product_url'));
        static::assertSame('http://testImageUrl.com', $products[3]->getValue('image_url'));

        static::assertInstanceOf(Product::class, $products[4]);
        static::assertSame('Shipping costs', $products[4]->getValue('name'));
        static::assertSame(1, $products[4]->getValue('quantity'));
        static::assertSame(7500.0, $products[4]->getValue('unit_price'));
        static::assertSame(1900.0, $products[4]->getValue('tax_rate'));
        static::assertSame(7500.0, $products[4]->getValue('total_amount'));
        static::assertSame(1197.0, $products[4]->getValue('total_tax_amount'));
        static::assertSame('shipping_fee', $products[4]->getValue('type'));
    }

    public function testCreateBillingAddress(): void
    {
        $user = $this->getTestUser();

        $billingAddress = $this->klarnaRequestBuilderService->createBillingAddress($user['billingaddress'], $user);
        static::assertInstanceOf(Address::class, $billingAddress);

        static::assertSame('firstname', $billingAddress->getValue('given_name'));
        static::assertSame('lastname', $billingAddress->getValue('family_name'));
        static::assertSame('test@email.com', $billingAddress->getValue('email'));
        static::assertSame('mr', $billingAddress->getValue('title'));
        static::assertSame('test 123', $billingAddress->getValue('street_address'));
        static::assertSame('', $billingAddress->getValue('street_address2'));
        static::assertSame('012345', $billingAddress->getValue('postal_code'));
        static::assertSame('testCity', $billingAddress->getValue('city'));
        static::assertSame('', $billingAddress->getValue('region'));
        static::assertSame('', $billingAddress->getValue('phone'));
        static::assertSame('DE', $billingAddress->getValue('country'));
    }

    private function getTestBasket(): array
    {
        return [
            'content' => [
                [
                    'articlename' => 'Test Product',
                    'ordernumber' => 'SW10002.3',
                    'shippingfree' => '0',
                    'quantity' => '1',
                    'price' => '19,99',
                    'netprice' => '16.798319327731',
                    'tax_rate' => '19',
                    'modus' => '0',
                    'esdarticle' => '0',
                    'instock' => '5',
                    'esd' => '0',
                    'amount' => '19,99',
                    'amountnet' => '16,80',
                    'priceNumeric' => '19.99',
                    'amountNumeric' => 19.99,
                    'amountnetNumeric' => 16.798319327731,
                    'tax' => '3,19',
                    'image' => ['source' => 'http://testImageUrl.com']
                ],
                [
                    'articlename' => 'Test Downloadable Product',
                    'ordernumber' => 'SW10002.3',
                    'shippingfree' => '0',
                    'quantity' => '1',
                    'price' => '199,00',
                    'netprice' => '167.2268907563',
                    'tax_rate' => '19',
                    'modus' => '0',
                    'esdarticle' => '1',
                    'instock' => '5',
                    'esd' => '1',
                    'amount' => '199,00',
                    'amountnet' => '167,23',
                    'priceNumeric' => '199',
                    'amountNumeric' => 199.0,
                    'amountnetNumeric' => 167.2268907563,
                    'tax' => '31,77',
                    'image' => ['source' => 'http://testImageUrl.com']
                ],
                [
                    'articlename' => 'Test Voucher',
                    'ordernumber' => 'GUTPROZ',
                    'shippingfree' => '0',
                    'quantity' => '1',
                    'price' => '-21,90',
                    'netprice' => '-18.403',
                    'tax_rate' => '19',
                    'modus' => '2',
                    'esdarticle' => '0',
                    'instock' => null,
                    'esd' => '0',
                    'amount' => '-21,90',
                    'amountnet' => '-18,40',
                    'priceNumeric' => '-21.899',
                    'amountNumeric' => -21.9,
                    'amountnetNumeric' => -18.4,
                    'tax' => '-3,50',
                    'image' => ['source' => 'http://testImageUrl.com']
                ],
                [
                    'articlename' => 'Test Surcharge',
                    'ordernumber' => 'sw-payment',
                    'shippingfree' => '0',
                    'quantity' => '1',
                    'price' => '9,85',
                    'netprice' => '8.281',
                    'tax_rate' => '19',
                    'modus' => '4',
                    'esdarticle' => '0',
                    'instock' => null,
                    'esd' => null,
                    'amount' => '9,85',
                    'amountnet' => '8,28',
                    'priceNumeric' => '9.8545',
                    'amountNumeric' => 9.85,
                    'amountnetNumeric' => 8.281,
                    'tax' => '1,57',
                    'image' => ['source' => 'http://testImageUrl.com']
                ]
            ],
            'Amount' => '206,94',
            'AmountNet' => '173,91',
            'Quantity' => 2,
            'AmountNumeric' => 281.94,
            'AmountNetNumeric' => 236.94,
            'AmountWithTax' => '0',
            'AmountWithTaxNumeric' => 0,
            'sCurrencyId' => 1,
            'sCurrencyName' => 'EUR',
            'sCurrencyFactor' => 1.0,
            'sShippingcostsWithTax' => 75.0,
            'sShippingcostsNet' => 63.03,
            'sShippingcostsTax' => 19.0,
            'sShippingcostsDifference' => 9802.91,
            'sShippingcosts' => 75.0,
            'sAmount' => 281.94,
            'sAmountTax' => 45.0
        ];
    }

    private function getTestUser(): array
    {
        return [
            'email' => 'test@email.com',
            'billingaddress' => [
                'firstname' => 'firstname',
                'lastname' => 'lastname',
                'salutation' => 'mr',
                'countryId' => 1,
                'street' => 'test 123',
                'city' => 'testCity',
                'zipcode' => '012345',
                'country' => [
                    'id' => 1
                ]
            ],
            'additional' => [
                'user' => [
                    'salutation' => 'mr',
                    'email' => 'test@email.com',
                ]
            ]
        ];
    }
}