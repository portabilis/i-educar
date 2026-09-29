<?php

namespace Tests\Browser\Support;

use Pest\Browser\Api\AwaitableWebpage;
use Pest\Browser\Api\PendingAwaitablePage;
use Pest\Browser\Api\Webpage;

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
     * (educar_campo_lista.php: getEscola(), getCurso(), getSerie(), getTurma()),
     * então a opção desejada só existe algum tempo depois do select anterior.
     * O `selectOption` do Playwright tenta de novo até a opção aparecer, e casa
     * por valor **ou** rótulo — é isso que faz a cascata funcionar sem sleeps.
     *
     * @param  array<string, string|int>  $fields
     */
    public static function cascade(
        PendingAwaitablePage|AwaitableWebpage|Webpage $page,
        array $fields,
    ): AwaitableWebpage|Webpage {
        foreach ($fields as $field => $value) {
            $page = $page->select($field, (string) $value)->waitForEvent('networkidle');
        }

        return $page;
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
