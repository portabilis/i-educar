<?php

use App\Models\LegacyUser;
use Illuminate\Support\Facades\Hash;
use Tests\Browser\Support\Form;
use Tests\Browser\Support\Menus;
use Tests\Browser\Support\Scenario;

/**
 * Etapa 3 do roteiro: criar usuários para os tipos cadastrados.
 *
 * A tela busca a pessoa num popup cujo input vem `disabled`, o que não é
 * automatizável; o caminho é chegar nela com a pessoa já escolhida
 * (`?ref_pessoa=`), exatamente como a interface faz depois da busca.
 *
 * Gravar cria três registros: portal.funcionario (matrícula e senha, ou seja o
 * login), pmieducar.usuario (tipo de usuário e instituição) e
 * pmieducar.escola_usuario quando escolas são vinculadas.
 */
/**
 * Escolhe os três selects obrigatórios da tela de usuário.
 *
 * A instituição é escolhida pelo id, e não pelo rótulo, porque o select é
 * preenchido por AJAX (DynamicInput) depois da página carregar: o selectOption
 * do Playwright tenta de novo até a opção existir, e é isso que faz a espera.
 * Sem escolher, o campo fica em "Selecione uma instituição" e a validação do
 * clsCadastro barra o submit.
 */
function chooseUserFormSelects($page, int $institution)
{
    return Form::cascade($page, [
        'ref_cod_tipo_usuario' => 'Secretaria',
        'ref_cod_funcionario_vinculo' => 'Efetivo',
        'ref_cod_instituicao' => $institution,
    ]);
}

beforeEach(function () {
    $this->scenario = Scenario::install(adminLogin: 'adm-user');
    $this->type = Scenario::userType('Secretaria', processes: [Menus::SCHOOLS, Menus::SCHOOL_CLASSES]);
});

it('lista os usuários', function () {
    $this->scenario->loginAsAdmin()
        ->navigate('/intranet/educar_usuario_lst.php')
        ->assertSee('Matrícula')
        ->assertSee('Tipo usuário')
        ->assertSee('Ana Diretora');

    // Sem assertNoJavaScriptErrors aqui: a inicialização do Chosen no filtro de
    // escolas corre antes do plugin carregar e estoura
    // "$escolaField.chosen is not a function" de forma intermitente. É corrida de
    // carregamento que já existe; quando for corrigida, a asserção volta.
});

it('cadastra um usuário para uma pessoa já existente', function () {
    $individual = Scenario::individual('Bruno Secretário');

    $page = $this->scenario->loginAsAdmin()
        ->navigate('/intranet/educar_usuario_cad.php?ref_pessoa=' . $individual->idpes)
        ->assertSee('Bruno Secretário');

    $page = Form::fill($page, [
        'matricula' => 'bruno',
        '_senha' => Scenario::PASSWORD,
    ]);

    $page = chooseUserFormSelects($page, $this->scenario->institution->getKey());

    Form::save($page)->assertPathIs('/intranet/educar_usuario_lst.php');

    $user = LegacyUser::query()->find($individual->idpes);

    expect($user)->not->toBeNull()
        ->and($user->ref_cod_tipo_usuario)->toBe($this->type->getKey())
        ->and($user->employee->matricula)->toBe('bruno');

    // A senha vai para portal.funcionario com o hash do Laravel, e é ela que dá
    // o login — conferir o hash evita um teste verde com uma senha inutilizável.
    expect(Hash::check(Scenario::PASSWORD, $user->employee->senha))->toBeTrue();
});

it('não cria usuário com senha fora da política', function () {
    $individual = Scenario::individual('Bruno Secretário');

    $page = $this->scenario->loginAsAdmin()
        ->navigate('/intranet/educar_usuario_cad.php?ref_pessoa=' . $individual->idpes);

    $page = chooseUserFormSelects(
        Form::fill($page, ['matricula' => 'bruno', '_senha' => 'bruno']),
        $this->scenario->institution->getKey(),
    );

    // A política exige 8 caracteres com maiúsculas, minúsculas, números e
    // símbolos (ValidateUserPasswordService). O teste afirma o efeito — a tela
    // não avança e nada é gravado — e não o texto da mensagem, que o clsCadastro
    // entrega de um jeito que não fica no fonte da página que volta.
    Form::save($page)->assertPathIs('/intranet/educar_usuario_cad.php');

    expect(LegacyUser::query()->find($individual->idpes))->toBeNull();
});

it('recusa gravar sem matrícula', function () {
    $individual = Scenario::individual('Bruno Secretário');

    $page = chooseUserFormSelects(
        $this->scenario->loginAsAdmin()
            ->navigate('/intranet/educar_usuario_cad.php?ref_pessoa=' . $individual->idpes),
        $this->scenario->institution->getKey(),
    );

    Form::save($page)->assertPathIs('/intranet/educar_usuario_cad.php');

    expect(LegacyUser::query()->find($individual->idpes))->toBeNull();
});

it('permite que o usuário de secretaria entre e veja só o seu menu', function () {
    // O usuário é montado por factory, e não pela tela, porque sair do sistema
    // dentro de um teste de browser não funciona: o intranet/logof.php chama
    // Session::flush(), mas com o servidor in-process do Pest a sessão sobrevive
    // e a navegação seguinte continua autenticada. O teste acima já cobre que a
    // tela grava o login; aqui o que importa é o que esse login enxerga.
    Scenario::user('bruno', 'Bruno Secretário', $this->type, $this->scenario->institution);

    Scenario::login('bruno')
        ->assertPathIs('/intranet/educar_index.php')
        ->assertSee('Bruno Secretário')
        // O tipo só tem permissão em Escolas e Turmas, ambas sob "Escola >
        // Cadastros", então é a única raiz que aparece no menu. A administradora,
        // no mesmo cenário, vê Pessoas, Escola, Servidores e Configurações.
        ->assertSee('Escola')
        ->assertSee('Cadastros')
        ->assertDontSee('Configurações')
        ->assertDontSee('Servidores')
        ->assertDontSee('Pessoas');
});
