<?php

namespace App\Services;

use App\Models\LegacyPhone;
use Illuminate\Support\Facades\Auth;

class PhoneService
{
    public function save(int $personId, int $type, ?string $ddd, ?string $phone, ?int $userId = null): void
    {
        $ddd = onlyDigits($ddd);
        $phone = onlyDigits($phone);
        $userId = $userId ?? Auth::id();

        if (empty($ddd) && empty($phone)) {
            $this->delete($personId, $type);

            return;
        }

        if (empty($ddd) || empty($phone)) {
            return;
        }

        $this->upsert($personId, $type, $ddd, $phone, $userId);
    }

    public function delete(int $personId, int $type): void
    {
        LegacyPhone::query()
            ->where('idpes', $personId)
            ->where('tipo', $type)
            ->delete();
    }

    public function deleteAll(int $personId): void
    {
        LegacyPhone::query()
            ->where('idpes', $personId)
            ->delete();
    }

    private function upsert(int $personId, int $type, string $ddd, string $phone, ?int $userId): void
    {
        $exists = LegacyPhone::query()
            ->where('idpes', $personId)
            ->where('tipo', $type)
            ->exists();

        if ($exists) {
            LegacyPhone::query()
                ->where('idpes', $personId)
                ->where('tipo', $type)
                ->update([
                    'ddd' => $ddd,
                    'fone' => $phone,
                    'idpes_rev' => $userId,
                ]);
        } else {
            LegacyPhone::create([
                'idpes' => $personId,
                'tipo' => $type,
                'ddd' => $ddd,
                'fone' => $phone,
                'idpes_cad' => $userId,
            ]);
        }
    }
}
