<?php

namespace Tests\Api;

use App\Models\LegacyIndividual;
use Database\Factories\LegacyIndividualFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\LoginFirstUser;
use Tests\TestCase;

class LegacyUnificationPersonJsonInvalidTest extends TestCase
{
    use DatabaseTransactions;
    use LoginFirstUser;

    private LegacyIndividual $individualOne;

    private LegacyIndividual $individualTwo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginWithFirstUser();

        $this->individualOne = LegacyIndividualFactory::new()->create();
        $this->individualTwo = LegacyIndividualFactory::new()->create();
    }

    public function testUnificationPersonJsonInvalid(): void
    {
        $request = [
            'tipoacao' => 'Novo',
        ];

        $data = [
            'pessoas' => [
                [
                    'idpes' => $this->individualOne->getKey(),
                    'pessoa_principal' => true,
                ],
                [
                    'idpes' => $this->individualTwo->getKey(),
                    'pessoa_principal' => false,
                ],
            ],
        ];

        $payload = array_merge($request, $data);

        $this->post('/intranet/educar_unifica_pessoa.php', $payload)
            ->assertSuccessful()
            ->assertSee('Informações inválidas para unificação');
    }

    public function testUnificationPersonCount(): void
    {
        $request = [
            'tipoacao' => 'Novo',
        ];

        $data = [
            'pessoas' => collect([
                [
                    'idpes' => $this->individualOne->getKey(),
                    'pessoa_principal' => true,
                ],
            ]),
        ];

        $payload = array_merge($request, $data);

        $this->post('/intranet/educar_unifica_pessoa.php', $payload)
            ->assertSuccessful()
            ->assertSee('Informe no mínimo duas pessoas para unificação.');
    }
}
