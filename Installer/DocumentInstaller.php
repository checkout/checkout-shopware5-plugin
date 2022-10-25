<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Installer;

use Doctrine\DBAL\Connection;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use Shopware\Models\Document\Document;
use Shopware\Models\Document\Element as DocumentElement;
use Shopware\Models\Shop\Shop;

class DocumentInstaller implements InstallerInterface
{
    private const DOCUMENT_INVOICE_ID = 1;
    private const DOCUMENT_ELEMENT_ADDRESS_NAME = 'CkoCheckoutPayment_Address';
    private const DOCUMENT_ELEMENT_DISPATCH_NAME = 'CkoCheckoutPayment_Dispatch';
    private const DOCUMENT_ELEMENT_VALUE_SUFFIX = '_Value';

    private const DOCUMENT_ELEMENTS = [
        [
            'name' => self::DOCUMENT_ELEMENT_ADDRESS_NAME,
            'translations' => [
                'de' => __DIR__ . '/../Resources/installer/documents/de/cko_checkout_payment_address.tpl',
                'en' => __DIR__ . '/../Resources/installer/documents/en/cko_checkout_payment_address.tpl'
            ]
        ],
        [
            'name' => self::DOCUMENT_ELEMENT_DISPATCH_NAME,
            'translations' => [
                'de' => __DIR__ . '/../Resources/installer/documents/de/cko_checkout_payment_dispatch.tpl',
                'en' => __DIR__ . '/../Resources/installer/documents/en/cko_checkout_payment_dispatch.tpl'
            ]
        ]
    ];

    private const GERMAN_LOCALE_PREFIX = 'de_';
    private const GERMAN_LOCALE_SUFFIX = '_de';

    private const TRANSLATION_OBJECT_TYPE = 'documents';

    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var \Shopware_Components_Translation
     */
    private $translationService;

    public function __construct(
        ModelManager $modelManager,
        Connection $connection,
        \Shopware_Components_Translation $translationService
    ) {
        $this->modelManager = $modelManager;
        $this->connection = $connection;
        $this->translationService = $translationService;
    }

    public function install(InstallContext $context): void
    {
        $this->createDocumentElements(self::DOCUMENT_ELEMENTS);
    }

    public function update(UpdateContext $context): void
    {
        // nothing todo here
    }

    public function uninstall(UninstallContext $context): void
    {
        if ($context->keepUserData()) {
            return;
        }

        $this->connection->exec('DELETE FROM s_core_documents_box WHERE `name` LIKE "%CkoCheckoutPayment%"');
    }

    public function deactivate(DeactivateContext $context): void
    {
        // nothing todo here
    }

    public function activate(ActivateContext $context): void
    {
        // nothing todo here
    }

    private function createDocumentElements(array $documentElements): void
    {
        foreach ($documentElements as $documentElement) {
            $documentElementTranslationGerman = file_get_contents($documentElement['translations']['de']);
            if (!$documentElementTranslationGerman) {
                return;
            }

            $documentElementTranslationEnglish = file_get_contents($documentElement['translations']['en']);
            if (!$documentElementTranslationEnglish) {
                return;
            }

            $this->createDocumentElement($documentElement['name'], $documentElementTranslationGerman, $documentElementTranslationEnglish);
        }
    }

    private function createDocumentElement(string $name, string $value, string $valueTranslated, int $documentId = self::DOCUMENT_INVOICE_ID): void
    {
        $documentRepository = $this->modelManager->getRepository(Document::class);
        $documentElementRepository = $this->modelManager->getRepository(DocumentElement::class);

        /** @var Document $document */
        $document = $documentRepository->findOneBy(['id' => $documentId]);

        if ($document === null) {
            return;
        }

        $existingDocumentElement = $documentElementRepository->findOneBy([
            'name' => $name,
            'documentId' => $documentId
        ]);

        if ($existingDocumentElement !== null) {
            return;
        }

        $documentElement = new DocumentElement();
        $documentElement->setName($name);
        $documentElement->setValue($value);
        $documentElement->setStyle('');
        $documentElement->setDocument($document);

        $this->modelManager->persist($documentElement);
        $this->modelManager->flush();

        $translations[$name . self::DOCUMENT_ELEMENT_VALUE_SUFFIX] = $valueTranslated;
        $this->writeDocumentElementsTranslations($translations);
    }

    private function writeDocumentElementsTranslations(array $translations): void
    {
        $shopRepository = $this->modelManager->getRepository(Shop::class);

        foreach ($shopRepository->findAll() as $shop) {
            /** @var Shop $shop */
            $locale = $shop->getLocale()->getLocale();
            if ((bool)mb_stripos($locale, self::GERMAN_LOCALE_PREFIX) || (bool)mb_stripos($locale, self::GERMAN_LOCALE_SUFFIX)) {
                // skip all german shops since we already created the document default by german
                continue;
            }

            $this->translationService->write($shop->getId(), self::TRANSLATION_OBJECT_TYPE, 1, $translations, true);
        }
    }
}