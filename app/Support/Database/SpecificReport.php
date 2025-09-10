<?php

namespace App\Support\Database;

use App\Setting;

trait SpecificReport
{
    public function isReportFor(string $client): bool
    {
        return Setting::query()->where([
            'key' => 'legacy.report.mostrar_relatorios',
            'value' => $client,
        ])->exists();
    }

    public function isNotReportFor(string $client): bool
    {
        return !$this->isReportFor($client);
    }
}
