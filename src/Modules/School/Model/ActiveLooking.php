<?php

namespace iEducar\Modules\School\Model;

use iEducar\Support\DescriptionValue;

class ActiveLooking
{
    use DescriptionValue;

    public const ACTIVE_LOOKING_ABANDONMENT_RESULT = 1;
    public const ACTIVE_LOOKING_IN_PROGRESS_RESULT = 2;
    public const ACTIVE_LOOKING_WITH_ABSENCE_RESULT = 3;
    public const ACTIVE_LOOKING_WITHOUT_ABSENCE_RESULT = 4;

    public static function getDescriptiveValues(): array
    {
        return [
            self::ACTIVE_LOOKING_IN_PROGRESS_RESULT => 'Em andamento',
            self::ACTIVE_LOOKING_ABANDONMENT_RESULT => 'Abandono',
            self::ACTIVE_LOOKING_WITH_ABSENCE_RESULT => 'Retorno com ausência justificada',
            self::ACTIVE_LOOKING_WITHOUT_ABSENCE_RESULT => 'Retorno sem ausência justificada',
        ];
    }
}
