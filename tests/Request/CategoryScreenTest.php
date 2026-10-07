<?php

use App\Models\City;
use Illuminate\Testing\TestResponse;
use Tests\Browser\Support\Category;
use Tests\Browser\Support\Scenario;
use Tests\Request\Support\CategoryPayload;
use Tests\Request\Support\LegacyForm;

/**
 * As telas de catálogo pelo protocolo, sem navegador.
 *
 * O mesmo conjunto que o CategoryTest de browser percorre, exercitado por
 * requisição: um GET para abrir o formulário e um POST para gravar. Roda em
 * segundos, é determinístico e — ao contrário da suíte de browser, que não está
 * em nenhuma `<testsuite>` do phpunit.xml — entra no CI.
 *
 * As duas camadas se dividem assim. Só o browser alcança o que é JavaScript: o
 * `valida()` que barra o envio sem campo obrigatório (a marcação
 * `obrigatorio: true` gera **apenas** regex no cliente, o servidor não tem
 * nenhuma validação equivalente), o `confirm()` da exclusão, a cascata AJAX e o
 * rótulo dos botões. Só esta camada alcança o contrato HTTP: que um cadastro
 * bem-sucedido devolve um redirecionamento para a listagem, e que os campos
 * vazios chegam ao legado como `null` — é o ConvertEmptyStringsToNull, middleware
 * global, e é por isso que nenhum arquivo daqui pode usar WithoutMiddleware.
 *
 * O descritor e o cenário são importados de Tests\Browser\Support porque foi lá
 * que nasceram; quando a pilha de PRs de browser entrar, eles mudam para
 * Tests\Support num commit só de renomeação.
 */
beforeEach(function () {
    // Login próprio, distinto do da suíte de browser, para as duas poderem
    // conviver no mesmo banco `testing`. Cabe em character varying(12).
    $this->scenario = Scenario::install(adminLogin: 'req-tipos');

    $this->actingAs($this->scenario->admin);

    $this->context = [
        'ref_cod_instituicao' => $this->scenario->institution->getKey(),
        'idpais' => $this->scenario->state->country_id,
        'iduf' => $this->scenario->state->getKey(),
        'idmun' => City::query()->where('state_id', $this->scenario->state->getKey())->sole()->getKey(),
    ];
});

/**
 * Afirma que a tela devolveu o usuário para a listagem depois de gravar.
 *
 * É a asserção mais valiosa deste conjunto: nada garantia, até aqui, que um
 * cadastro bem-sucedido volta para onde deveria. Vinte e duas das vinte e três
 * telas fazem isso com um `Location` de verdade — o `simpleRedirect()` do
 * CoreExt levanta uma HttpResponseException com RedirectResponse.
 *
 * A exceção é funcionario_vinculo, que grava e manda o navegador embora com
 * `echo '<script>document.location=...</script>'`, devolvendo 200. É defeito de
 * hoje; quando a tela passar a redirecionar, esta exceção some e o
 * assertRedirect vale para as vinte e três.
 */
function voltaParaListagem(TestResponse $resposta, Category $category): void
{
    $listagem = $category->screen . '_lst.php';

    if ($category->screen === 'funcionario_vinculo') {
        $resposta->assertOk()->assertSee("document.location='{$listagem}'", false);

        return;
    }

    $resposta->assertRedirect($listagem);
}

it('abre a listagem', function (Category $category) {
    $this->get($category->list())
        ->assertOk()
        ->assertSee($category->heading);
})->with(Category::all());

it('cadastra, edita e exclui pelo POST', function (Category $category) {
    $formulario = $this->get($category->form())->assertOk();

    $payload = CategoryPayload::for(
        $category,
        LegacyForm::from($formulario->getContent()),
        $category->name,
        $this->context,
    );

    expect($payload['tipoacao'])->toBe('Novo');

    voltaParaListagem($this->post($category->form(), $payload), $category);

    $registro = $category->record($category->name);

    expect($registro)->not->toBeNull();

    // A edição relê o formulário: os ocultos voltam preenchidos pelo
    // Inicializar(), com a chave e o tipoacao que a própria tela decidiu.
    $formulario = $this->get($category->form($registro->getKey()))->assertOk();

    $edicao = CategoryPayload::for(
        $category,
        LegacyForm::from($formulario->getContent()),
        $category->renamed,
        $this->context,
    );

    // Sem isto um formulário que deixasse de emitir a chave faria um cadastro
    // novo, e o teste passaria em silêncio.
    expect($edicao['tipoacao'])->toBe('Editar');

    voltaParaListagem($this->post($category->form($registro->getKey()), $edicao), $category);

    expect($category->record($category->renamed)?->getKey())->toBe($registro->getKey());

    if (!$category->removable) {
        return;
    }

    voltaParaListagem(
        $this->post($category->form($registro->getKey()), ['tipoacao' => 'Excluir'] + $edicao),
        $category,
    );

    // Pela listagem, e não pelo banco: metade destas telas exclui de forma
    // lógica (`ativo = 0`) e o registro continua na tabela. O que o usuário
    // precisa ver é que ele saiu da lista.
    $this->get($category->list())->assertOk()->assertDontSee($category->renamed);
})->with(Category::all());
