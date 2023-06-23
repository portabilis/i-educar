<?php

namespace Tests\Api;

use App\Models\LegacyIndividual;
use Database\Factories\LegacyIndividualFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\LoginFirstUser;
use Tests\TestCase;

class LegacyUnificationPersonValidIdPesTest extends TestCase
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

    public function testUnificationPersonNotPresentIdPes(): void
    {
        $request = [
            'tipoacao' => 'Novo',
        ];

        $data = [
            'pessoas' => collect([
                [
                    'idpe' => $this->individualOne->getKey(),
                    'pessoa_principal' => true,
                ],
                [
                    'idpes' => $this->individualTwo->getKey(),
                    'pessoa_principal' => false,
                ],
            ]),
        ];

        $payload = array_merge($request, $data);

        $this->post('/intranet/educar_unifica_pessoa.php', $payload)
            ->assertSuccessful()
            ->assertSee('Dados enviados inválidos, recarregue a tela e tente novamente!');
    }

    public function testUnificationPersonDuplicateIdPes(): void
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
                [
                    'idpes' => $this->individualOne->getKey(),
                    'pessoa_principal' => false,
                ],
            ]),
        ];

        $payload = array_merge($request, $data);

        $this->post('/intranet/educar_unifica_pessoa.php', $payload)
            ->assertSuccessful()
            ->assertSee('Erro ao tentar unificar Pessoas, foi inserido cadastro duplicados');
    }
}
