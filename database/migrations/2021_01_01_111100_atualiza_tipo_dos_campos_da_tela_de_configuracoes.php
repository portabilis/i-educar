<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AtualizaTipoDosCamposDaTelaDeConfiguracoes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            UPDATE settings
            SET type = (
                CASE key
                    WHEN \'legacy.code\' THEN \'boolean\'
                    WHEN \'legacy.display_errors\' THEN \'boolean\'
                    WHEN \'legacy.app.administrative_pending.exist\' THEN \'boolean\'
                    WHEN \'legacy.app.alunos.mostrar_codigo_sistema\' THEN \'boolean\'
                    WHEN \'legacy.app.alunos.laudo_medico_obrigatorio\' THEN \'boolean\'
                    WHEN \'legacy.app.alunos.nao_apresentar_campo_alfabetizado\' THEN \'boolean\'
                    WHEN \'legacy.app.alunos.obrigar_recursos_tecnologicos\' THEN \'boolean\'
                    WHEN \'legacy.app.auditoria.notas\' THEN \'boolean\'
                    WHEN \'legacy.app.database.port\' THEN \'integer\'
                    WHEN \'legacy.app.diario.nomenclatura_exame\' THEN \'boolean\'
                    WHEN \'legacy.app.faltas_notas.mostrar_botao_replicar\' THEN \'boolean\'
                    WHEN \'legacy.app.filaunica.criterios\' THEN \'boolean\'
                    WHEN \'legacy.app.filaunica.current_year\' THEN \'integer\'
                    WHEN \'legacy.app.filaunica.trabalho_obrigatorio\' THEN \'boolean\'
                    WHEN \'legacy.app.fisica.exigir_cartao_sus\' THEN \'boolean\'
                    WHEN \'legacy.app.locale.country\' THEN \'integer\'
                    WHEN \'legacy.app.matricula.dependencia\' THEN \'boolean\'
                    WHEN \'legacy.app.matricula.multiplas_matriculas\' THEN \'boolean\'
                    WHEN \'legacy.app.processar_historicos_conceituais\' THEN \'boolean\'
                    WHEN \'legacy.app.projetos.ignorar_turno_igual_matricula\' THEN \'boolean\'
                    WHEN \'legacy.app.recaptcha_v3.minimum_score\' THEN \'float\'
                    WHEN \'legacy.app.recaptcha.options.secure\' THEN \'float\'
                    WHEN \'legacy.app.remove_obrigatorios_cadastro_pessoa\' THEN \'boolean\'
                    WHEN \'legacy.app.reserva_vaga.permite_indeferir_candidatura\' THEN \'boolean\'
                    WHEN \'legacy.app.rg_pessoa_fisica_pais_opcional\' THEN \'boolean\'
                    WHEN \'legacy.config.active_on_ieducar\' THEN \'boolean\'
                    WHEN \'legacy.educacenso.enable_export\' THEN \'boolean\'
                    WHEN \'legacy.filaunica.obriga_certidao_nascimento\' THEN \'boolean\'
                    WHEN \'legacy.report.atestado_vaga_alternativo\' THEN \'boolean\'
                    WHEN \'legacy.report.emitir_tabela_conversao\' THEN \'boolean\'
                    WHEN \'legacy.report.header.alternativo\' THEN \'boolean\'
                    WHEN \'legacy.report.header.show_data_emissao\' THEN \'boolean\'
                    WHEN \'legacy.report.historico_escolar.modelo_sp\' THEN \'boolean\'
                    WHEN \'legacy.report.modelo_atestado_transferencia_botucatu\' THEN \'boolean\'
                    WHEN \'legacy.report.modelo_atestado_transferencia_parauapebas\' THEN \'boolean\'
                    WHEN \'legacy.report.print_back_conclusion_certificate\' THEN \'boolean\'
                    WHEN \'legacy.report.show_error_details\' THEN \'boolean\'
                    WHEN \'legacy.modules.error.send_notification_email\' THEN \'boolean\'
                    WHEN \'legacy.modules.error.send_notification_email\' THEN \'boolean\'
                    WHEN \'legacy.modules.error.track\' THEN \'boolean\'
                    WHEN \'legacy.report.reservas_de_vagas_integrais_por_escola.renda_per_capita_order\' THEN \'boolean\'
                    WHEN \'legacy.report.diario_classe.dias_temporarios\' THEN \'integer\'
                    WHEN \'legacy.app.mailer.debug\' THEN \'boolean\'
                    WHEN \'legacy.modules.error.notification_email\' THEN \'boolean\'
                    WHEN \'preregistration.active\' THEN \'boolean\'
                    WHEN \'preregistration.enabled\' THEN \'boolean\'
                    WHEN \'preregistration.ibge_code\' THEN \'integer\'
                    WHEN \'preregistration.lat\' THEN \'float\'
                    WHEN \'preregistration.lng\' THEN \'float\'
                    WHEN \'preregistration.radius\' THEN \'integer\'
                    WHEN \'preregistration.year\' THEN \'integer\'
                    ELSE type
                END
            );
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('settings')
            ->update(['type' => 'string']);
    }
}
