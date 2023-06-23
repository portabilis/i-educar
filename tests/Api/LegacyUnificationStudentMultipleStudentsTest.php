<?php

namespace Tests\Api;

use App\Models\LegacyStudent;
use App\Models\LogUnification;
use App\Models\LogUnificationOldData;
use Database\Factories\LegacyStudentFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\LoginFirstUser;
use Tests\TestCase;

class LegacyUnificationStudentMultipleStudentsTest extends TestCase
{
    use DatabaseTransactions;
    use LoginFirstUser;

    private LegacyStudent $studentOne;

    private LegacyStudent $studentTwo;

    private LegacyStudent $studentThree;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginWithFirstUser();

        $this->studentOne = LegacyStudentFactory::new()->create();
        $this->studentTwo = LegacyStudentFactory::new()->create();
        $this->studentThree = LegacyStudentFactory::new()->create();
    }

    public function testUnificationMultipleStudents(): void
    {
        $request = [
            'tipoacao' => 'Novo',
        ];

        $data = [
            'alunos' => collect([
                [
                    'codAluno' => $this->studentOne->getKey(),
                    'aluno_principal' => true,
                ],
                [
                    'codAluno' => $this->studentTwo->getKey(),
                    'aluno_principal' => false,
                ],
                [
                    'codAluno' => $this->studentThree->getKey(),
                    'aluno_principal' => false,
                ],
            ]),
        ];

        $payload = array_merge($request, $data);

        $this->post('/intranet/educar_unifica_aluno.php', $payload)
            ->assertRedirectContains(route('student-log-unification.index'));

        $log = LogUnification::query()
            ->where('main_id', $this->studentOne->getKey())
            ->where('type', 'App\Models\Student')
            ->where('active', true)
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals($log->duplicates_id, [
            $this->studentTwo->getKey(),
            $this->studentThree->getKey(),
        ]);
        $this->assertTrue($log->created_at->isToday());
        $this->assertTrue($log->updated_at->isToday());

        $logOldDataStudent = LogUnificationOldData::query()
            ->where('unification_id', $log->getKey())
            ->where('table', $this->studentTwo->getTable())
            ->get();

        $this->assertNotNull($logOldDataStudent);
        $this->assertCount(2, $logOldDataStudent);

        $this->assertEquals($logOldDataStudent[0]->keys[0], [
            'cod_aluno' => $this->studentTwo->getKey(),
        ]);
        $this->assertEquals($logOldDataStudent[1]->keys[0], [
            'cod_aluno' => $this->studentThree->getKey(),
        ]);

        $this->assertDatabaseHas($log, [
            'id' => $log->getKey(),
            'type' => 'App\Models\Student',
            'main_id' => $this->studentOne->getKey(),
            'active' => true,
        ])->assertDatabaseHas($this->studentOne, [
            'cod_aluno' => $this->studentOne->getKey(),
            'ativo' => 1,
        ])->assertDatabaseHas($this->studentTwo, [
            'cod_aluno' => $this->studentTwo->getKey(),
            'ativo' => 0,
        ])->assertDatabaseHas($this->studentThree, [
            'cod_aluno' => $this->studentThree->getKey(),
            'ativo' => 0,
        ]);
    }
}
