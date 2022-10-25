<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Tests\Unit\Components\CheckoutApi\Request\Details;

use CkoCheckoutPayment\Components\CheckoutApi\Request\Details\PaymentDetailsRequestService;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentDetailsResponseStruct;
use CkoCheckoutPayment\Components\Logger\LoggerService;
use CkoCheckoutPayment\Components\PaymentMethods\CreditCardPaymentMethod;
use CkoCheckoutPayment\Tests\Mocks\CheckoutApiClientServiceMock;
use CkoCheckoutPayment\Tests\Mocks\ConfigurationServiceMock;
use CkoCheckoutPayment\Tests\Mocks\CountryRepositoryMock;
use CkoCheckoutPayment\Tests\Mocks\DependencyProviderServiceMock;
use CkoCheckoutPayment\Tests\Mocks\PaymentSessionServiceFactoryMock;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;
use Shopware\Components\DependencyInjection\Container;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Country\Country;

class PaymentDetailsRequestServiceTest extends TestCase
{
    /**
     * @var PaymentSessionServiceFactoryMock
     */
    private $paymentSessionServiceFactoryMock;

    /**
     * @var CheckoutApiClientServiceMock
     */
    private $apiClientServiceMock;

    /**
     * @var PaymentDetailsRequestService
     */
    private $paymentDetailsRequestService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->paymentSessionServiceFactoryMock = new PaymentSessionServiceFactoryMock($this->createMock(Container::class));
        $this->paymentSessionServiceFactoryMock->createPaymentSessionService();

        $this->apiClientServiceMock = new CheckoutApiClientServiceMock(CreditCardPaymentMethod::NAME);
        $this->paymentDetailsRequestService = new PaymentDetailsRequestService(
            $this->apiClientServiceMock,
            new ConfigurationServiceMock(),
            new DependencyProviderServiceMock(),
            $this->paymentSessionServiceFactoryMock,
            $this->createMock(LoggerService::class),
            new CountryRepositoryMock($this->createMock(ModelManager::class), new ClassMetadata(Country::class))
        );
    }

    public function testGetPaymentDetails(): void
    {
        $paymentDetails = $this->paymentDetailsRequestService->getPaymentDetails('testThreeDsSessionId', 1);
        static::assertInstanceOf(PaymentDetailsResponseStruct::class, $paymentDetails);
        static::assertSame('pay_abcabc', $paymentDetails->getPaymentId());
        static::assertSame('testReference', $paymentDetails->getReference());
        static::assertInstanceOf(\DateTimeImmutable::class, $paymentDetails->getPlannedDebitDate());
        static::assertSame('EUR', $paymentDetails->getCurrency());
        static::assertSame(5.0, $paymentDetails->getAmount());
        static::assertSame('Authorized', $paymentDetails->getStatus());
        static::assertSame(['planned_debit_date' => '2021-10-07'], $paymentDetails->getSource());
        static::assertTrue($paymentDetails->isSuccessful());
    }
}