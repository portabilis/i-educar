<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\LegacyRegistration;
use App\Models\LegacyLevel;
use App\Models\LegacyTransferRequest;
use App\Services\TransferRegistrationDataService;

class TransferRegistrationDataServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function testGetTransfers()
    {
        $registration = factory(LegacyRegistration::class)->create([
            'ref_ref_cod_serie' => factory(LegacyLevel::class)->create(),
        ]);
        factory(LegacyTransferRequest::class)->create([
            'ref_cod_matricula_saida' => $registration->cod_matricula
        ]);
        $service = new TransferRegistrationDataService($registration);

        $this->assertCount(1, $service->getTransfers());
    }
}
