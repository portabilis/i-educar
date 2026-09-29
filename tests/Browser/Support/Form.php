<?php

namespace Tests\Browser\Support;

use Pest\Browser\Api\AwaitableWebpage;
use Pest\Browser\Api\PendingAwaitablePage;
use Pest\Browser\Api\Webpage;
use PHPUnit\Framework\ExpectationFailedException;

/**
 * Interação com as telas legadas (clsCadastro / clsListagem).
 *
 * As telas geradas por ieducar/intranet/include/clsCampos.inc.php seguem um
 * padrão próprio: todo campo tem `name` igual ao `id`, o formulário carrega um
 * hidden `tipoacao` (Novo|Editar|Excluir), e o botão de gravar é
 * `<input type="button" id="btn_enviar" value="Salvar" onclick="valida()">` —
 * não é submit, então precisa de clique. O clique por texto funciona porque o
 * Playwright casa `input[type=button]` pelo `value`.
 *
 * O tipo do parâmetro é a união dos três estágios da página no plugin do Pest:
 * `visit()` devolve PendingAwaitablePage, as chamadas seguintes AwaitableWebpage
 * (que repete a asserção até passar), e Webpage é o objeto por baixo.
 */
final class Form
{
    /**
     * Grava o formulário e espera a navegação.
     *
     * Confere o rótulo do botão e clica pelo id, e não pelo texto: um clique em
     * seletor que não casa com nada **não falha** no plugin do Pest, fica
     * esperando o elemento aparecer para sempre. Conferir o rótulo antes mantém a
     * garantia que interessa ao rebranding — o botão continua se chamando
     * "Salvar" — sem correr esse risco.
     *
     * O clique dispara `valida()`, que só então faz o submit; sem esperar a rede
     * ficar ociosa as asserções seguintes correm contra a página antiga. Quando a
     * validação barra o submit não há navegação, e a espera volta na hora.
     */
    public static function save(
        PendingAwaitablePage|AwaitableWebpage|Webpage $page,
        string $label = 'Salvar',
    ): AwaitableWebpage|Webpage {
        self::awaitHandler($page, '#btn_enviar');

        return $page->assertValue('#btn_enviar', $label)
            ->click('#btn_enviar')
            ->waitForEvent('networkidle');
    }

    /**
     * Exclui o registro em edição.
     */
    public static function remove(
        PendingAwaitablePage|AwaitableWebpage|Webpage $page,
    ): AwaitableWebpage|Webpage {
        return $page->assertPresent('#btn_excluir')
            ->click('#btn_excluir')
            ->waitForEvent('networkidle');
    }

    /**
     * Dispara a busca de uma listagem.
     */
    public static function search(
        PendingAwaitablePage|AwaitableWebpage|Webpage $page,
    ): AwaitableWebpage|Webpage {
        self::awaitHandler($page, '#botao_busca');

        return $page->assertValue('#botao_busca', 'Buscar')
            ->click('#botao_busca')
            ->waitForEvent('networkidle');
    }

    /**
     * Sai do sistema.
     *
     * O "Sair" fica num dropdown que só aparece no hover, e clicar num elemento
     * invisível faz o plugin esperar para sempre em vez de falhar. Então o teste
     * afirma que o link está lá, com o rótulo e o destino certos — que é o que o
     * rebranding não pode mudar — e navega para o destino, em vez de depender do
     * CSS de hover.
     */
    public static function logout(
        PendingAwaitablePage|AwaitableWebpage|Webpage $page,
    ): AwaitableWebpage|Webpage {
        // assertPresent, e não assertSeeIn: o item está no DOM mas invisível até
        // o hover, e as asserções de texto exigem visibilidade.
        return $page->assertPresent('#logout')
            ->assertSourceHas('id="logout">Sair<')
            ->assertAttributeContains('#logout', 'href', 'intranet/logof.php')
            ->navigate('/intranet/logof.php')
            ->waitForEvent('networkidle');
    }

    /**
     * Preenche vários campos de uma vez.
     *
     * @param  array<string, string>  $fields
     */
    public static function fill(
        PendingAwaitablePage|AwaitableWebpage|Webpage $page,
        array $fields,
    ): AwaitableWebpage|Webpage {
        foreach ($fields as $field => $value) {
            $page = $page->fill($field, $value);
        }

        return $page;
    }

    /**
     * Seleciona os campos em cascata, na ordem.
     *
     * As telas de gestão repopulam os `<select>` dependentes por AJAX
     * (educar_campo_lista.php e os scripts em intranet/scripts/extra), então a
     * opção desejada só existe algum tempo depois do select anterior.
     *
     * A espera é explícita, e não a do selectOption, porque um select cuja opção
     * nunca aparece faz o plugin **travar** em vez de falhar: aqui o teste para
     * com uma mensagem dizendo qual campo ficou sem a opção e o que havia nele.
     *
     * @param  array<string, string|int>  $fields
     */
    public static function cascade(
        PendingAwaitablePage|AwaitableWebpage|Webpage $page,
        array $fields,
    ): AwaitableWebpage|Webpage {
        foreach ($fields as $field => $value) {
            self::awaitOption($page, $field, (string) $value);

            $page = $page->select($field, (string) $value);
        }

        return $page;
    }

    /**
     * Espera a função do `onclick` do botão existir.
     *
     * Os botões do clsCadastro chamam uma função definida em script externo
     * (`valida()`, no caso das telas com JS próprio). O navigate() volta no evento
     * `load`, mas sob carga o servidor in-process às vezes demora a entregar esses
     * scripts, e o clique cai antes: o resultado é um "valida is not defined" e um
     * formulário que nunca é enviado, de forma intermitente.
     */
    private static function awaitHandler(
        PendingAwaitablePage|AwaitableWebpage|Webpage $page,
        string $selector,
    ): void {
        $script = sprintf(
            '(() => { const b = document.querySelector(%s);'
            . ' if (!b) return "botão ausente";'
            . ' const m = (b.getAttribute("onclick") || "").match(/^\\s*([A-Za-z_$][\\w$]*)\\s*\\(/);'
            . ' if (!m) return "ok";'
            . ' return typeof window[m[1]] === "function" ? "ok"'
            . ' : "falta " + m[1] + " (scripts carregados: "'
            . ' + document.querySelectorAll("script[src]").length + ")"; })()',
            json_encode($selector),
        );

        $state = 'botão ausente';

        for ($attempt = 0; $attempt < 25; $attempt++) {
            $state = (string) $page->script($script);

            if ($state === 'ok') {
                return;
            }

            $page->wait(0.2);
        }

        throw new ExpectationFailedException(
            "O botão [{$selector}] não ficou pronto para o clique: {$state}."
        );
    }

    /**
     * Espera a opção aparecer no select, casando por valor ou por rótulo — que é
     * o que o selectOption do Playwright aceita.
     */
    private static function awaitOption(
        PendingAwaitablePage|AwaitableWebpage|Webpage $page,
        string $field,
        string $value,
    ): void {
        $script = sprintf(
            '(() => { const e = document.getElementsByName(%s)[0];'
            . ' if (!e) return "ausente";'
            . ' return Array.from(e.options).some(o => o.value === %s || o.text.trim() === %s)'
            . ' ? "ok" : Array.from(e.options).map(o => o.value + "=" + o.text.trim()).join(" | "); })()',
            json_encode($field),
            json_encode($value),
            json_encode($value),
        );

        $options = 'ausente';

        for ($attempt = 0; $attempt < 25; $attempt++) {
            $options = (string) $page->script($script);

            if ($options === 'ok') {
                return;
            }

            $page->wait(0.2);
        }

        throw new ExpectationFailedException(
            "O campo [{$field}] não recebeu a opção [{$value}]. Opções: {$options}."
        );
    }

    /**
     * Guarda uma imagem da etapa, para comparar o antes e o depois do rebranding.
     */
    public static function shot(
        PendingAwaitablePage|AwaitableWebpage|Webpage $page,
        string $stage,
    ): AwaitableWebpage|Webpage {
        return $page->screenshot(filename: $stage);
    }
}
