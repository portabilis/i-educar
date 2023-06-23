<?php

namespace Tests\Api;

use App\Models\LegacyStudent;
use Database\Factories\LegacyStudentFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\LoginFirstUser;
use Tests\TestCase;

class LegacyUnificationStudentNotPresentAlunoPrincipalTest extends TestCase
{
    use DatabaseTransactions;
    use LoginFirstUser;

    private LegacyStudent $studentOne;

    private LegacyStudent $studentTwo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginWithFirstUser();

        $this->studentOne = LegacyStudentFactory::new()->create();
        $this->studentTwo = LegacyStudentFactory::new()->create();
    }

    public function testNotPresentAlunoPrincipal(): void
    {
        $request = [
            'tipoacao' => 'Novo',
        ];

        $data = [
            'alunos' => collect([
                [
                    'codAluno' => $this->studentOne->getKey(),
                ],
                [
                    'codAluno' => $this->studentTwo->getKey(),
                    'aluno_principal' => false,
                ],
            ]),
        ];

        $payload = array_merge($request, $data);

        $this->post('/intranet/educar_unifica_aluno.php', $payload)
            ->assertSuccessful()
            ->assertSee('Dados enviados inválidos, recarregue a tela e tente novamente!');
    }
}
