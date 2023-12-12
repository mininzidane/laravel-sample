<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Partner;
use App\Models\SiteBalance;
use App\Models\User;
use App\Service\DummyMoneyExportService;
use App\Service\TransactionService;
use Tests\TestCase;

class TransactionServiceTest extends TestCase
{
    private TransactionService $transactionService;

    protected function setUp(): void
    {
        parent::setUp();
        $dummyMoneyExportServiceMock = $this->createMock(DummyMoneyExportService::class);
        $dummyMoneyExportServiceMock->method('exportToUser')->willReturn(true);
        $dummyMoneyExportServiceMock->method('exportToPartner')->willReturn(true);
        $this->transactionService = new TransactionService($dummyMoneyExportServiceMock);
        \DB::beginTransaction();
    }

    protected function tearDown(): void
    {
        \DB::rollBack();
        parent::tearDown();
    }

    /**
     * @dataProvider withdrawDataProvider
     */
    public function testWithdrawAmountFromUser(
        int $amount,
        int $siteBalanceDiff,
        int $partnerBalanceDiff,
        int $userCashbackDiff
    ): void {
        $user = User::first();
        $partner = Partner::first();
        $balance = $user->balance;
        $partnerBalance = $partner->balance;
        $siteBalance = SiteBalance::getBalance();
        $userCashback = $user->cashback;
        $result = $this->transactionService->withdrawAmountFromUser($amount, $user, $partner);
        $this->assertTrue($result);
        $this->assertSame($balance - $amount, $user->balance);
        $this->assertSame($siteBalance + $siteBalanceDiff, SiteBalance::getBalance());
        $this->assertSame($partnerBalance + $partnerBalanceDiff, $partner->balance);
        $this->assertSame($userCashback + $userCashbackDiff, $user->cashback);
    }

    public static function withdrawDataProvider(): \Generator
    {
        yield [10, 1, 0, 0];
        yield [2, 1, 0, 0];
        yield [10000, 85, 5, 10];
        yield [26123, 223, 13, 26];
    }

    public function testWithdrawAmountFromUserInsufficientFunds(): void
    {
        $user = User::first();
        $partner = Partner::first();
        $result = $this->transactionService->withdrawAmountFromUser($user->balance + 1, $user, $partner);
        $this->assertSame(TransactionService::ERROR_CODE_INSUFFICIENT_FUNDS, $result);
    }

    public function testWithdrawFromPartner(): void
    {
        $partner = Partner::first();
        $result = $this->transactionService->withdrawFromPartner($partner);
        $this->assertTrue($result);
        $this->assertSame(0, $partner->balance);
    }

    public function testWithdrawFromPartnerInsufficientFunds(): void
    {
        $partner = Partner::first();
        $partner->balance = 0;
        $result = $this->transactionService->withdrawFromPartner($partner);
        $this->assertSame(TransactionService::ERROR_CODE_INSUFFICIENT_FUNDS, $result);
    }

    public function testTransferCashbackToUser(): void
    {
        $user = User::first();
        $resultBalance = $user->cashback + $user->balance;
        $result = $this->transactionService->transferCashbackToUser($user);
        $this->assertTrue($result);
        $this->assertSame(0, $user->cashback);
        $this->assertSame($resultBalance, $user->balance);
    }

    public function testTransferCashbackToUserInsufficientFunds(): void
    {
        $user = User::first();
        $user->cashback = 0;
        $result = $this->transactionService->transferCashbackToUser($user);
        $this->assertSame(TransactionService::ERROR_CODE_INSUFFICIENT_FUNDS, $result);
    }
}
