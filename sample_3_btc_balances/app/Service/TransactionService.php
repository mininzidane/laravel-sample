<?php

declare(strict_types=1);

namespace App\Service;

use App\Models\Partner;
use App\Models\SiteBalance;
use App\Models\User;

class TransactionService
{
    public const ERROR_CODE_INSUFFICIENT_FUNDS = 1;
    public const ERROR_CODE_INTERNAL_ERROR = 2;
    public const ERROR_CODE_INVALID_AMOUNT = 3;

    public const SITE_FEE_PERCENT = 1;
    public const PARTNER_FEE_PERCENT = 5;
    public const USER_CASHBACK_PERCENT = 10;

    public function __construct(
        private readonly DummyMoneyExportService $dummyMoneyExportService,
    ) {}

    public function withdrawAmountFromUser(int $amount, User $user, Partner $partner): true|int
    {
        if ($amount <= 1) {
            return self::ERROR_CODE_INVALID_AMOUNT;
        }
        if ($user->balance < $amount) {
            return self::ERROR_CODE_INSUFFICIENT_FUNDS;
        }

        \DB::beginTransaction();
        try {
            $user->balance -= $amount;
            $siteFee = (int)ceil($amount * self::SITE_FEE_PERCENT / 100);
            $partnerFee = (int) floor($siteFee * self::PARTNER_FEE_PERCENT / 100);
            $userCashback = (int) floor($siteFee * self::USER_CASHBACK_PERCENT / 100);
            SiteBalance::changeBalance($siteFee - $partnerFee - $userCashback);
            $partner->balance += $partnerFee;
            $user->cashback += $userCashback;
            if (!$user->save()) {
                throw new \Exception('Error on saving user record');
            }

            if (!$partner->save()) {
                throw new \Exception('Error on saving partner record');
            }

            if (!$this->dummyMoneyExportService->exportToUser($amount - $siteFee)) {
                throw new \Exception('Error on user money export');
            }

            \DB::commit();

        } catch (\Throwable $e) {
            \DB::rollBack();
            \Log::error($e->getMessage());
            return self::ERROR_CODE_INTERNAL_ERROR;
        }

        return true;
    }

    public function withdrawFromPartner(Partner $partner): true|int
    {
        if ($partner->balance <= 0) {
            return self::ERROR_CODE_INSUFFICIENT_FUNDS;
        }

        \DB::beginTransaction();
        try {
            $balance = $partner->balance;
            $partner->balance = 0;
            if (!$partner->save()) {
                throw new \Exception('Error on saving partner record');
            }

            if (!$this->dummyMoneyExportService->exportToPartner($balance)) {
                throw new \Exception('Error on partner money export');
            }

            \DB::commit();

        } catch (\Throwable $e) {
            \DB::rollBack();
            \Log::error($e->getMessage());
            return self::ERROR_CODE_INTERNAL_ERROR;
        }

        return true;
    }

    public function transferCashbackToUser(User $user): true|int
    {
        if ($user->cashback <= 0) {
            return self::ERROR_CODE_INSUFFICIENT_FUNDS;
        }

        $user->balance += $user->cashback;
        $user->cashback = 0;
        if (!$user->save()) {
            return self::ERROR_CODE_INTERNAL_ERROR;
        }

        return true;
    }

    public function getErrorLabel(int $errorCode): string
    {
        return match ($errorCode) {
            self::ERROR_CODE_INSUFFICIENT_FUNDS => 'Insufficient funds',
            self::ERROR_CODE_INTERNAL_ERROR => 'Internal error',
            self::ERROR_CODE_INVALID_AMOUNT => 'Invalid amount',
            default => 'Unknown error',
        };
    }
}
