<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\Logger;

use CkoCheckoutPayment\Components\Configuration\ConfigurationServiceInterface;
use Shopware\Components\Logger;

class LoggerService implements LoggerServiceInterface
{
    /**
     * @var Logger
     */
    private $baseLogger;

    /**
     * @var ConfigurationServiceInterface
     */
    private $configurationService;

    public function __construct(
        Logger $baseLogger,
        ConfigurationServiceInterface $configurationService
    ) {
        $this->baseLogger = $baseLogger;
        $this->configurationService = $configurationService;
    }

    public function info(string $message, array $context = []): void
    {
        $this->baseLogger->info($message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->baseLogger->warning($message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        // todo more detail handled request params etc.. response
        $this->baseLogger->error($message, $context);
    }
}
