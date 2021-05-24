<?php

namespace iEducar\Modules\School\Model;

use iEducar\Support\DescriptionValue;

class ActiveSearch
{
    use DescriptionValue;

    public const ACTIVE_SEARCH_ABANDONMENT_RESULT = 1;
    public const ACTIVE_SEARCH_IN_PROGRESS_RESULT = 2;
    public const ACTIVE_SEARCH_WITH_ABSENCE_RESULT = 3;
    public const ACTIVE_SEARCH_WITHOUT_ABSENCE_RESULT = 4;

    public static function getDescriptiveValues(): array
    {
        return [
            self::ACTIVE_SEARCH_IN_PROGRESS_RESULT => 'Em andamento',
            self::ACTIVE_SEARCH_ABANDONMENT_RESULT => 'Abandono',
            self::ACTIVE_SEARCH_WITH_ABSENCE_RESULT => 'Retorno com ausência justificada',
            self::ACTIVE_SEARCH_WITHOUT_ABSENCE_RESULT => 'Retorno sem ausência justificada',
        ];
    }
}
