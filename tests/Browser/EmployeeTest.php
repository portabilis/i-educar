<?php

use App\Models\Employee;
use App\Models\LegacySchoolingDegree;
use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyEmployeeRoleFactory;
use Database\Factories\LegacyRoleFactory;
use Tests\Browser\Support\Form;
use Tests\Browser\Support\Scenario;

/**
 * Servidores > Cadastros > Servidores.
 *
 * O servidor é o primeiro cadastro que junta o que as telas de tipos
 * entregaram: a função, a escolaridade e a carga horária. A pessoa não é criada
 * aqui — ela já existe em cadastro.fisica, e a tela só a vincula.
 *
 * **O cadastro não entra por enquanto, e o motivo é da tela.** Para o `<select>`
 * de função vir preenchido, o Gerar() exige `ref_cod_instituicao` na URL; e para
 * a pessoa vir escolhida, sem passar pelo popup de busca, exige `cod_servidor`.
 * Só que o Inicializar() usa exatamente esse par para procurar o servidor e,
 * quando não acha, redireciona para a listagem — que é o caso de todo cadastro
 * novo. Os dois caminhos automatizáveis se excluem, então aqui ficam a listagem,
 * a edição e a recusa; o cadastro entra quando a tela oferecer um caminho ou
 * quando o popup de busca for automatizável.
 */
beforeEach(function () {
    $this->scenario = Scenario::install(adminLogin: 'adm-servidor');
});

/**
 * Cria o servidor por baixo, com a função e a escolaridade que o cenário já tem.
 */
function employee(string $name = 'Carlos Professor'): Employee
{
    $person = Scenario::individual($name);

    $employee = EmployeeFactory::new()->create([
        'id' => $person->idpes,
        'workload' => 20,
        'ref_idesco' => LegacySchoolingDegree::query()->orderBy('idesco')->value('idesco'),
    ]);

    LegacyEmployeeRoleFactory::new()->create([
        'ref_cod_servidor' => $employee->getKey(),
        'ref_cod_funcao' => LegacyRoleFactory::new()->current(),
        'matricula' => 'M-100',
    ]);

    return $employee;
}

it('lista os servidores', function () {
    $employee = employee();

    // A listagem só traz quem tem alocação; o filtro "Incluir servidores sem
    // alocação" é o que mostra o servidor recém-cadastrado.
    $this->scenario->loginAsAdmin()
        ->navigate('/intranet/educar_servidor_lst.php?servidor_sem_alocacao=1')
        ->assertSee('Servidor - Listagem')
        ->assertSee('Carlos Professor');

    expect($employee->getKey())->toBeInt();
});

it('edita a carga horária do servidor', function () {
    $employee = employee();

    $page = $this->scenario->loginAsAdmin()
        ->navigate(sprintf(
            '/intranet/educar_servidor_cad.php?cod_servidor=%d&ref_cod_instituicao=%d',
            $employee->getKey(),
            $this->scenario->institution->getKey(),
        ))
        ->assertSee('Carlos Professor')
        ->fill('carga_horaria', '30:00');

    // Gravar leva para o detalhe do servidor, não para a listagem.
    Form::save($page)->assertPathIs('/intranet/educar_servidor_det.php');

    expect((int) $employee->fresh()->carga_horaria)->toBe(30);
});

it('recusa gravar o servidor sem a carga horária', function () {
    $employee = employee();

    $page = $this->scenario->loginAsAdmin()
        ->navigate(sprintf(
            '/intranet/educar_servidor_cad.php?cod_servidor=%d&ref_cod_instituicao=%d',
            $employee->getKey(),
            $this->scenario->institution->getKey(),
        ))
        ->fill('carga_horaria', '');

    Form::reject($page)->assertPathIs('/intranet/educar_servidor_cad.php');

    expect((int) $employee->fresh()->carga_horaria)->toBe(20);
});
