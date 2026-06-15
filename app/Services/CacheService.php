<?php

namespace App\Services;

use App\Models\LegacyUserType;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    public static function clearAnnouncements(): void
    {
        foreach (LegacyUserType::pluck('cod_tipo_usuario') as $userTypeId) {
            Cache::forget("announcement.user_type.{$userTypeId}");
        }
    }
}
