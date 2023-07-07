<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyInstitution;
use App\Models\LegacyTransferRequest;
use App\Models\LegacyTransferType;
use App\Models\LegacyUser;
use Tests\EloquentTestCase;

class LegacyTransferTypeTest extends EloquentTestCase
{
    public $relations = [
        'institution' => LegacyInstitution::class,
        'transferRequests' => LegacyTransferRequest::class,
        'createdByUser' => LegacyUser::class,
        'deletedByUser' => LegacyUser::class,
    ];

    protected function getEloquentModelName(): string
    {
        return LegacyTransferType::class;
    }
}
