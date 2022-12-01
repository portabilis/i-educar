<?php

namespace Tests\Feature\Api\Modules;

use Database\Factories\LegacyStudentFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\LoginFirstUser;
use Tests\TestCase;

class AlunoControllerTest extends TestCase
{
    use LoginFirstUser;
    use WithoutMiddleware;
    use DatabaseTransactions;

    public const ROUTE = '/module/Api/Aluno?oper=get&resource=aluno';

    public function testGetResourceAluno()
    {
        $student = LegacyStudentFactory::new()->create();

        $this->get(self::ROUTE . '&id=' . $student->cod_aluno)
            ->assertSuccessful()
            ->assertJsonFragment(['nome' => $student->person->name]);
    }
}
