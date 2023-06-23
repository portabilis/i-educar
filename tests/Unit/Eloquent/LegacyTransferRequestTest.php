<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyRegistration;
use App\Models\LegacySchool;
use App\Models\LegacyTransferRequest;
use App\Models\LegacyTransferType;
use App\Models\LegacyUser;
use Database\Factories\LegacyTransferRequestFactory;
use Tests\EloquentTestCase;

class LegacyTransferRequestTest extends EloquentTestCase
{
    public $relations = [
        'oldRegistration' => LegacyRegistration::class,
        'newRegistration' => LegacyRegistration::class,
        'transferType' => LegacyTransferType::class,
        'destinationSchool' => LegacySchool::class,
        'createdByUser' => LegacyUser::class,
        'deletedByUser' => LegacyUser::class,
    ];

    protected function getEloquentModelName(): string
    {
        return LegacyTransferRequest::class;
    }

    public function testScopeActive(): void
    {
        LegacyTransferRequestFactory::new()->create(['ativo' => 0]);
        $found = $this->instanceNewEloquentModel()->active()->get();
        $this->assertCount(1, $found);
    }

    public function testScopeUnattended(): void
    {
        LegacyTransferRequestFactory::new()->create(['ref_cod_matricula_entrada' => null]);
        $found = $this->instanceNewEloquentModel()->unattended()->get();
        $this->assertCount(1, $found);
    }
}
