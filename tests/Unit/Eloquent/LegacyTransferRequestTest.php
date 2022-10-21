<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyRegistration;
use App\Models\LegacyTransferRequest;
use Tests\EloquentTestCase;

class LegacyTransferRequestTest extends EloquentTestCase
{
    public $relations = [
        'oldRegistration' => LegacyRegistration::class,
        'newRegistration' => LegacyRegistration::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyTransferRequest::class;
    }
}
