<?php

use App\Support\View\SearchParameters;
use Database\Factories\LegacyEmployeeFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\StudentScenario;

beforeEach(function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());

    // O que a tela que abre a pesquisa manda: os campos a preencher na volta.
    $this->parameters = function (string $type = 'F', bool $submit = false, array $extra = []) {
        $parameters = new SearchParameters;
        $parameters->pessoa = $type;
        $parameters->submit = $submit;
        $key = $type === 'FUNC' ? 'ref_cod_pessoa_fj' : 'idpes';
        $parameters->adicionaCampoTexto('cod_pessoa', $key);
        $parameters->adicionaCampoSelect('nome_pessoa', $key, 'nome');

        foreach ($extra as $key => $value) {
            $parameters->$key = $value;
        }

        return $parameters->serializaCampos();
    };

    $this->search = fn (array $query, string $type = 'F', bool $submit = false, array $extra = []) => $this
        ->get('/intranet/pesquisa_pessoa_lst.php?' . http_build_query($query + ['busca' => 'S']) . '&campos=' . ($this->parameters)($type, $submit, $extra))
        ->assertOk();
});

test('pesquisa pessoas físicas pelo nome e devolve o código e o nome para a tela de origem', function () {
    $ana = StudentScenario::individual('Ana Álves', ['cpf' => '12345678909']);
    $bia = StudentScenario::individual('Bia Borges');

    ($this->search)(['campo_busca' => 'ana alv'])
        ->assertSeeInOrder(['CPF', 'Nome', '123.456.789-09', 'Ana Álves'])
        ->assertSee("set_campo_pesquisa( 'cod_pessoa', '{$ana->getKey()}', 'nome_pessoa', '{$ana->getKey()}', 'Ana Álves' )", false)
        ->assertDontSee('Bia Borges');
});

test('pesquisa pelo código e pelo CPF', function (Closure $query) {
    $ana = StudentScenario::individual('Ana Alves', ['cpf' => '12345678909']);
    $bia = StudentScenario::individual('Bia Borges', ['cpf' => '98765432100']);

    ($this->search)($query->call($this, $ana))
        ->assertSee('Ana Alves')
        ->assertDontSee('Bia Borges');
})->with([
    'código' => [fn ($ana) => ['campo_busca' => $ana->getKey()]],
    'CPF com máscara' => [fn () => ['cpf' => '123.456.789-09']],
    'CPF com o nome' => [fn () => ['campo_busca' => 'Alves', 'cpf' => '12345678909']],
]);

test('ordena pelo nome social, mas mostra o nome de registro', function () {
    StudentScenario::individual('Zélia Alves', ['nome_social' => 'Alba Alves']);
    StudentScenario::individual('Bia Alves');

    ($this->search)(['campo_busca' => 'Alves'])
        ->assertSeeInOrder(['Zélia Alves', 'Bia Alves'])
        ->assertDontSee('Alba Alves');
});

test('com a chave do campo numérica, devolve os campos indexados e com o submit', function () {
    $ana = StudentScenario::individual('Ana Alves');

    ($this->search)(['campo_busca' => 'Ana Alves', 'chave_campo' => '2'], submit: true)
        ->assertSee("set_campo_pesquisa( 'cod_pessoa[2]', '{$ana->getKey()}', 'nome_pessoa[2]', '{$ana->getKey()}', 'Ana Alves', 'submit' )", false);
});

test('com o código do sistema, traz quem tem esse código ou tem CPF', function () {
    $ana = StudentScenario::individual('Ana Alves', ['ref_cod_sistema' => 77]);
    $bia = StudentScenario::individual('Bia Alves', ['cpf' => '12345678909']);
    $caio = StudentScenario::individual('Caio Alves');

    ($this->search)(['campo_busca' => 'Alves'], extra: ['ref_cod_sistema' => 77])
        ->assertSee('Ana Alves')
        ->assertSee('Bia Alves')
        ->assertDontSee('Caio Alves');
});

test('não lista pessoa inativa', function () {
    StudentScenario::individual('Ana Alves', ['ativo' => 0]);

    ($this->search)(['campo_busca' => 'Ana Alves'])->assertSee('Não existe nenhum resultado a ser apresentado.');
});

test('avisa quando o CPF é inválido', function () {
    ($this->search)(['cpf' => '111.111.111-11'])->assertSee('Informado um CPF Inválido');
});

test('pagina de 10 em 10', function () {
    foreach (range(1, 11) as $number) {
        StudentScenario::individual(sprintf('Registro %02d', $number));
    }

    ($this->search)(['campo_busca' => 'Registro'])->assertSee('Registro 10')->assertDontSee('Registro 11');
    ($this->search)(['campo_busca' => 'Registro', 'pagina_form1' => 2])->assertSee('Registro 11')->assertDontSee('Registro 10');
});

test('reaproveita os campos guardados na sessão quando a tela volta sem eles', function () {
    $ana = StudentScenario::individual('Ana Alves');

    ($this->search)(['campo_busca' => 'Ana Alves']);

    $this->get('/intranet/pesquisa_pessoa_lst.php?busca=S&campo_busca=Ana+Alves')
        ->assertOk()
        ->assertSee("set_campo_pesquisa( 'cod_pessoa', '{$ana->getKey()}'", false);
});

test('pesquisa funcionários pelo nome e pela matrícula, ativos ou todos', function () {
    $ana = StudentScenario::individual('Ana Alves');
    $bia = StudentScenario::individual('Bia Alves');
    LegacyEmployeeFactory::new()->create(['ref_cod_pessoa_fj' => $ana->getKey(), 'matricula' => 1001, 'ativo' => 1]);
    LegacyEmployeeFactory::new()->create(['ref_cod_pessoa_fj' => $bia->getKey(), 'matricula' => 2002, 'ativo' => 0]);

    ($this->search)(['campo_busca' => 'Alves'], 'FUNC')
        ->assertSeeInOrder(['Matricula', 'Nome', '1001', 'Ana Alves'])
        ->assertSee("set_campo_pesquisa( 'cod_pessoa', '{$ana->getKey()}', 'nome_pessoa', '{$ana->getKey()}', 'Ana Alves' )", false)
        ->assertDontSee('Bia Alves');

    // A tela lê `show` de $_REQUEST, que o PHP preenche só numa requisição
    // real; aqui ele é preenchido à mão.
    $_REQUEST['show'] = 'todos';

    ($this->search)(['campo_busca' => 'Alves', 'show' => 'todos'], 'FUNC', submit: true)
        ->assertSee('Ana Alves')
        ->assertSee('Bia Alves')
        ->assertSee("'submit' )", false);

    ($this->search)(['matricula' => '2002', 'show' => 'todos'], 'FUNC')
        ->assertSee('Bia Alves')
        ->assertDontSee('Ana Alves');

    $_REQUEST = [];

    ($this->search)(['campo_busca' => 'Ninguém'], 'FUNC')->assertSee('Não existe nenhum resultado a ser apresentado.');

});

test('para funcionários com edição em tela cheia, a linha também devolve os campos', function () {
    $ana = StudentScenario::individual('Ana Alves');
    LegacyEmployeeFactory::new()->create(['ref_cod_pessoa_fj' => $ana->getKey(), 'matricula' => 1001, 'ativo' => 1]);

    ($this->search)(['campo_busca' => 'Alves'], 'FUNC', extra: ['pessoa_editar' => 'S', 'pessoa_tela' => 'cheia'])
        ->assertSee("set_campo_pesquisa( 'cod_pessoa', '{$ana->getKey()}'", false);
});
