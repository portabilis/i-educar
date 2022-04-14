<?php

use App\Support\Database\AuditTrigger;
use Illuminate\Database\Migrations\Migration;

class AuditTriggers extends Migration
{
    use AuditTrigger;

    /**
     * @var bool
     */
    public $withinTransaction = false;

    /**
     * Return not audited tables.
     *
     * @return array
     */
    public function getSkippedTables()
    {
        return config('audit.skip', [
            'ieducar_audit',
            'public.ieducar_audit',
            'modules.auditoria',
            'modules.auditoria_geral',
            'portal.acesso',
            'cadastro.deficiencia_excluidos',
            'modules.area_conhecimento_excluidos',
            'modules.componente_curricular_ano_escolar_excluidos',
            'modules.componente_curricular_turma_excluidos',
            'modules.professor_turma_excluidos',
            'modules.regra_avaliacao_recuperacao_excluidos',
            'modules.regra_avaliacao_serie_ano_excluidos',
            'pmieducar.aluno_excluidos',
            'pmieducar.disciplina_dependencia_excluidos',
            'pmieducar.dispensa_disciplina_excluidos',
            'pmieducar.escola_serie_disciplina_excluidos',
            'pmieducar.matricula_turma_excluidos',
            'public.migrations',
            'migrations',
        ]);
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createAuditTriggers();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropAuditTriggers();
    }
}
