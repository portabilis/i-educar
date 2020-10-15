<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateCategoriesInSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
            UPDATE settings
            SET setting_category_id = (
                CASE key
                    WHEN 'legacy.apis.access_key' THEN 2
                    WHEN 'legacy.apis.secret_key' THEN 2
                    WHEN 'legacy.apis.educacao_token_header' THEN 2
                    WHEN 'legacy.apis.educacao_token_key' THEN 2
                    WHEN 'legacy.app.novoeducacao.caminho_api' THEN 2
                    WHEN 'legacy.app.novoeducacao.url' THEN 2
                    WHEN 'legacy.app.administrative_pending.exist' THEN 3
                    WHEN 'legacy.app.administrative_pending.msg' THEN 3
                    WHEN 'legacy.app.administrative_tools_url' THEN 3
                    WHEN 'legacy.config.active_on_ieducar' THEN 3
                    WHEN 'legacy.app.database.dbname' THEN 4
                    WHEN 'legacy.app.database.hostname' THEN 4
                    WHEN 'legacy.app.database.password' THEN 4
                    WHEN 'legacy.app.database.port' THEN 4
                    WHEN 'legacy.app.database.username' THEN 4
                    WHEN 'legacy.app.rdstation.private_token' THEN 5
                    WHEN 'legacy.app.rdstation.token' THEN 5
                    WHEN 'legacy.app.recaptcha_v3.minimum_score' THEN 6
                    WHEN 'legacy.app.recaptcha_v3.private_key' THEN 6
                    WHEN 'legacy.app.recaptcha_v3.public_key' THEN 6
                    WHEN 'legacy.app.recaptcha.options.lang' THEN 6
                    WHEN 'legacy.app.recaptcha.options.secure' THEN 6
                    WHEN 'legacy.app.recaptcha.options.theme' THEN 6
                    WHEN 'legacy.app.recaptcha.private_key' THEN 6
                    WHEN 'legacy.app.recaptcha.public_key' THEN 6
                    WHEN 'legacy.modules.error.email_recipient' THEN 7
                    WHEN 'legacy.modules.error.honeybadger_key' THEN 7
                    WHEN 'legacy.modules.error.link_to_support' THEN 7
                    WHEN 'legacy.modules.error.send_notification_email' THEN 7
                    WHEN 'legacy.modules.error.show_details' THEN 7
                    WHEN 'legacy.modules.error.track' THEN 7
                    WHEN 'legacy.modules.error.tracker_name' THEN 7
                    WHEN 'preregistration.active' THEN 8
                    WHEN 'preregistration.city' THEN 8
                    WHEN 'preregistration.enabled' THEN 8
                    WHEN 'preregistration.endpoint' THEN 8
                    WHEN 'preregistration.entity' THEN 8
                    WHEN 'preregistration.google_api_key' THEN 8
                    WHEN 'preregistration.grades' THEN 8
                    WHEN 'preregistration.ibge_code' THEN 8
                    WHEN 'preregistration.lat' THEN 8
                    WHEN 'preregistration.lng' THEN 8
                    WHEN 'preregistration.logo.horizontal' THEN 8
                    WHEN 'preregistration.logo.vertical' THEN 8
                    WHEN 'preregistration.messages.initial_message' THEN 8
                    WHEN 'preregistration.messages.review_message' THEN 8
                    WHEN 'preregistration.messages.subtitle' THEN 8
                    WHEN 'preregistration.messages.success_info' THEN 8
                    WHEN 'preregistration.messages.success_message' THEN 8
                    WHEN 'preregistration.messages.title' THEN 8
                    WHEN 'preregistration.radius' THEN 8
                    WHEN 'preregistration.state_abbreviation' THEN 8
                    WHEN 'preregistration.title' THEN 8
                    WHEN 'preregistration.token' THEN 8
                    WHEN 'preregistration.year' THEN 8
                    WHEN 'legacy.report.atestado_vaga_alternativo' THEN 9
                    WHEN 'legacy.report.caminho_fundo_carteira_transporte' THEN 9
                    WHEN 'legacy.report.caminho_fundo_certificado' THEN 9
                    WHEN 'legacy.report.carteira_estudante.codigo' THEN 9
                    WHEN 'legacy.report.diario_classe.dias_temporarios' THEN 9
                    WHEN 'legacy.report.emitir_tabela_conversao' THEN 9
                    WHEN 'legacy.report.header.alternativo' THEN 9
                    WHEN 'legacy.report.header.show_data_emissao' THEN 9
                    WHEN 'legacy.report.historico_escolar.modelo_sp' THEN 9
                    WHEN 'legacy.report.lei_conclusao_ensino_medio' THEN 9
                    WHEN 'legacy.report.lei_estudante' THEN 9
                    WHEN 'legacy.report.logo_file_name' THEN 9
                    WHEN 'legacy.report.modelo_atestado_transferencia_botucatu' THEN 9
                    WHEN 'legacy.report.modelo_atestado_transferencia_parauapebas' THEN 9
                    WHEN 'legacy.report.modelo_ficha_individual' THEN 9
                    WHEN 'legacy.report.mostrar_relatorios' THEN 9
                    WHEN 'legacy.report.portaria_aprovacao_pontos' THEN 9
                    WHEN 'legacy.report.print_back_conclusion_certificate' THEN 9
                    WHEN 'legacy.report.default_factory' THEN 9
                    WHEN 'legacy.report.remote_factory.logo_name' THEN 9
                    WHEN 'legacy.report.remote_factory.password' THEN 9
                    WHEN 'legacy.report.remote_factory.this_app_name' THEN 9
                    WHEN 'legacy.report.remote_factory.token' THEN 9
                    WHEN 'legacy.report.remote_factory.url' THEN 9
                    WHEN 'legacy.report.remote_factory.username' THEN 9
                    WHEN 'legacy.report.show_error_details' THEN 9
                    WHEN 'legacy.report.source_path' THEN 9
                    WHEN 'legacy.report.reservas_de_vagas_integrais_por_escola.renda_per_capita_order' THEN 9
                    WHEN 'legacy.app.alunos.mostrar_codigo_sistema' THEN 10
                    WHEN 'legacy.app.alunos.codigo_sistema' THEN 10
                    WHEN 'legacy.app.alunos.laudo_medico_obrigatorio' THEN 10
                    WHEN 'legacy.app.alunos.nao_apresentar_campo_alfabetizado' THEN 10
                    WHEN 'legacy.app.alunos.obrigar_recursos_tecnologicos' THEN 10
                    WHEN 'legacy.app.auditoria.notas' THEN 10
                    WHEN 'legacy.app.diario.nomenclatura_exame' THEN 10
                    WHEN 'legacy.app.faltas_notas.mostrar_botao_replicar' THEN 10
                    WHEN 'legacy.app.filaunica.criterios' THEN 10
                    WHEN 'legacy.app.filaunica.current_year' THEN 10
                    WHEN 'legacy.app.filaunica.ordenacao' THEN 10
                    WHEN 'legacy.app.filaunica.trabalho_obrigatorio' THEN 10
                    WHEN 'legacy.app.fisica.exigir_cartao_sus' THEN 10
                    WHEN 'legacy.app.matricula.dependencia' THEN 10
                    WHEN 'legacy.app.matricula.multiplas_matriculas' THEN 10
                    WHEN 'legacy.app.mostrar_aplicacao' THEN 10
                    WHEN 'legacy.app.processar_historicos_conceituais' THEN 10
                    WHEN 'legacy.app.projetos.ignorar_turno_igual_matricula' THEN 10
                    WHEN 'legacy.app.remove_obrigatorios_cadastro_pessoa' THEN 10
                    WHEN 'legacy.app.reserva_vaga.permite_indeferir_candidatura' THEN 10
                    WHEN 'legacy.app.rg_pessoa_fisica_pais_opcional' THEN 10
                    WHEN 'legacy.app.user_accounts.default_password_expiration_period' THEN 10
                    WHEN 'legacy.educacenso.enable_export' THEN 10
                    WHEN 'legacy.filaunica.obriga_certidao_nascimento' THEN 10
                    WHEN 'legacy.app.template.pdf.logo' THEN 10
                    WHEN 'legacy.app.template.vars.instituicao' THEN 10
                    WHEN 'legacy.app.entity.name' THEN 10
                    WHEN 'legacy.app.name' THEN 10
                    ELSE 1
                END
            );
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('settings')
            ->update(['setting_category_id' => 1]);
    }
}
