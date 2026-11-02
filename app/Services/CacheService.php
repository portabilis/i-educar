<?php

namespace App\Services;

use App\Models\LegacyUserType;
use Closure;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    private const TTL_SETTINGS = 300;

    private const TTL_GENERAL_CONFIGURATION = 300;

    private const TTL_ANNOUNCEMENT = 604800;

    private const KEY_SETTINGS = 'settings.all';

    private const KEY_GENERAL_CONFIGURATION = 'settings.general_configuration';

    public static function rememberSettings(Closure $callback): array
    {
        return Cache::remember(self::KEY_SETTINGS, self::TTL_SETTINGS, $callback);
    }

    public static function clearSettings(): void
    {
        Cache::forget(self::KEY_SETTINGS);
    }

    public static function rememberGeneralConfiguration(Closure $callback): array
    {
        return Cache::remember(self::KEY_GENERAL_CONFIGURATION, self::TTL_GENERAL_CONFIGURATION, $callback);
    }

    public static function clearGeneralConfiguration(): void
    {
        Cache::forget(self::KEY_GENERAL_CONFIGURATION);
    }

    public static function rememberAnnouncementByUserType(int $userTypeId, Closure $callback): mixed
    {
        return Cache::remember("announcement.user_type.{$userTypeId}", self::TTL_ANNOUNCEMENT, $callback);
    }

    public static function clearAnnouncements(): void
    {
        foreach (LegacyUserType::pluck('cod_tipo_usuario') as $userTypeId) {
            Cache::forget("announcement.user_type.{$userTypeId}");
        }
    }

    public static function clearInstitution(int $institutionId): void
    {
        Cache::forget('select_instituicao');
        Cache::forget('instituicao_' . $institutionId);

        self::clearGeneralConfiguration();
    }
}
