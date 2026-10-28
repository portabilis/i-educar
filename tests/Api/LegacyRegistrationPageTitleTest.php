<?php

use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * As telas de falecimento e de abandono nasceram como cópia da tela de
 * solicitação de transferência e herdaram o título dela na aba do navegador.
 * O título é só exibição, mas é o que identifica a tela para quem usa o
 * navegador, e a tela de transferência não tem relação com as duas.
 */
uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    /**
     * O título da aba é o primeiro texto do <title> da página.
     * A busca é ancorada na tag: os mesmos nomes aparecem no breadcrumb da tela.
     */
    $this->browserTitle = function (string $route): string {
        $this->actingAs(LegacyUserFactory::new()->admin()->create());

        $registration = LegacyRegistrationFactory::new()->create();

        $response = $this->get('/intranet/' . $route . '?' . http_build_query([
            'ref_cod_matricula' => $registration->getKey(),
            'ref_cod_aluno' => $registration->ref_cod_aluno,
        ]));

        $response->assertOk();

        preg_match('/<title>(.*?)<\/title>/s', $response->getContent(), $matches);

        $this->assertArrayHasKey(1, $matches, 'A página não tem tag <title>.');

        return trim($matches[1]);
    };
});

test('falecimento page uses its own browser title', function () {
    $this->assertStringStartsWith('Falecimento - ', ($this->browserTitle)('educar_falecido_cad.php'));
});

test('abandono page uses its own browser title', function () {
    $this->assertStringStartsWith('Deixou de frequentar - ', ($this->browserTitle)('educar_abandono_cad.php'));
});
