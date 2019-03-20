<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MoveCustomLabelsToDatabase extends Migration
{
    /**
     * Return default custom labels.
     *
     * @return array
     */
    private function getDefaultCustomLabels()
    {
        return [
            'aluno.detalhe.codigo_aluno' => 'Código Aluno',
            'aluno.detalhe.codigo_estado' => 'Código estado',
            'matricula.detalhe.enturmar' => 'Enturmar',
            'matricula.detalhe.solicitar_transferencia' => 'Solicitar transferência',
            'historico.cadastro.curso_detalhe' => '',
            'historico.cadastro.serie' => 'Série',
            'turma.detalhe.sigla' => 'Sigla',
            'report.termo_assinatura_secretario'  => 'Secretário(a)',
            'report.termo_assinatura_diretor' => 'Gestor(a) da unidade escolar',
        ];
    }

    /**
     * Run the migrations.
     *
     * @return void
     *
     * @throws Throwable
     */
    public function up()
    {
        $setting = DB::table('pmieducar.configuracoes_gerais')
            ->select('ref_cod_instituicao', 'custom_labels')
            ->where('active_on_ieducar', 1)
            ->first();

        if (empty($setting)) {
            return;
        }

        $customLabels = json_decode($setting->custom_labels, true);
        $customLabels = is_array($customLabels) ? array_filter($customLabels) : [];

        $newCustomLabels = array_merge($this->getDefaultCustomLabels(), $customLabels);
        $newCustomLabels = json_encode($newCustomLabels);

        DB::unprepared(
            "
                UPDATE pmieducar.configuracoes_gerais
                SET custom_labels = '{$newCustomLabels}'
                WHERE ref_cod_instituicao = {$setting->ref_cod_instituicao}
            "
        );
    }
}
