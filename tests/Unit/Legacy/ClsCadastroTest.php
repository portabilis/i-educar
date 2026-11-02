<?php

use Database\Factories\LegacyUserFactory;
use iEducar\Support\Navigation\Breadcrumb;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

// Cadastro mínimo: só o que cada teste precisa sobrescrever.
class CadastroDeTeste extends clsCadastro
{
    public $enturmar = false;

    public $erros_do_controller = [];

    public function Enturmar()
    {
        return $this->enturmar;
    }

    public function hasErrors()
    {
        return !empty($this->erros_do_controller);
    }

    public function getErrors()
    {
        return $this->erros_do_controller;
    }

    protected function _getEntityLabel($key)
    {
        return ucfirst($key);
    }

    public function nivelDeAcesso()
    {
        return $this->nivelAcessoPessoaLogada();
    }
}

function renderizaCadastro(clsCadastro $cadastro): string
{
    // As telas legadas rodam sem avisos do PHP: o LegacyController zera o error_reporting.
    $level = error_reporting(0);

    try {
        return $cadastro->RenderHTML();
    } finally {
        error_reporting($level);
    }
}

beforeEach(function () {
    $_GET = [];
    $_POST = [];
    $_FILES = [];
});

afterEach(function () {
    $_GET = [];
    $_POST = [];
    $_FILES = [];
});

test('o link de excluir vira a ação Excluir e o POST vira propriedades do cadastro', function () {
    $_GET['excluir'] = 1;
    $_POST = ['nome' => 'Ana'];
    $_FILES = ['foto' => ['name' => 'foto.jpg']];

    $cadastro = new CadastroDeTeste;
    $cadastro->Processar();

    expect($cadastro->tipoacao)->toBe('Excluir')
        ->and($cadastro->nome)->toBe('Ana')
        ->and($cadastro->foto)->toBe(['name' => 'foto.jpg'])
        ->and($cadastro->sucesso)->toBeFalse();
});

test('as ações sem implementação falham com a mensagem padrão', function (string $acao, string $mensagem) {
    $_POST = ['tipoacao' => $acao];

    $cadastro = new CadastroDeTeste;
    $cadastro->Processar();

    $property = new ReflectionProperty(clsCadastro::class, '_mensagem');

    expect($cadastro->sucesso)->toBeFalse()
        ->and($property->getValue($cadastro))->toBe($mensagem);
})->with([
    'novo' => ['Novo', 'Não foi possível inserir a informação. [CAD01]'],
    'exclusão' => ['Excluir', 'Não foi possível excluir a informação. [CAD03]'],
    'exclusão da imagem' => ['ExcluirImg', 'Não foi possível excluir a informação. [CAD04]'],
    'enturmação' => ['Enturmar', 'Não foi possível copiar as entrumações. [CAD05]'],
]);

test('avisa o sucesso pedido pela URL quando a tela não deixou mensagem', function () {
    $_GET['mensagem'] = 'sucesso';
    $_POST = ['tipoacao' => 'Enturmar'];

    $cadastro = new CadastroDeTeste;
    $cadastro->enturmar = true;
    $cadastro->Processar();

    expect(Session::get('success'))->toBe('Registro incluido com sucesso!');
});

test('valida no navegador os campos de tabela e os campos com regras próprias', function () {
    $cadastro = new CadastroDeTeste;
    $cadastro->campoTabelaInicio('documentos', 'Documentos', ['Documento', 'CPF', 'CNPJ', 'CPF opcional']);
    $cadastro->campoIdFederal('documento', 'Documento', '', true);
    $cadastro->campoCpf('cpf', 'CPF', '', true);
    $cadastro->campoCnpj('cnpj', 'CNPJ', '', true);
    $cadastro->campoTabelaFim();
    $cadastro->campoTabelaInicio('opcionais', 'Opcionais', ['CPF']);
    $cadastro->campoCpf('cpf', 'CPF', '');
    $cadastro->campoTabelaFim();
    $cadastro->campoTexto('latitude', 'Latitude', '', 10, 10, false, 'lat');
    $cadastro->campoTexto('longitude', 'Longitude', '', 10, 10, false, 'lon');
    $cadastro->campoIdFederal('id_federal', 'CPF ou CNPJ', '', true);
    $cadastro->campoCpf('cpf_responsavel', 'CPF do responsável', '', true);
    $cadastro->campoCnpj('cnpj_empresa', 'CNPJ da empresa', '', true);

    $html = renderizaCadastro($cadastro);

    expect($html)
        ->toContain("alert( 'Preencha o campo \\'Documento\\' corretamente!' )")
        ->toContain("else { if(! DvCpfOk( document.getElementById('cpf['+id_campo+']')) ) return false; }")
        ->toContain("if(! DvCnpjOk( document.getElementById('cnpj['+id_campo+']')) ) return false;")
        ->toContain("alert('Preencha o campo \\'CPF\\' Corretamente');")
        ->toContain('_graus.value')
        ->toContain('/^(-4[7-9])|(-5[0-4])/')
        ->toContain("else{ if(! DvCpfOk( document.getElementById('id_federal')) ) return false; }")
        ->toContain("else { if(! DvCpfOk( document.getElementById('cpf_responsavel')) ) return false; }")
        ->toContain('if(! DvCnpjOk( document.formcadastro.cnpj_empresa) ) return false;');
});

test('mostra os botões extras, o script final, o breadcrumb e os erros do controller', function () {
    $cadastro = new CadastroDeTeste;
    $cadastro->locale = 'Alunos';
    $cadastro->_title = 'Cadastro de alunos';
    $cadastro->bot_alt = true;
    $cadastro->url_alt = 'educar_aluno_lst.php';
    $cadastro->nome_url_alt = 'Alternativo';
    $cadastro->excluir_Img = true;
    $cadastro->acao = 'fazer();';
    $cadastro->nome_acao = 'Fazer';
    $cadastro->url_copiar_enturmacoes = 'copiar.php';
    $cadastro->nome_url_copiar_enturmacoes = 'Copiar enturmações';
    $cadastro->array_botao = ['Imprimir'];
    $cadastro->array_botao_url = ['imprimir.php'];
    $cadastro->array_botao_id = ['btn_imprimir'];
    $cadastro->executa_script = 'iniciar();';
    $cadastro->erros_do_controller = ['nome' => 'Obrigatório', 'idade' => null];
    $cadastro->campoTexto('nome', 'Nome', '');

    $html = renderizaCadastro($cadastro);

    expect(app(Breadcrumb::class)->getLegacy())->toBe('Alunos')
        ->and(view()->shared('title'))->toBe('Cadastro de alunos')
        ->and($html)
        ->toContain('go( "educar_aluno_lst.php" );\' value=\' Alternativo \'')
        ->toContain("onclick='javascript:ExcluirImg();' value=' Excluir Imagem '")
        ->toContain("onclick='javascript: fazer();' value=' Fazer '")
        ->toContain('go( "copiar.php" );\' value=\' Copiar enturmações \'')
        ->toContain('value=\'Imprimir\' id="btn_imprimir"')
        ->toContain('<script>iniciar();</script>')
        ->toContain('<li>Nome: Obrigatório</li>')
        ->not->toContain('Idade');
});

test('botões por script usam o id informado ou o nome do botão', function () {
    $cadastro = new CadastroDeTeste;
    $cadastro->array_botao = ['Voltar', 'Avançar'];
    $cadastro->array_botao_url_script = ['history.back()', 'history.forward()'];
    $cadastro->array_botao_id = [null, 'btn_avancar'];
    $cadastro->campoTexto('nome', 'Nome', '');

    expect(renderizaCadastro($cadastro))
        ->toContain('onclick="history.back()" value="Voltar" id="arr_bot_Voltar"')
        ->toContain('onclick="history.forward()" value="Avançar" id="btn_avancar"');
});

test('os padrões do cadastro não fazem nada', function () {
    $cadastro = new class extends clsCadastro {};

    expect($cadastro->Novo())->toBeFalse()
        ->and($cadastro->Excluir())->toBeFalse()
        ->and($cadastro->ExcluirImg())->toBeFalse();
});

test('converte valores vazios em NULL e checkboxes em 0 e 1', function () {
    $cadastro = new CadastroDeTeste;

    expect($cadastro->isNullNow('Ana', '', 0))->toBe(['Ana', 'NULL', 'NULL'])
        ->and($cadastro->isOnNow('on', '', 'off'))->toBe([1, 0, 0]);
});

test('o nível de acesso é o do usuário logado', function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());

    expect((new CadastroDeTeste)->nivelDeAcesso())->toBe(App_Model_NivelTipoUsuario::POLI_INSTITUCIONAL);
});
