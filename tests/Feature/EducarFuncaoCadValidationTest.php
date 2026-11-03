<?php

namespace Tests\Feature;

use App\Models\LegacyRole;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\LoginFirstUser;
use Tests\TestCase;

class EducarFuncaoCadValidationTest extends TestCase
{
    use DatabaseTransactions, LoginFirstUser, WithoutMiddleware;

    public const ROUTE = '/intranet/educar_funcao_cad.php';

    // Teste 1: verifica o cadastro com abreviatura inválida
    public function test_rejeita_cadastro_com_abreviatura_maior(): void
    {
        $dados = [
            'tipoacao' => 'Novo',
            'nm_funcao' => 'Prof',
            'abreviatura' => 'Professor',
            'professor' => 'N',
            'ref_cod_instituicao' => auth()->user()->ref_cod_instituicao,
        ];

        $response = $this->post(self::ROUTE, $dados);

        $this->assertDatabaseMissing('pmieducar.funcao', [
            'nm_funcao' => $dados['nm_funcao'],
            'abreviatura' => $dados['abreviatura'],
            'ref_cod_instituicao' => $dados['ref_cod_instituicao'],
            'ref_usuario_cad' => auth()->id(),
        ]);

        $response->assertSee(
            'A abreviatura não pode ser maior que o nome da função.'
        );
    }

    // Teste 2: verifica a edição com abreviatura inválida e preserva a seleção
    #[DataProvider('opcoesProfessor')]
    public function test_rejeita_edicao_com_abreviatura_maior_e_preserva_professor(string $professor): void
    {
        // Cria uma função válida para ser editada
        $funcao = new LegacyRole;
        $funcao->nm_funcao = 'Professor';
        $funcao->abreviatura = 'Prof';
        $funcao->professor = '0';
        $funcao->ref_cod_instituicao = auth()->user()->ref_cod_instituicao;
        $funcao->ref_usuario_cad = auth()->id();
        $funcao->save();

        // Tenta editar a função com dados inválidos
        $dados = [
            'tipoacao' => 'Editar',
            'cod_funcao' => $funcao->cod_funcao,
            'nm_funcao' => 'Prof',
            'abreviatura' => 'Professor',
            'professor' => $professor,
            'ref_cod_instituicao' => $funcao->ref_cod_instituicao,
        ];

        $response = $this->post(self::ROUTE, $dados);

        // Confirma que os dados originais não foram alterados
        $this->assertDatabaseHas('pmieducar.funcao', [
            'cod_funcao' => $funcao->cod_funcao,
            'nm_funcao' => 'Professor',
            'abreviatura' => 'Prof',
            'professor' => '0',
        ]);

        // Confirma que a mensagem foi exibida
        $response->assertSee(
            'A abreviatura não pode ser maior que o nome da função.'
        );

        $response->assertSee(
            '<option id="professor_' . $professor . '" value="' . $professor . '" selected>',
            false
        );
    }

    public static function opcoesProfessor(): array
    {
        return [
            'não' => ['N'],
            'sim' => ['S'],
        ];
    }
}
