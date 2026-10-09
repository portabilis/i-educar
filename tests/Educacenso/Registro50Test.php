<?php

namespace Tests\Educacenso;

use App\Models\Educacenso\Registro50;
use iEducar\Modules\Educacenso\Model\OrganizacaoCurricular;
use Tests\TestCase;

class Registro50Test extends TestCase
{
    public function test_descreve_a_organizacao_curricular_e_a_etapa_da_turma_do_docente(): void
    {
        $docente = new Registro50;
        $docente->organizacaoCurricular = [OrganizacaoCurricular::FORMACAO_GERAL_BASICA, OrganizacaoCurricular::ITINERARIO_FORMATIVO_APROFUNDAMENTO];
        $docente->etapaEducacensoTurma = 14;

        // A descrição da etapa vem de um JSON lido com caminho relativo à intranet.
        $cwd = getcwd();
        chdir(base_path('ieducar/intranet'));

        try {
            $etapa = $docente->estapaEducacensoDescritiva();
        } finally {
            chdir($cwd);
        }

        $descricoes = OrganizacaoCurricular::getDescriptiveValues();

        $this->assertSame(
            $descricoes[OrganizacaoCurricular::FORMACAO_GERAL_BASICA] . '/' . $descricoes[OrganizacaoCurricular::ITINERARIO_FORMATIVO_APROFUNDAMENTO],
            $docente->organizacaoCurricularDescritivas()
        );
        $this->assertNotEmpty($etapa);
        $this->assertSame('', (new Registro50)->organizacaoCurricularDescritivas());
    }
}
