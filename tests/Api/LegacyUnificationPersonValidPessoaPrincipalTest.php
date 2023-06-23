<?php

namespace Tests\Api;

use App\Models\LegacyIndividual;
use Database\Factories\LegacyIndividualFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\LoginFirstUser;
use Tests\TestCase;

class LegacyUnificationPersonValidPessoaPrincipalTest extends TestCase
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

    public function testUnificationPersonInvalidPessoaPrincipal(): void
    {
        $request = [
            'tipoacao' => 'Novo',
        ];

        $data = [
            'pessoas' => collect([
                [
                    'idpes' => $this->individualOne->getKey(),
                    'pessoaprincipal' => true,
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

    public function testUnificationPersonNotPresentPessoaPrincipal(): void
    {
        $request = [
            'tipoacao' => 'Novo',
        ];

        $data = [
            'pessoas' => collect([
                [
                    'idpes' => $this->individualOne->getKey(),
                    'pessoa_principal' => false,
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
            ->assertSee('Pessoa principal não informada');
    }

    public function testUnificationPersonDuplicatePessoaPrincipal(): void
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
                    'idpes' => $this->individualTwo->getKey(),
                    'pessoa_principal' => true,
                ],
            ]),
        ];

        $payload = array_merge($request, $data);

        $this->post('/intranet/educar_unifica_pessoa.php', $payload)
            ->assertSuccessful()
            ->assertSee('Não pode haver mais de uma pessoa principal');
    }
}
