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
            'auditoria',
            'auditoria_geral',
            'acesso',
            'deficiencia_excluidos',
            'area_conhecimento_excluidos',
            'componente_curricular_ano_escolar_excluidos',
            'componente_curricular_turma_excluidos',
            'professor_turma_excluidos',
            'regra_avaliacao_recuperacao_excluidos',
            'regra_avaliacao_serie_ano_excluidos',
            'aluno_excluidos',
            'disciplina_dependencia_excluidos',
            'dispensa_disciplina_excluidos',
            'escola_serie_disciplina_excluidos',
            'matricula_turma_excluidos',
            'migrations',
            'telescope_entries',
            'telescope_entries_tags',
            'telescope_monitoring',
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
