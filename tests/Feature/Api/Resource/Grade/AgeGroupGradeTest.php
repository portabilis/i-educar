<?php

namespace Tests\Feature\Api\Resource\Grade;

use App\Models\LegacyCourse;
use App\Models\LegacyGrade; // representa pmieducar.serie
use Database\Factories\LegacyCourseFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AgeGroupGradeTest extends TestCase
{
    use DatabaseTransactions;

    private LegacyCourse $course;

    /**
     * Rota usada para cadastrar/editar série.
     * Ajuste aqui se você tiver uma route nomeada em vez de path.
     */
    private string $route = '/intranet/educar_serie_cad.php';

    protected function setUp(): void
    {
        parent::setUp();

        // Cria um curso legacy para associar à série
        $this->course = LegacyCourseFactory::new()->create();

        // Se você precisar autenticar um usuário legacy, faça aqui.
        // Exemplo (ajuste para o seu projeto):
        // $this->actingAs(User::factory()->legacyUser()->create(), 'legacy');
    }

    /**
     * Helper com os dados mínimos válidos para cadastrar uma série.
     */
    private function payloadSerie(array $override = []): array
    {
        $base = [
            'ref_cod_curso'          => $this->course->id,
            'nm_serie'               => 'Pré-Escolar',
            'etapa_curso'            => 1,
            'concluinte'             => 1,
            'carga_horaria'          => '800',
            'dias_letivos'           => 200,
            'idade_inicial'          => 6,
            'idade_ideal'            => 7,
            'idade_final'            => 10,
            'regras_avaliacao_id'    => 1, // ajuste conforme seeds/fixtures do projeto
            // Outros campos obrigatórios da tela, se houver
        ];

        return array_merge($base, $override);
    }

    public function test_nao_deve_permitir_idade_inicial_negativa_ao_cadastrar(): void
    {
        $dados = $this->payloadSerie([
            'idade_inicial' => -1,
        ]);

        $response = $this->post($this->route, $dados);

        // A tela legacy costuma usar $this->mensagem na sessão (flash)
        $response->assertSessionHas('mensagem');

        $mensagem = session('mensagem');
        $this->assertIsString($mensagem);
        $this->assertStringContainsString('idade', $mensagem);

        // Garante que não foi salva série com idade_inicial negativa
        $this->assertDatabaseMissing('pmieducar.serie', [
            'nm_serie'      => 'Pré-Escolar',
            'idade_inicial' => -1,
        ]);
    }

//     public function test_nao_deve_permitir_idade_final_negativa_ao_cadastrar(): void
//     {
//         $dados = $this->payloadSerie([
//             'idade_final' => -5,
//         ]);

//         $response = $this->post($this->route, $dados);

//         $response->assertSessionHas('mensagem');

//         $mensagem = session('mensagem');
//         $this->assertIsString($mensagem);
//         $this->assertStringContainsString('idade', $mensagem);

//         $this->assertDatabaseMissing('pmieducar.serie', [
//             'nm_serie'     => 'Pré-Escolar',
//             'idade_final'  => -5,
//         ]);
//     }

//     public function test_nao_deve_permitir_idade_ideal_negativa_ao_cadastrar(): void
//     {
//         $dados = $this->payloadSerie([
//             'idade_ideal' => -7,
//         ]);

//         $response = $this->post($this->route, $dados);

//         $response->assertSessionHas('mensagem');

//         $mensagem = session('mensagem');
//         $this->assertIsString($mensagem);
//         $this->assertStringContainsString('idade', $mensagem);

//         $this->assertDatabaseMissing('pmieducar.serie', [
//             'nm_serie'    => 'Pré-Escolar',
//             'idade_ideal' => -7,
//         ]);
//     }

//     public function test_deve_permitir_idades_zero_ao_cadastrar(): void
//     {
//         $dados = $this->payloadSerie([
//             'idade_inicial' => 0,
//             'idade_ideal'   => 0,
//             'idade_final'   => 0,
//         ]);

//         $response = $this->post($this->route, $dados);

//         // No sucesso a tela normalmente redireciona para a listagem
//         $response->assertStatus(302);

//         $this->assertDatabaseHas('pmieducar.serie', [
//             'nm_serie'      => 'Pré-Escolar',
//             'idade_inicial' => 0,
//             'idade_ideal'   => 0,
//             'idade_final'   => 0,
//         ]);
//     }

//     public function test_deve_permitir_idades_positivas_ao_cadastrar(): void
//     {
//         $dados = $this->payloadSerie([
//             'idade_inicial' => 6,
//             'idade_ideal'   => 7,
//             'idade_final'   => 10,
//         ]);

//         $response = $this->post($this->route, $dados);

//         $response->assertStatus(302);

//         $this->assertDatabaseHas('pmieducar.serie', [
//             'nm_serie'      => '1ª Série',
//             'idade_inicial' => 6,
//             'idade_ideal'   => 7,
//             'idade_final'   => 10,
//         ]);
//     }

//     public function test_nao_deve_permitir_editar_para_idades_negativas(): void
//     {
//         // 1) Cria uma série válida direto no banco (usando o fluxo normal)
//         $this->post($this->route, $this->payloadSerie());

//         /** @var LegacyGrade $serie */
//         $serie = LegacyGrade::query()
//             ->where('nm_serie', '1ª Série')
//             ->firstOrFail();

//         // 2) Tenta editar com idade_inicial negativa
//         $dadosEdicao = $this->payloadSerie([
//             'cod_serie'     => $serie->id,
//             'idade_inicial' => -3,
//         ]);

//         $response = $this->post($this->route, $dadosEdicao);

//         $response->assertSessionHas('mensagem');

//         $mensagem = session('mensagem');
//         $this->assertIsString($mensagem);
//         $this->assertStringContainsString('idade', $mensagem);

//         // Garante que o valor negativo não foi salvo
//         $this->assertDatabaseMissing('pmieducar.serie', [
//             'cod_serie'     => $serie->id,
//             'idade_inicial' => -3,
//         ]);
//     }
}
