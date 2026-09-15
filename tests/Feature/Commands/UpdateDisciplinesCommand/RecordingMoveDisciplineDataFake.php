<?php

namespace Tests\Feature\Commands\UpdateDisciplinesCommand;

use App\Services\Discipline\MoveDisciplineDataInterface;

final class RecordingMoveDisciplineDataFake implements MoveDisciplineDataInterface
{
    public static array $calls = [];

    public static int $updatedResources = 7;

    public function moveData($disciplineFrom, $disciplineTo, $year, $gradeId)
    {
        self::$calls[] = [
            'disciplineFrom' => $disciplineFrom,
            'disciplineTo' => $disciplineTo,
            'year' => $year,
            'gradeId' => $gradeId,
        ];

        return self::$updatedResources;
    }

    public static function reset(): void
    {
        self::$calls = [];
        self::$updatedResources = 7;
    }
}