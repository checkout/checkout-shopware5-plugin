<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Builder\RequestBuilder;

use Checkout\Library\Exceptions\CheckoutException;
use Checkout\Models\Address;
use Checkout\Models\Payments\KlarnaSource;
use Checkout\Models\Product;
use Checkout\Models\Sources\Klarna;
use CkoCheckoutPayment\Components\CheckoutApi\ApiClient\CheckoutApiClientServiceInterface;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\KlarnaRequestDataStruct;
use CkoCheckoutPayment\Components\DependencyProvider\DependencyProviderServiceInterface;
use CkoCheckoutPayment\Components\Logger\LoggerServiceInterface;
use Shopware\Models\Country\Country;
use Shopware\Models\Country\Repository as CountryRepository;
use Shopware\Models\Shop\Shop;

class KlarnaRequestBuilderService implements KlarnaRequestBuilderServiceInterface
{
    private const MODE_PHYSICAL = 0;
    private const MODE_DISCOUNT = 2;
    private const MODE_SURCHARGE = 4;

    private const TYPE_PHYSICAL = 'physical';
    private const TYPE_DIGITAL = 'digital';
    private const TYPE_DISCOUNT = 'discount';
    private const TYPE_SURCHARGE = 'surcharge';
    private const TYPE_SHIPPING_FEE = 'shipping_fee';

    private const PRODUCT_SHIPPING_COST_NAME = 'Shipping costs';

    private const SALUTATION_MR = 'mr';
    private const SALUTATION_MS = 'ms';

    private const SALUTATION_MALE = 'male';
    private const SALUTATION_FEMALE = 'female';

    /**
     * @var DependencyProviderServiceInterface
     */
    private $dependencyProviderService;

    /**
     * @var CheckoutApiClientServiceInterface
     */
    private $apiClientService;

    /**
     * @var LoggerServiceInterface
     */
    private $loggerService;

    /**
     * @var CountryRepository
     */
    private $countryRepository;

    public function __construct(
        DependencyProviderServiceInterface $dependencyProviderService,
        CheckoutApiClientServiceInterface $apiClientService,
        LoggerServiceInterface $loggerService,
        CountryRepository $countryRepository
    ) {
        $this->dependencyProviderService = $dependencyProviderService;
        $this->apiClientService = $apiClientService;
        $this->loggerService = $loggerService;
        $this->countryRepository = $countryRepository;
    }

    public function createKlarnaPaymentInitializeData(array $basket, array $user, ?int $shopId): KlarnaRequestDataStruct
    {
        $shop = $this->dependencyProviderService->getShop();
        $locale = $this->getLocale($shop);

        $billingAddress = $user['billingaddress'];
        /** @var Country $billingCountry */
        $billingCountry = $this->countryRepository->find($billingAddress['countryId']);

        if ($billingCountry === null) {
            return new KlarnaRequestDataStruct('', '', [], []);
        }

        $userData = $user['additional']['user'];

        $billingCountryIso = $billingCountry->getIso();
        $basketDataArray = $this->createBasketProducts($basket);
        $billingAddressArray = $this->createBillingAddress($billingAddress, $userData);

        $customerDataArray = [];
        if ($userData['birthday']) {
            $customerDataArray['date_of_birth'] = $userData['birthday'];
        }

        if ($userData['salutation'] === self::SALUTATION_MR || $userData['salutation'] === self::SALUTATION_MS) {
            $customerDataArray['gender'] = $userData['salutation'] === self::SALUTATION_MR ? self::SALUTATION_MALE : self::SALUTATION_FEMALE;
        }

        $currency = $basket['sCurrencyName'];
        $totalPrice = $this->calculateTotalPrice($basket);
        $totalTax = $this->calculateTotalTax($basket);

        $finalDataArray = [
            'purchase_country' => $billingCountryIso,
            'purchase_currency' => $currency,
            'locale' => $locale,
            'order_amount' => $totalPrice,
            'order_tax_amount' => $totalTax,
            'order_lines' => $basketDataArray,
            'billing_address' => $billingAddressArray->getValues(),
            'customer' => $customerDataArray
        ];

        return $this->createKlarnaDefaultInitializationData($basket, $user, $finalDataArray, $shopId);
    }

    public function createKlarnaSource(string $token, string $purchaseCountry, array $billingAddress, array $basket, array $userData): KlarnaSource
    {
        $address = $this->createBillingAddress($billingAddress, $userData);

        $shop = $this->dependencyProviderService->getShop();
        $locale = $this->getLocale($shop);

        $totalTax = $this->calculateTotalTax($basket);
        $products = $this->createProductsFromBasket($basket);

        return new KlarnaSource($token, $purchaseCountry, $locale, $address, $totalTax, $products);
    }

    public function createBillingAddress(array $billingAddress, array $userData): Address
    {
        /** @var Country $billingCountry */
        $billingCountry = $this->countryRepository->find((int)$billingAddress['country']['id']);
        if ($billingCountry === null) {
            return new Address();
        }

        $address = new Address();
        $address->given_name = $billingAddress['firstname'];
        $address->family_name = $billingAddress['lastname'];
        $address->email = $userData['email'];
        $address->title = $billingAddress['salutation'];
        $address->street_address = $billingAddress['street'];
        $address->street_address2 = $billingAddress['additionalAddressLine1'] ?: '';
        $address->postal_code = $billingAddress['zipcode'];
        $address->city = $billingAddress['city'];
        $address->region = $billingAddress['state'] ?: '';
        $address->phone = $billingAddress['phone'] ?: '';
        $address->country = $billingCountry->getIso();

        return $address;
    }

    private function createKlarnaDefaultInitializationData(array $basket, array $user, array $finalData, ?int $shopId): KlarnaRequestDataStruct
    {
        $billingAddress = $user['billingaddress'];

        /** @var Country $billingCountry */
        $billingCountry = $this->countryRepository->find($billingAddress['countryId']);
        if ($billingCountry === null) {
            return new KlarnaRequestDataStruct('', '', [], []);
        }

        $billingCountryIso = $billingCountry->getIso();
        $shop = $this->dependencyProviderService->getShop();
        $locale = $this->getLocale($shop);
        $currency = $basket['sCurrencyName'];

        $klarna = new Klarna(
            $billingCountryIso,
            $currency,
            $locale,
            $this->calculateTotalPrice($basket),
            $this->calculateTotalTax($basket),
            $this->createProductsFromBasket($basket)
        );

        try {
            $response = $this->apiClientService->createClient($shopId)->sources()->add($klarna);

            $paymentMethods = array_map(function (array $paymentMethod) {
                return $paymentMethod['identifier'];
            }, $response->getPaymentMethods());

            return new KlarnaRequestDataStruct($response->getTokenId(), $response->getId(), $paymentMethods, $finalData);
        } catch (CheckoutException $checkoutException) {
            $this->loggerService->error(
                sprintf(
                    'An error occurred while creating klarna session: %s',
                    $checkoutException->getMessage()
                ),
                ['exception' => $checkoutException]
            );

            return new KlarnaRequestDataStruct('', '', [], $finalData);
        }
    }

    private function createBasketProducts(array $basket): array
    {
        $products = [];
        foreach ($this->createProductsFromBasket($basket) as $product) {
            $products[] = $product->getValues();
        }

        return $products;
    }

    private function createProductsFromBasket(array $basket): array
    {
        $products = [];
        foreach ($basket['content'] as $item) {
            $itemMode = (int)$item['modus'];

            $productType = self::TYPE_PHYSICAL;
            if ($itemMode === self::MODE_PHYSICAL) {
                $productType = (int)$item['esdarticle'] === 0 ? self::TYPE_PHYSICAL : self::TYPE_DIGITAL;
            }

            if ($itemMode === self::MODE_DISCOUNT) {
                $productType = self::TYPE_DISCOUNT;
            }

            if ($itemMode === self::MODE_SURCHARGE) {
                $productType = self::TYPE_SURCHARGE;
            }

            $reference = $item['ordernumber'];

            // klarna only accepts reference up to 64 characters
            if (mb_strlen($reference) > 64) {
                $reference = mb_substr($item['ordernumber'], 0, 64);
            }

            $name = $item['articlename'];

            // klarna only accepts name up to 255 characters
            if (mb_strlen($name) > 255) {
                $name = mb_substr($item['articlename'], 0, 255);
            }

            $product = new Product();
            $product->type = $productType;
            $product->reference = $reference;
            $product->name = $name;
            $product->quantity = (int)$item['quantity'];
            $product->unit_price = $this->calculatePrice((float)$item['priceNumeric']);
            $product->tax_rate = $this->calculatePrice((float)$item['tax_rate']);
            $product->total_amount = $this->calculatePrice((float)$item['amountNumeric']);
            $product->total_discount_amount = $productType === self::TYPE_DISCOUNT ? $this->calculatePrice((float)$item['amountNumeric']) : 0;
            $product->total_tax_amount = round(((float)str_replace(',', '.', $item['tax'])) * 100, 0);
            $product->product_url = $this->getArticleUrl((int)$item['articleID']);
            $product->image_url = $item['image']['source'];

            $products[] = $product;
        }

        $products[] = $this->createShippingCostProductFromBasket($basket);

        return $products;
    }

    private function createShippingCostProductFromBasket(array $basket): Product
    {
        $shippingCostProduct = new Product();
        $shippingCostProduct->name = self::PRODUCT_SHIPPING_COST_NAME;
        $shippingCostProduct->quantity = 1;
        $shippingCostProduct->unit_price = $this->calculatePrice((float)$basket['sShippingcostsWithTax']);
        $shippingCostProduct->tax_rate = $this->calculatePrice((float)$basket['sShippingcostsTax']);
        $shippingCostProduct->total_amount = $this->calculatePrice((float)$basket['sShippingcostsWithTax']);
        $shippingCostProduct->total_tax_amount = $this->calculatePrice((float)$basket['sShippingcostsWithTax'] - (float)$basket['sShippingcostsNet']);
        $shippingCostProduct->type = self::TYPE_SHIPPING_FEE;

        return $shippingCostProduct;
    }

    private function getLocale(Shop $shop): string
    {
        return str_replace('_', '-', $shop->getLocale()->getLocale());
    }

    private function getArticleUrl(int $articleId): string
    {
        return Shopware()->Front()->Router()->assemble(['controller' => 'Detail', 'module' => 'frontend', 'sArticle' => $articleId]);
    }

    private function calculatePrice(float $price): float
    {
        return abs(round($price * 100, 0));
    }

    private function calculateTotalPrice(array $basket): float
    {
        return $this->calculatePrice((float)$basket['AmountNumeric']);
    }

    private function calculateTotalTax(array $basket): float
    {
        return $this->calculatePrice((float)$basket['AmountNumeric'] - (float)$basket['AmountNetNumeric']);
    }
}
