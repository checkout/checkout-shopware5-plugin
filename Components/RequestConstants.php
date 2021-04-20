<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components;

final class RequestConstants
{
    public const TOKEN = 'ckoToken';
    public const BASKET_SIGNATURE = 'ckoBasketSignature';

    public const CC_SAVE_CARD = 'ckoCreditcardSaveCard';
    public const CC_EXPIRY_DATE = 'ckoCreditcardExpiryDate';
    public const CC_LAST_4 = 'ckoCreditcardLast4';
    public const SOURCE_ID = 'ckoSourceId';
    public const BIC = 'ckoBic';
    public const IBAN = 'ckoIban';

    public const GOOGLE_PAY_SIGNATURE = 'ckoGooglePaySignature';
    public const GOOGLE_PAY_PROTOCOL_VERSION = 'ckoGooglePayProtocolVersion';
    public const GOOGLE_PAY_SIGNED_MESSAGE = 'ckoGooglePaySignedMessage';

    public const APPLE_PAY_TRANSACTION_ID = 'ckoApplePayTransactionId';
    public const APPLE_PAY_PUBLIC_KEY_HASH = 'ckoApplePayPublicKeyHash';
    public const APPLE_PAY_EPHEMERAL_PUBLIC_KEY = 'ckoEphemeralPublicKey';
    public const APPLE_PAY_VERSION = 'ckoApplePayVersion';
    public const APPLE_PAY_SIGNATURE = 'ckoApplePaySignature';
    public const APPLE_PAY_DATA = 'ckoApplePayData';

    public static function getConstants(): array
    {
        return [
            self::TOKEN,
            self::BASKET_SIGNATURE,
            self::CC_EXPIRY_DATE,
            self::CC_LAST_4,
            self::CC_SAVE_CARD,
            self::SOURCE_ID,
            self::BIC,
            self::IBAN,
            self::GOOGLE_PAY_SIGNATURE,
            self::GOOGLE_PAY_PROTOCOL_VERSION,
            self::GOOGLE_PAY_SIGNED_MESSAGE,
            self::APPLE_PAY_TRANSACTION_ID,
            self::APPLE_PAY_PUBLIC_KEY_HASH,
            self::APPLE_PAY_EPHEMERAL_PUBLIC_KEY,
            self::APPLE_PAY_VERSION,
            self::APPLE_PAY_SIGNATURE,
            self::APPLE_PAY_DATA
        ];
    }
}
