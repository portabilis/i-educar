<?php

namespace Tests\Api;

use Database\Factories\LegacyStudentFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DiarioRecuperaNomeECodigoAluno extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequest;

    protected function setUp(): void
    {
        dump(1);
        parent::setUp();
    }

    public function testGetNomeCodigoAluno()
    {
        $student = LegacyStudentFactory::new()->create()->name;

       $response =  $this->get('/module/Api/Aluno?oper=get&', 'resource=aluno-search&query=' . $student);

       assert(true);
    }
}
