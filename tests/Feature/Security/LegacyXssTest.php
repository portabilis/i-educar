<?php

namespace Tests\Feature\Security;

use App\Models\State;
use Database\Factories\LegacyBenefitFactory;
use Database\Factories\LegacyRaceFactory;
use Database\Factories\LegacyStudentFactory;
use Database\Factories\LegacyUserTypeFactory;
use Database\Factories\StateFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Testing\TestResponse;
use Tests\LoginFirstUser;
use Tests\TestCase;

/**
 * Garante que valores vindos da requisição ou gravados no banco sejam
 * escapados nas telas legadas apontadas pelas CVEs de XSS.
 */
class LegacyXssTest extends TestCase
{
    use DatabaseTransactions, LoginFirstUser, WithoutMiddleware;

    private const MARKER = '<b>CVECANARY</b>';

    private const PAYLOAD = 'x"\'>' . self::MARKER;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loginWithFirstUser();
    }

    private function assertEscaped(TestResponse $response): void
    {
        $response->assertSuccessful();
        $response->assertDontSee(self::MARKER, false);
        $response->assertSee(e(self::MARKER), false);
    }

    public function test_paginador_escapa_os_parametros_mantidos()
    {
        LegacyRaceFactory::new()->create();

        $response = $this->get('/intranet/educar_raca_lst.php?' . http_build_query(['filtro' => self::PAYLOAD]));

        $response->assertSuccessful();
        $response->assertDontSee(self::MARKER, false);
        $response->assertSee('filtro=' . urlencode(self::PAYLOAD), false);
    }

    public function test_listagem_escapa_a_ordenacao_enviada()
    {
        LegacyRaceFactory::new()->create();

        $this->assertEscaped(
            $this->post('/intranet/educar_raca_lst.php', ['ordenacao' => self::PAYLOAD])
        );
    }

    public function test_campo_cpf_escapa_o_valor_informado()
    {
        $this->assertEscaped(
            $this->get('/intranet/pesquisa_pessoa_lst.php?' . http_build_query(['cpf' => self::PAYLOAD]))
        );
    }

    public function test_cadastro_escapa_o_tipoacao_enviado()
    {
        $this->assertEscaped(
            $this->post('/intranet/educar_funcao_cad.php', ['tipoacao' => self::PAYLOAD])
        );
    }

    public function test_listagem_de_matriculas_usa_o_codigo_do_aluno_como_inteiro()
    {
        $student = LegacyStudentFactory::new()->create();

        $response = $this->get('/intranet/educar_matricula_lst.php?' . http_build_query([
            'ref_cod_aluno' => $student->getKey() . self::PAYLOAD,
        ]));

        $response->assertSuccessful();
        $response->assertDontSee('CVECANARY', false);
    }

    public function test_listagem_de_beneficios_escapa_o_nome_gravado()
    {
        LegacyBenefitFactory::new()->create(['name' => self::PAYLOAD]);

        $this->assertEscaped($this->get('/intranet/educar_aluno_beneficio_lst.php'));
    }

    public function test_detalhe_do_beneficio_escapa_nome_e_descricao_gravados()
    {
        $benefit = LegacyBenefitFactory::new()->create([
            'name' => self::PAYLOAD,
            'description' => self::PAYLOAD,
        ]);

        $this->assertEscaped($this->get('/intranet/educar_aluno_beneficio_det.php?cod_aluno_beneficio=' . $benefit->getKey()));
    }

    public function test_edicao_do_beneficio_escapa_a_descricao_no_textarea()
    {
        $benefit = LegacyBenefitFactory::new()->create(['description' => self::PAYLOAD]);

        $this->assertEscaped($this->get('/intranet/educar_aluno_beneficio_cad.php?cod_aluno_beneficio=' . $benefit->getKey()));
    }

    public function test_listagem_de_racas_escapa_o_nome_gravado()
    {
        LegacyRaceFactory::new()->create(['nm_raca' => self::PAYLOAD]);

        $this->assertEscaped($this->get('/intranet/educar_raca_lst.php'));
    }

    public function test_detalhe_da_raca_escapa_o_nome_gravado()
    {
        $race = LegacyRaceFactory::new()->create(['nm_raca' => self::PAYLOAD]);

        $this->assertEscaped($this->get('/intranet/educar_raca_det.php?cod_raca=' . $race->getKey()));
    }

    public function test_tipo_de_usuario_escapa_nome_e_descricao_na_listagem()
    {
        LegacyUserTypeFactory::new()->create([
            'nm_tipo' => self::PAYLOAD,
            'descricao' => self::PAYLOAD,
        ]);

        $this->assertEscaped($this->get('/intranet/educar_tipo_usuario_lst.php'));
    }

    public function test_uf_escapa_o_nome_gravado()
    {
        /** @var State $state */
        $state = StateFactory::new()->create(['name' => self::PAYLOAD]);

        $this->assertEscaped($this->get('/intranet/public_uf_det.php?id=' . $state->getKey()));
    }
}
