<?php

use App\Models\LegacyInstitution;
use App\Models\LegacyTransferRequest;
use App\Models\LegacyTransferType;
use App\Models\LegacyUser;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyTransferType::class, relations: [
    'institution' => LegacyInstitution::class,
    'transferRequests' => LegacyTransferRequest::class,
    'createdByUser' => LegacyUser::class,
    'deletedByUser' => LegacyUser::class,
]));

EloquentModelTestCase::registerModelTests();
