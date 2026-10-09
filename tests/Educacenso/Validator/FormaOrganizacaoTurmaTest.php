<?php

namespace Tests\Educacenso\Validator;

use App\Models\Educacenso\Registro20;
use iEducar\Modules\Educacenso\Model\FormaOrganizacaoTurma as Forma;
use iEducar\Modules\Educacenso\Validator\FormaOrganizacaoTurma;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class FormaOrganizacaoTurmaTest extends TestCase
{
    public static function turmas(): array
    {
        return [
            'sem forma de organização' => [null, 14, true],
            'sem etapa' => [Forma::SERIE_ANO, null, true],
            'educação infantil' => [Forma::CICLOS, 1, true],
            'série/ano no fundamental' => [Forma::SERIE_ANO, 14, true],
            'semestral no fundamental' => [Forma::SEMESTRAL, 14, false],
            'ciclos no curso técnico' => [Forma::CICLOS, 39, false],
            'não seriado na EJA' => [Forma::NAO_SERIADO, 69, true],
            'módulos no ensino médio' => [Forma::MODULES, 25, true],
        ];
    }

    #[DataProvider('turmas')]
    public function test_valida_a_forma_de_organizacao_pela_etapa_da_turma(?int $forma, ?int $etapa, bool $valida): void
    {
        $turma = new Registro20;
        $turma->formasOrganizacaoTurma = $forma;
        $turma->etapaEducacenso = $etapa;

        $validator = new FormaOrganizacaoTurma($turma);

        $this->assertSame($valida, $validator->isValid());
        $this->assertSame('Teste', $validator->getMessage());
    }
}
