<?php

namespace Tests\Api;

use App\Models\LegacyIndividual;
use App\Models\LegacyStudent;
use App\Models\LogUnification;
use Database\Factories\LegacyDocumentFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyPersonFactory;
use Database\Factories\LegacyPhoneFactory;
use Database\Factories\LegacyRaceFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\LoginFirstUser;
use Tests\TestCase;

class LegacyUnificationPersonSuccessfulTest extends TestCase
{
    use DatabaseTransactions;
    use LoginFirstUser;

    private LegacyIndividual $individual;
    private LegacyStudent $student;
    private LegacyStudent $studentTwo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginWithFirstUser();
    }

    public function testUnificationPersonStudent(): void
    {
        $individual = LegacyIndividualFactory::new()->create();
        $individualSecondary = LegacyIndividualFactory::new()->create();

        $person = LegacyIndividualFactory::new()->create(
            [
                'idpes' => LegacyPersonFactory::new()->create(
                    [
                        'idpes_cad' => $individualSecondary->getKey(),
                        'idpes_rev' => $individualSecondary->getKey(),
                    ]
                ),
                'idpes_mae' => $individualSecondary->getKey(),
                'idpes_pai' => $individualSecondary->getKey(),
                'idpes_responsavel' => $individualSecondary->getKey(),
                'idpes_con' => $individualSecondary->getKey(),
                'idpes_rev' => $individualSecondary->getKey(),
                'idpes_cad' => $individualSecondary->getKey(),
            ]
        );
        $document = LegacyDocumentFactory::new()->create([
            'idpes' => $individualSecondary->getKey(),
            'idpes_rev' => $individualSecondary->getKey(),
            'idpes_cad' => $individualSecondary->getKey(),
        ]);

        $race = LegacyRaceFactory::new()->create([
            'idpes_cad' => $individualSecondary->getKey(),
            'idpes_exc' => $individualSecondary->getKey(),
        ]);

        $fone = LegacyPhoneFactory::new()->create([
            'idpes_cad' => $individualSecondary->getKey(),
            'idpes_rev' => $individualSecondary->getKey(),
        ]);

        $request = [
            'tipoacao' => 'Novo',
        ];

        $data = [
            'pessoas' => collect([
                [
                    'idpes' => $individual->getKey(),
                    'pessoa_principal' => true,
                ],
                [
                    'idpes' => $individualSecondary->getKey(),
                    'pessoa_principal' => false,
                ]
            ]),
        ];

        $payload = array_merge($request, $data);

        $this->post('/intranet/educar_unifica_pessoa.php', $payload)
            ->assertSuccessful()
            ->assertSee('Pessoas unificadas com sucesso.');

        $log = LogUnification::query()
            ->where('main_id', $individual->getKey())
            ->where('type', 'App\Models\Individual')
            ->where('active', true)
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals($log->duplicates_id, [$individualSecondary->getKey()]);
        $this->assertTrue($log->created_at->isToday());
        $this->assertTrue($log->updated_at->isToday());

        $this->assertDatabaseHas($fone, [
            'idpes_rev' => $individual->getKey(),
            'idpes_cad' => $individual->getKey(),
        ])->assertDatabaseHas($race, [
            'idpes_cad' => $individual->getKey(),
            'idpes_exc' => $individual->getKey(),
        ])->assertDatabaseHas($document, [
            'idpes' => $individual->getKey(),
            'idpes_rev' => $individual->getKey(),
            'idpes_cad' => $individual->getKey(),
        ])->assertDatabaseHas($person, [
            'idpes_cad' => $individual->getKey(),
            'idpes_rev' => $individual->getKey(),
            'idpes_mae' => $individual->getKey(),
            'idpes_pai' => $individual->getKey(),
            'idpes_responsavel' => $individual->getKey(),
            'idpes_con' => $individual->getKey(),
        ])->assertDatabaseHas($person->person, [
            'idpes_cad' => $individual->getKey(),
            'idpes_rev' => $individual->getKey(),
        ]);
    }
}
