<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Subscribers\Documents;

use CkoCheckoutPayment\Components\Configuration\ConfigurationServiceInterface;
use CkoCheckoutPayment\Components\PaymentMethods\SepaPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethodValidator\PaymentMethodValidatorServiceInterface;
use CkoCheckoutPayment\Models\Configuration\SepaConfiguration;
use Doctrine\DBAL\Connection;
use Enlight\Event\SubscriberInterface;

class InvoiceDocumentSubscriber implements SubscriberInterface
{
    private const DOCUMENT_ELEMENT_VALUE_SUFFIX = '_Value';
    private const DOCUMENT_ELEMENT_STYLE_SUFFIX = '_Style';

    private const TRANSLATION_OBJECT_TYPE = 'documents';

    /**
     * @var ConfigurationServiceInterface
     */
    private $configurationService;

    /**
     * @var PaymentMethodValidatorServiceInterface
     */
    private $paymentMethodValidatorService;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var \Shopware_Components_Translation
     */
    private $translationService;

    public function __construct(
        ConfigurationServiceInterface $configurationService,
        PaymentMethodValidatorServiceInterface $paymentMethodValidatorService,
        Connection $connection,
        \Shopware_Components_Translation $translationService
    ) {
        $this->configurationService = $configurationService;
        $this->paymentMethodValidatorService = $paymentMethodValidatorService;
        $this->connection = $connection;
        $this->translationService = $translationService;
    }

    public static function getSubscribedEvents()
    {
        return [
            'Shopware_Components_Document::assignValues::after' => 'onRenderDocument',
        ];
    }

    public function onRenderDocument(\Enlight_Hook_HookArgs $args): void
    {
        /** @var \Shopware_Components_Document $subject */
        $subject = $args->getSubject();
        $view = $subject->_view;

        $view->assign('ckoShowAdditionalInformation', false);

        $orderData = (array)$view->getTemplateVars('Order');
        $order = $orderData['_order'];
        $mandateReference = $order['attributes']['cko_mandate_reference'] ?? null;
        $paymentMethodName = $orderData['_payment']['name'];

        if (!$this->paymentMethodValidatorService->isCheckoutPaymentMethod($paymentMethodName) || $paymentMethodName !== SepaPaymentMethod::NAME) {
            return;
        }

        if (empty($order['transactionID']) || empty($order['temporaryID']) || empty($mandateReference)) {
            return;
        }

        try {
            /** @var SepaConfiguration $configuration */
            $configuration = $this->configurationService->getPaymentMethodConfiguration(SepaPaymentMethod::NAME, (int)$order['subshopID']);

            $view->assign('ckoShowAdditionalInformation', true);
            $view->assign('ckoCustomDocumentElements', $this->getCustomDocumentElements((int)$subject->_typID, (int)$subject->_order->order->language));
            $view->assign('ckoSepaMandateReference', $mandateReference);
            $view->assign('ckoSepaMandateCreditorName', $configuration->getMandateCreditorName());
            $view->assign('ckoSepaMandateCreditorId', $configuration->getMandateCreditorId());
            $view->assign('ckoSepaMandateCreditorAddressFirst', $configuration->getMandateCreditorAddressFirst());
            $view->assign('ckoSepaMandateCreditorAddressSecond', $configuration->getMandateCreditorAddressSecond());
            $view->assign('ckoSepaMandateCreditorCountry', $configuration->getMandateCreditorCountry());
        } catch (\RuntimeException $exception) {
            $view->assign('ckoShowAdditionalInformation', false);
            $view->assign('ckoCustomDocumentElements', []);
            $view->assign('ckoSepaMandateReference');
            $view->assign('ckoSepaMandateCreditorName');
            $view->assign('ckoSepaMandateCreditorId');
            $view->assign('ckoSepaMandateCreditorAddressFirst');
            $view->assign('ckoSepaMandateCreditorAddressSecond');
            $view->assign('ckoSepaMandateCreditorCountry');
        }
    }

    private function getCustomDocumentElements(int $documentId, int $language, string $documentType = self::TRANSLATION_OBJECT_TYPE): array
    {
        $documentElements = $this->connection->createQueryBuilder()
            ->select(['name', 'value', 'style'])
            ->from('s_core_documents_box')
            ->where('name LIKE "%CkoCheckoutPayment%"')
            ->andWhere('documentId = :documentId')
            ->setParameter('documentId', $documentId)
            ->execute()
            ->fetchAll();

        $translation = $this->translationService->read($language, $documentType);

        $customDocumentElements = [];
        foreach ($documentElements as $documentElement) {
            $templateName = $documentElement['name'];

            $customDocumentElements[$templateName] = [
                'value' => $documentElement['value'],
                'style' => $documentElement['style']
            ];

            $valueTranslation = $translation[$templateName. self::DOCUMENT_ELEMENT_VALUE_SUFFIX];

            if (!empty($valueTranslation)) {
                $customDocumentElements[$templateName]['value'] = $valueTranslation;
            }

            $styleTranslation = $translation[$templateName. self::DOCUMENT_ELEMENT_STYLE_SUFFIX];

            if (!empty($styleTranslation)) {
                $customDocumentElements[$templateName]['style'] = $styleTranslation;
            }
        }

        return $customDocumentElements;
    }
}