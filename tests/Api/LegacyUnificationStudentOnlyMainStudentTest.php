<?php

namespace Tests\Api;

use App\Models\LegacyStudent;
use App\Models\LogUnification;
use Database\Factories\LegacyStudentFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\LoginFirstUser;
use Tests\TestCase;

class LegacyUnificationStudentOnlyMainStudentTest extends TestCase
{
    use DatabaseTransactions;
    use LoginFirstUser;

    private LegacyStudent $student;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginWithFirstUser();

        $this->student = LegacyStudentFactory::new()->create();
    }

    public function test_only_main_student(): void
    {
        $payload = [
            'tipoacao' => 'Novo',
            'alunos' => json_encode([
                [
                    'codAluno' => $this->student->getKey(),
                    'aluno_principal' => true,
                ],
            ]),
        ];

        $this->post('/intranet/educar_unifica_aluno.php', $payload)
            ->assertSuccessful()
            ->assertSee('Informe pelo menos dois alunos para unificar.');

        $this->assertDatabaseMissing(LogUnification::class, [
            'main_id' => $this->student->getKey(),
        ]);
    }
}
