<?php

use Database\Factories\LegacyGeneralConfigurationFactory;

/**
 * O teste que prova que a camada de browser está de pé.
 *
 * É pequeno de propósito: se ele passa, a corrente inteira funciona — o Pest
 * sobe o servidor HTTP in-process, o Playwright abre o Chromium da imagem, a
 * requisição atravessa o Laravel e o navegador renderiza a resposta.
 *
 * Os testes de browser não entram em nenhuma `<testsuite>` do phpunit.xml, então
 * a rodada padrão do CI não os enxerga; eles são disparados pelo
 * `composer test:browser`.
 */
it('renderiza a tela de login', function () {
    // Sem uma instituição ativa gravada, o middleware Suspended manda qualquer
    // requisição para /intranet/suspenso.php — inclusive a da tela de login.
    LegacyGeneralConfigurationFactory::new()->create([
        'active_on_ieducar' => true,
    ]);

    visit('/login')
        ->assertPathIs('/login')
        ->assertSee('Entrar');
});
