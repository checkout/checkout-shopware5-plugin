<?php

declare(strict_types=1);

use CkoCheckoutPayment\Components\Applepay\CertificateServiceInterface;
use CkoCheckoutPayment\Components\ApplePay\Exception\CertificateException;
use CkoCheckoutPayment\Components\Configuration\ConfigurationServiceInterface;
use CkoCheckoutPayment\Components\DependencyProvider\DependencyProviderServiceInterface;
use CkoCheckoutPayment\Components\Logger\LoggerServiceInterface;
use CkoCheckoutPayment\Components\PaymentMethods\ApplePayPaymentMethod;
use CkoCheckoutPayment\Models\Configuration\ApplePayConfiguration;
use Shopware\Components\CSRFWhitelistAware;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Shopware_Controllers_Backend_CkoSetupApplePay extends Shopware_Controllers_Backend_Application implements CSRFWhitelistAware
{
	protected $model = ApplePayConfiguration::class;

	private const UPLOAD_VERIFY_ALLOWED_EXTENSIONS = ['txt'];
	private const UPLOAD_VERIFY_DIRECTORY_NAME = '/.well-known';

	private const UPLOAD_CERTIFICATE_ALLOWED_EXTENSIONS = ['cer'];

	private const SNIPPET_NAMESPACE = 'backend/cko_setup/controller/apple_pay';

	/**
	 * @var CertificateServiceInterface
	 */
	private $certificateService;

	/**
	 * @var DependencyProviderServiceInterface
	 */
	private $dependencyProviderService;

	/**
	 * @var ConfigurationServiceInterface
	 */
	private $configurationService;

	/**
	 * @var LoggerServiceInterface
	 */
	private $loggerService;

	/**
	 * @var \Enlight_Components_Snippet_Manager
	 */
	private $snippetManager;

	public function preDispatch()
	{
		parent::preDispatch();

		$this->certificateService = $this->get('cko_checkout_payment.components.apple_pay.certificate_service');
		$this->dependencyProviderService = $this->get('cko_checkout_payment.components.dependency_provider.dependency_provider_service');
		$this->configurationService = $this->get('cko_checkout_payment.components.configuration.configuration_service');
		$this->loggerService = $this->get('cko_checkout_payment.components.logger.logger_service');
		$this->snippetManager = $this->get('snippets');
	}

	public function checkRequirementsAction(): void
	{
		$snippetNamespace = $this->snippetManager->getNamespace(self::SNIPPET_NAMESPACE);

		$this->View()->assign([
			'success' => extension_loaded('openssl'),
			'message' => $snippetNamespace->get('notification/growl/domainVerifyFile/openSslNotAvailableMessage')
		]);
	}

	public function loadConfigurationAction(): void
    {
        try {
            /** @var ApplePayConfiguration $configuration */
            $configuration = $this->configurationService->getPaymentMethodConfiguration(
                ApplePayPaymentMethod::NAME,
                (int)$this->Request()->getParam('shopId'),
                false
            );

            $this->View()->assign([
                'success' => true,
                'configuration' => $configuration->toArray()
            ]);
        } catch (\RuntimeException $exception) {
            // catch exception so it will possible to save new configuration

            $this->View()->assign([
                'success' => true,
                'configuration' => []
            ]);
        }
    }

	public function getDomainRootPathAction(): void
	{
		$rootPath = $this->container->getParameter('kernel.root_dir');
		$rootPath = str_replace('/vendor/shopware/shopware', '', $rootPath);

		$this->View()->assign([
			'path' => $rootPath,
			'success' => true,
		]);
	}

	public function generateCsrCertificateAction(): void
	{
		$snippetNamespace = $this->snippetManager->getNamespace(self::SNIPPET_NAMESPACE);

		$shopId = (int)$this->Request()->getParam('shopId');

		if (!$shopId) {
			$this->View()->assign([
				'success' => false,
				'message' => $snippetNamespace->get('notification/growl/noShopSelectedMessage')
			]);

			return;
		}

		$shop = $this->dependencyProviderService->getShop($shopId);
		$certificateName = sprintf('upload_certificate_applepay_%s_%s.csr', $shop->getHost(), $this->configurationService->getApplePayEnvironment($shop->getId()));

		try {
			$pemCertificate = $this->certificateService->generateCsrCertificate($shopId);

			$this->Front()->Plugins()->ViewRenderer()->setNoRender();
			$this->Front()->Plugins()->Json()->setRenderer(false);

			$this->Response()->setHeader('cache-control', 'public', true);
			$this->Response()->setHeader('content-description', 'File Transfer');
			$this->Response()->setHeader('Content-Disposition', 'attachment; filename="' . $certificateName . '"', true);
			$this->Response()->setHeader('Content-Type', 'application/csr', true);
			$this->Response()->setHeader('content-transfer-encoding', 'binary');
			$this->Response()->setHeader('content-length', strlen($pemCertificate));

			$this->Response()->setBody($pemCertificate);
		} catch (CertificateException | \RuntimeException $certificateException) {
			$this->loggerService->error($certificateException->getMessage());

			$this->View()->assign([
				'success' => false,
				'message' => $certificateException->getMessage()
			]);
		}
	}

	public function generatePemCertificateAction(): void
	{
		$snippetNamespace = $this->snippetManager->getNamespace(self::SNIPPET_NAMESPACE);

		$shopId = (int)$this->Request()->getParam('shopId');

		if (!$shopId) {
			$this->View()->assign([
				'success' => false,
				'message' => $snippetNamespace->get('notification/growl/noShopSelectedMessage')
			]);

			return;
		}

		/** @var UploadedFile $applePayCertificateFile */
		$applePayCertificateFile = $this->Request()->files->get('applePayCertificateFile');

		if (!$applePayCertificateFile) {
			$this->View()->assign([
				'success' => false,
				'message' => $snippetNamespace->get('notification/growl/applePayCertificateFile/noFileSelectedMessage')
			]);

			return;
		}

		$extension = strtolower($applePayCertificateFile->getClientOriginalExtension());

		if (!in_array($extension, self::UPLOAD_CERTIFICATE_ALLOWED_EXTENSIONS, true)) {
			$this->View()->assign([
				'success' => false,
				'message' => $snippetNamespace->get('notification/growl/applePayCertificateFile/invalidCertificateFileMessage')
			]);

			return;
		}

		try {
			$certificateContent = file_get_contents($applePayCertificateFile->getPathname());
			$pemCertificate = $this->certificateService->generatePemCertificate($certificateContent, $shopId);

			$shop = $this->dependencyProviderService->getShop($shopId);
			$certificateName = sprintf('certificate_applepay_%s_%s.pem', $shop->getHost(), $this->configurationService->getApplePayEnvironment($shop->getId()));

			$this->Front()->Plugins()->ViewRenderer()->setNoRender();
			$this->Front()->Plugins()->Json()->setRenderer(false);

			$this->Response()->setHeader('cache-control', 'public', true);
			$this->Response()->setHeader('content-description', 'File Transfer');
			$this->Response()->setHeader('Content-Disposition', 'attachment; filename="' . $certificateName . '"', true);
			$this->Response()->setHeader('Content-Type', 'application/x-pem-file', true);
			$this->Response()->setHeader('content-transfer-encoding', 'binary');
			$this->Response()->setHeader('content-length', strlen($pemCertificate));

			$this->Response()->setBody($pemCertificate);
		} catch (CertificateException | \RuntimeException $certificateException) {
			$this->loggerService->error($certificateException->getMessage());

			$this->View()->assign([
				'success' => false,
				'message' => $certificateException->getMessage()
			]);
		}
	}

	public function uploadDomainVerifyFileAction(): void
	{
		$snippetNamespace = $this->snippetManager->getNamespace(self::SNIPPET_NAMESPACE);
		$applePayDomainVerifyFile = $this->Request()->files->get('applePayDomainVerifyFile');
		$applePayDomainPathField = $this->Request()->getPost('domainVerificationFilePathField');

		if (!$applePayDomainVerifyFile) {
			$this->View()->assign([
				'success' => false,
				'message' => $snippetNamespace->get('notification/growl/domainVerifyFile/noFileSelectedMessage')
			]);

			return;
		}
		/** @var UploadedFile $applePayDomainVerifyFile */

		if (!$applePayDomainVerifyFile instanceof UploadedFile) {
			$this->View()->assign([
				'success' => false,
				'message' => $snippetNamespace->get('notification/growl/domainVerifyFile/invalidVerificationFileMessage')
			]);

			return;
		}

		if (!$applePayDomainPathField) {
			$this->View()->assign([
				'success' => false,
				'message' => $snippetNamespace->get('notification/growl/domainVerifyFile/unableToCreateVerificationDirectoryMessage')
			]);

			return;
		}

		$extension = strtolower($applePayDomainVerifyFile->getClientOriginalExtension());

		if (!in_array($extension, self::UPLOAD_VERIFY_ALLOWED_EXTENSIONS, true)) {
			$this->View()->assign([
				'success' => false,
				'message' => $snippetNamespace->get('notification/growl/domainVerifyFile/invalidVerificationFileMessage')
			]);

			return;
		}

		$applePayDomainPathField .= self::UPLOAD_VERIFY_DIRECTORY_NAME;

		if (!$this->createApplePayVerificationDirectory($applePayDomainPathField)) {
			$this->View()->assign([
				'success' => false,
				'message' => $snippetNamespace->get('notification/growl/domainVerifyFile/unableToCreateVerificationDirectoryMessage')
			]);

			return;
		}

		try {
			$applePayDomainVerifyFile->move($applePayDomainPathField, $applePayDomainVerifyFile->getClientOriginalName());

			$this->View()->assign([
				'success' => true,
				'message' => null
			]);
		} catch (\Throwable $e) {
			$this->loggerService->error(
				'uploading apple pay domain verify file failed', [
				'message' => $e->getMessage(),
				'trace' => $e->getTraceAsString()
			]);

			$this->View()->assign([
				'success' => false,
				'message' => $e->getMessage()
			]);
		}
	}

	private function createApplePayVerificationDirectory($applePayDomainVerifyPath): bool
	{
		// Check if directory exists, create if not
		return !(!is_dir($applePayDomainVerifyPath) && !mkdir($applePayDomainVerifyPath) && !is_dir($applePayDomainVerifyPath));
	}

    public function getWhitelistedCSRFActions(): array
    {
        return [
            'generateCsrCertificate'
        ];
    }
}
