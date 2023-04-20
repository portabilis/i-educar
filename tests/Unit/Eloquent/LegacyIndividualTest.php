<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDeficiency;
use App\Models\LegacyDocument;
use App\Models\LegacyIndividual;
use App\Models\LegacyIndividualPicture;
use App\Models\LegacyPerson;
use App\Models\LegacyRace;
use App\Models\LegacyStudent;
use Tests\EloquentTestCase;

class LegacyIndividualTest extends EloquentTestCase
{
    public $relations = [
        'race' => LegacyRace::class,
        'deficiency' => LegacyDeficiency::class,
        'person' => LegacyPerson::class,
        'student' => LegacyStudent::class,
        'document' => LegacyDocument::class,
        'picture' => LegacyIndividualPicture::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyIndividual::class;
    }
}
