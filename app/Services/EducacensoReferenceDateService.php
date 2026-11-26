<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonInterface;

class EducacensoReferenceDateService
{
    public function forYear(int $year): Carbon
    {
        return Carbon::create($year, 5, 1)->lastOfMonth(CarbonInterface::WEDNESDAY);
    }

    public function fill(?string $date, int $year): string
    {
        if (!empty($date) && $this->yearOf($date) >= $year) {
            return $date;
        }

        return $this->forYear($year)->format('d/m/Y');
    }

    private function yearOf(string $date): int
    {
        if (str_contains($date, '-')) {
            return (int) substr($date, 0, 4);
        }

        return (int) substr($date, -4);
    }
}
