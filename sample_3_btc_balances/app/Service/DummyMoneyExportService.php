<?php

declare(strict_types=1);

namespace App\Service;

class DummyMoneyExportService
{
    public function exportToUser(int $amount): bool
    {
        // some call to external API
        return true;
    }

    public function exportToPartner(int $amount): bool
    {
        // some call to external API
        return true;
    }
}
