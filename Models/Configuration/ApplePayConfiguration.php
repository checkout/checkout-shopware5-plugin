<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Models\Configuration;

use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity;

/**
 * @ORM\Entity()
 * @ORM\Table(name="s_plugin_cko_apple_pay_configuration")
 */
class ApplePayConfiguration extends ModelEntity
{
    public const CSR = 'csr';
    public const PEM = 'pem';
    public const PRIVATE_KEY = 'privateKey';

    public const NETWORK_AMEX = 'amex';
    public const NETWORK_MASTERCARD = 'masterCard';
    public const NETWORK_VISA = 'visa';

    public const CAPABILITIES_CREDIT = 'supportsCredit';
    public const CAPABILITIES_DEBIT = 'supportsDebit';
    public const CAPABILITIES_3DS = 'supports3DS';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="shop_id", type="string", nullable=false)
     */
    private $shopId;

    /**
     * @var null|string
     *
     * @ORM\Column(name="csr", type="text", nullable=true)
     */
    private $csr;

    /**
     * @var null|string
     *
     * @ORM\Column(name="pem", type="text", nullable=true)
     */
    private $pem;

    /**
     * @var null|string
     *
     * @ORM\Column(name="private_key", type="text", nullable=true)
     */
    private $privateKey;

    /**
     * @var string
     *
     * @ORM\Column(name="csr_common_name", type="string", nullable=false)
     */
    private $csrCommonName;

    /**
     * @var string
     *
     * @ORM\Column(name="csr_organization_name", type="string", nullable=false)
     */
    private $csrOrganizationName;

    /**
     * @var string
     *
     * @ORM\Column(name="csr_organization_unit_name", type="string", nullable=false)
     */
    private $csrOrganizationUnitName;

    /**
     * @var string
     *
     * @ORM\Column(name="csr_locality_name", type="string", nullable=false)
     */
    private $csrLocalityName;

    /**
     * @var string
     *
     * @ORM\Column(name="csr_state_or_province_name", type="string", nullable=false)
     */
    private $csrStateOrProvinceName;

    /**
     * @var string
     *
     * @ORM\Column(name="csr_country_name", type="string", nullable=false)
     */
    private $csrCountryName;

    /**
     * @var string
     *
     * @ORM\Column(name="csr_email_address", type="string", nullable=false)
     */
    private $csrEmailAddress;

    /**
     * @var null|string
     *
     * @ORM\Column(name="csr_certificate_password", type="string", nullable=true)
     */
    private $csrCertificatePassword;

    /**
     * @var bool
     *
     * @ORM\Column(name="auto_capture_enabled", type="boolean")
     */
    private $autoCaptureEnabled;

    /**
     * @var string
     *
     * @ORM\Column(name="merchant_id", type="string")
     */
    private $merchantId;

    /**
     * @var bool
     *
     * @ORM\Column(name="supported_networks_amex_enabled", type="boolean")
     */
    private $supportedNetworksAmexEnabled;

    /**
     * @var bool
     *
     * @ORM\Column(name="supported_networks_mastercard_enabled", type="boolean")
     */
    private $supportedNetworksMastercardEnabled;

    /**
     * @var bool
     *
     * @ORM\Column(name="supported_networks_visa_enabled", type="boolean")
     */
    private $supportedNetworksVisaEnabled;

    /**
     * @var bool
     *
     * @ORM\Column(name="merchant_capabilities_credit_enabled", type="boolean")
     */
    private $merchantCapabilitiesCreditEnabled;

    /**
     * @var bool
     *
     * @ORM\Column(name="merchant_capabilities_debit_enabled", type="boolean")
     */
    private $merchantCapabilitiesDebitEnabled;

    /**
     * @var bool
     *
     * @ORM\Column(name="merchant_capabilities_3ds_enabled", type="boolean")
     */
    private $merchantCapabilities3dsEnabled;

    /**
     * @var string
     *
     * @ORM\Column(name="button_color", type="string")
     */
    private $buttonColor;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getShopId(): string
    {
        return $this->shopId;
    }

    /**
     * @param string $shopId
     */
    public function setShopId(string $shopId): void
    {
        $this->shopId = $shopId;
    }

    /**
     * @return string|null
     */
    public function getCsr(): ?string
    {
        return $this->csr;
    }

    /**
     * @param string|null $csr
     */
    public function setCsr(?string $csr): void
    {
        $this->csr = $csr;
    }

    /**
     * @return string|null
     */
    public function getPem(): ?string
    {
        return $this->pem;
    }

    /**
     * @param string|null $pem
     */
    public function setPem(?string $pem): void
    {
        $this->pem = $pem;
    }

    /**
     * @return string|null
     */
    public function getPrivateKey(): ?string
    {
        return $this->privateKey;
    }

    /**
     * @param string|null $privateKey
     */
    public function setPrivateKey(?string $privateKey): void
    {
        $this->privateKey = $privateKey;
    }

    /**
     * @return string
     */
    public function getCsrCommonName(): string
    {
        return $this->csrCommonName;
    }

    /**
     * @param string $csrCommonName
     */
    public function setCsrCommonName(string $csrCommonName): void
    {
        $this->csrCommonName = $csrCommonName;
    }

    /**
     * @return string
     */
    public function getCsrOrganizationName(): string
    {
        return $this->csrOrganizationName;
    }

    /**
     * @param string $csrOrganizationName
     */
    public function setCsrOrganizationName(string $csrOrganizationName): void
    {
        $this->csrOrganizationName = $csrOrganizationName;
    }

    /**
     * @return string
     */
    public function getCsrOrganizationUnitName(): string
    {
        return $this->csrOrganizationUnitName;
    }

    /**
     * @param string $csrOrganizationUnitName
     */
    public function setCsrOrganizationUnitName(string $csrOrganizationUnitName): void
    {
        $this->csrOrganizationUnitName = $csrOrganizationUnitName;
    }

    /**
     * @return string
     */
    public function getCsrLocalityName(): string
    {
        return $this->csrLocalityName;
    }

    /**
     * @param string $csrLocalityName
     */
    public function setCsrLocalityName(string $csrLocalityName): void
    {
        $this->csrLocalityName = $csrLocalityName;
    }

    /**
     * @return string
     */
    public function getCsrStateOrProvinceName(): string
    {
        return $this->csrStateOrProvinceName;
    }

    /**
     * @param string $csrStateOrProvinceName
     */
    public function setCsrStateOrProvinceName(string $csrStateOrProvinceName): void
    {
        $this->csrStateOrProvinceName = $csrStateOrProvinceName;
    }

    /**
     * @return string
     */
    public function getCsrCountryName(): string
    {
        return $this->csrCountryName;
    }

    /**
     * @param string $csrCountryName
     */
    public function setCsrCountryName(string $csrCountryName): void
    {
        $this->csrCountryName = $csrCountryName;
    }

    /**
     * @return string
     */
    public function getCsrEmailAddress(): string
    {
        return $this->csrEmailAddress;
    }

    /**
     * @param string $csrEmailAddress
     */
    public function setCsrEmailAddress(string $csrEmailAddress): void
    {
        $this->csrEmailAddress = $csrEmailAddress;
    }

    /**
     * @return string|null
     */
    public function getCsrCertificatePassword(): ?string
    {
        return $this->csrCertificatePassword;
    }

    /**
     * @param string|null $csrCertificatePassword
     */
    public function setCsrCertificatePassword(?string $csrCertificatePassword): void
    {
        $this->csrCertificatePassword = $csrCertificatePassword;
    }

    /**
     * @return bool
     */
    public function isAutoCaptureEnabled(): bool
    {
        return $this->autoCaptureEnabled;
    }

    /**
     * @param bool $autoCaptureEnabled
     */
    public function setAutoCaptureEnabled(bool $autoCaptureEnabled): void
    {
        $this->autoCaptureEnabled = $autoCaptureEnabled;
    }

    /**
     * @return string
     */
    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    /**
     * @param string $merchantId
     */
    public function setMerchantId(string $merchantId): void
    {
        $this->merchantId = $merchantId;
    }

    /**
     * @return bool
     */
    public function isSupportedNetworksAmexEnabled(): bool
    {
        return $this->supportedNetworksAmexEnabled;
    }

    /**
     * @param bool $supportedNetworksAmexEnabled
     */
    public function setSupportedNetworksAmexEnabled(bool $supportedNetworksAmexEnabled): void
    {
        $this->supportedNetworksAmexEnabled = $supportedNetworksAmexEnabled;
    }

    /**
     * @return bool
     */
    public function isSupportedNetworksMastercardEnabled(): bool
    {
        return $this->supportedNetworksMastercardEnabled;
    }

    /**
     * @param bool $supportedNetworksMastercardEnabled
     */
    public function setSupportedNetworksMastercardEnabled(bool $supportedNetworksMastercardEnabled): void
    {
        $this->supportedNetworksMastercardEnabled = $supportedNetworksMastercardEnabled;
    }

    /**
     * @return bool
     */
    public function isSupportedNetworksVisaEnabled(): bool
    {
        return $this->supportedNetworksVisaEnabled;
    }

    /**
     * @param bool $supportedNetworksVisaEnabled
     */
    public function setSupportedNetworksVisaEnabled(bool $supportedNetworksVisaEnabled): void
    {
        $this->supportedNetworksVisaEnabled = $supportedNetworksVisaEnabled;
    }

    /**
     * @return bool
     */
    public function isMerchantCapabilitiesCreditEnabled(): bool
    {
        return $this->merchantCapabilitiesCreditEnabled;
    }

    /**
     * @param bool $merchantCapabilitiesCreditEnabled
     */
    public function setMerchantCapabilitiesCreditEnabled(bool $merchantCapabilitiesCreditEnabled): void
    {
        $this->merchantCapabilitiesCreditEnabled = $merchantCapabilitiesCreditEnabled;
    }

    /**
     * @return bool
     */
    public function isMerchantCapabilitiesDebitEnabled(): bool
    {
        return $this->merchantCapabilitiesDebitEnabled;
    }

    /**
     * @param bool $merchantCapabilitiesDebitEnabled
     */
    public function setMerchantCapabilitiesDebitEnabled(bool $merchantCapabilitiesDebitEnabled): void
    {
        $this->merchantCapabilitiesDebitEnabled = $merchantCapabilitiesDebitEnabled;
    }

    /**
     * @return bool
     */
    public function isMerchantCapabilities3dsEnabled(): bool
    {
        return $this->merchantCapabilities3dsEnabled;
    }

    /**
     * @param bool $merchantCapabilities3dsEnabled
     */
    public function setMerchantCapabilities3dsEnabled(bool $merchantCapabilities3dsEnabled): void
    {
        $this->merchantCapabilities3dsEnabled = $merchantCapabilities3dsEnabled;
    }

    /**
     * @return string
     */
    public function getButtonColor(): string
    {
        return $this->buttonColor;
    }

    /**
     * @param string $buttonColor
     */
    public function setButtonColor(string $buttonColor): void
    {
        $this->buttonColor = $buttonColor;
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
}
