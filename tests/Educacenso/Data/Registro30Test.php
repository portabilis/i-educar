<?php

use App\Models\Educacenso\Registro30;
use App\Repositories\EducacensoRepository;
use iEducar\Modules\Educacenso\Data\Registro30 as Registro30Data;
use Tests\TestCase;

uses(TestCase::class);

test('escola sem gestores docentes e alunos nao tem pessoas no registro 30', function () {
    $data = new Registro30Data(new EducacensoRepository, new Registro30);

    // Itens que não são pessoas do registro 30 são ignorados.
    $data->setArrayDataByType([new stdClass], Registro30::TIPO_MANAGER);

    // A exportação roda na tela legada, sem avisos do PHP: sem pessoas, a lista
    // interna nunca é criada e o laço final percorre um valor nulo.
    $level = error_reporting(0);

    try {
        $pessoas = $data->getData(1);
    } finally {
        error_reporting($level);
    }

    $this->assertEmpty($pessoas);
});
