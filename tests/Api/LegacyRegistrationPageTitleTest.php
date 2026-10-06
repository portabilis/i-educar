<?php

namespace Tests\Api;

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
class LegacyRegistrationPageTitleTest extends TestCase
{
    use DatabaseTransactions;

    public function test_falecimento_page_uses_its_own_browser_title(): void
    {
        $this->assertStringStartsWith('Falecimento - ', $this->browserTitle('educar_falecido_cad.php'));
    }

    public function test_abandono_page_uses_its_own_browser_title(): void
    {
        $this->assertStringStartsWith('Deixou de frequentar - ', $this->browserTitle('educar_abandono_cad.php'));
    }

    /**
     * O título da aba é o primeiro texto do <title> da página.
     * A busca é ancorada na tag: os mesmos nomes aparecem no breadcrumb da tela.
     */
    private function browserTitle(string $route): string
    {
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
    }
}
