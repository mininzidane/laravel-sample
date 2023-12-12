<?php

declare(strict_types=1);

namespace App\Helpers;

class CurrencyConverter
{
    private const SATOSHI_IN_BTC = 100000000;

    public static function satoshiToBitcoin(int $amount): string
    {
        $zeroCount = strlen((string) self::SATOSHI_IN_BTC) - 1;
        return sprintf("%.{$zeroCount}f", ($amount / self::SATOSHI_IN_BTC));
    }
}
