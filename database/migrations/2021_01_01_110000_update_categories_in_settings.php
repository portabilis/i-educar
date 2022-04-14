<?php

use App\Support\Database\SettingCategoryTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateCategoriesInSettings extends Migration
{
    use SettingCategoryTrait;

    public function up()
    {
        DB::unprepared('
            UPDATE settings
            SET setting_category_id = (
                CASE key
                    WHEN \'legacy.apis.access_key\' THEN ' . $this->getSettingCategoryIdByName('Integração entre iEducar e iDiário') . '
                    WHEN \'legacy.apis.secret_key\' THEN ' . $this->getSettingCategoryIdByName('Integração entre iEducar e iDiário') . '
                    WHEN \'legacy.apis.educacao_token_header\' THEN ' . $this->getSettingCategoryIdByName('Integração entre iEducar e iDiário') . '
                    WHEN \'legacy.apis.educacao_token_key\' THEN ' . $this->getSettingCategoryIdByName('Integração entre iEducar e iDiário') . '
                    WHEN \'legacy.app.novoeducacao.caminho_api\' THEN ' . $this->getSettingCategoryIdByName('Integração entre iEducar e iDiário') . '
                    WHEN \'legacy.app.novoeducacao.url\' THEN ' . $this->getSettingCategoryIdByName('Integração entre iEducar e iDiário') . '
                    WHEN \'legacy.app.administrative_pending.exist\' THEN ' . $this->getSettingCategoryIdByName('Administrativo') . '
                    WHEN \'legacy.app.administrative_pending.msg\' THEN ' . $this->getSettingCategoryIdByName('Administrativo') . '
                    WHEN \'legacy.app.administrative_tools_url\' THEN ' . $this->getSettingCategoryIdByName('Administrativo') . '
                    WHEN \'legacy.config.active_on_ieducar\' THEN ' . $this->getSettingCategoryIdByName('Administrativo') . '
                    WHEN \'legacy.app.database.dbname\' THEN ' . $this->getSettingCategoryIdByName('Banco de dados') . '
                    WHEN \'legacy.app.database.hostname\' THEN ' . $this->getSettingCategoryIdByName('Banco de dados') . '
                    WHEN \'legacy.app.database.password\' THEN ' . $this->getSettingCategoryIdByName('Banco de dados') . '
                    WHEN \'legacy.app.database.port\' THEN ' . $this->getSettingCategoryIdByName('Banco de dados') . '
                    WHEN \'legacy.app.database.username\' THEN ' . $this->getSettingCategoryIdByName('Banco de dados') . '
                    WHEN \'legacy.app.rdstation.private_token\' THEN ' . $this->getSettingCategoryIdByName('RDStation') . '
                    WHEN \'legacy.app.rdstation.token\' THEN ' . $this->getSettingCategoryIdByName('RDStation') . '
                    WHEN \'legacy.app.recaptcha_v3.minimum_score\' THEN ' . $this->getSettingCategoryIdByName('Recaptcha v2') . '
                    WHEN \'legacy.app.recaptcha_v3.private_key\' THEN ' . $this->getSettingCategoryIdByName('Recaptcha v2') . '
                    WHEN \'legacy.app.recaptcha_v3.public_key\' THEN ' . $this->getSettingCategoryIdByName('Recaptcha v2') . '
                    WHEN \'legacy.app.recaptcha.options.lang\' THEN ' . $this->getSettingCategoryIdByName('Recaptcha v2') . '
                    WHEN \'legacy.app.recaptcha.options.secure\' THEN ' . $this->getSettingCategoryIdByName('Recaptcha v2') . '
                    WHEN \'legacy.app.recaptcha.options.theme\' THEN ' . $this->getSettingCategoryIdByName('Recaptcha v2') . '
                    WHEN \'legacy.app.recaptcha.private_key\' THEN ' . $this->getSettingCategoryIdByName('Recaptcha v2') . '
                    WHEN \'legacy.app.recaptcha.public_key\' THEN ' . $this->getSettingCategoryIdByName('Recaptcha v2') . '
                    WHEN \'legacy.modules.error.email_recipient\' THEN ' . $this->getSettingCategoryIdByName('Track de erros') . '
                    WHEN \'legacy.modules.error.honeybadger_key\' THEN ' . $this->getSettingCategoryIdByName('Track de erros') . '
                    WHEN \'legacy.modules.error.link_to_support\' THEN ' . $this->getSettingCategoryIdByName('Track de erros') . '
                    WHEN \'legacy.modules.error.send_notification_email\' THEN ' . $this->getSettingCategoryIdByName('Track de erros') . '
                    WHEN \'legacy.modules.error.show_details\' THEN ' . $this->getSettingCategoryIdByName('Track de erros') . '
                    WHEN \'legacy.modules.error.track\' THEN ' . $this->getSettingCategoryIdByName('Track de erros') . '
                    WHEN \'legacy.modules.error.tracker_name\' THEN ' . $this->getSettingCategoryIdByName('Track de erros') . '
                    WHEN \'preregistration.active\' THEN ' . $this->getSettingCategoryIdByName('Inscrições online') . '
                    WHEN \'preregistration.city\' THEN ' . $this->getSettingCategoryIdByName('Inscrições online') . '
                    WHEN \'preregistration.enabled\' THEN ' . $this->getSettingCategoryIdByName('Inscrições online') . '
                    WHEN \'preregistration.endpoint\' THEN ' . $this->getSettingCategoryIdByName('Inscrições online') . '
                    WHEN \'preregistration.entity\' THEN ' . $this->getSettingCategoryIdByName('Inscrições online') . '
                    WHEN \'preregistration.google_api_key\' THEN ' . $this->getSettingCategoryIdByName('Inscrições online') . '
                    WHEN \'preregistration.grades\' THEN ' . $this->getSettingCategoryIdByName('Inscrições online') . '
                    WHEN \'preregistration.ibge_code\' THEN ' . $this->getSettingCategoryIdByName('Inscrições online') . '
                    WHEN \'preregistration.lat\' THEN ' . $this->getSettingCategoryIdByName('Inscrições online') . '
                    WHEN \'preregistration.lng\' THEN ' . $this->getSettingCategoryIdByName('Inscrições online') . '
                    WHEN \'preregistration.logo.horizontal\' THEN ' . $this->getSettingCategoryIdByName('Inscrições online') . '
                    WHEN \'preregistration.logo.vertical\' THEN ' . $this->getSettingCategoryIdByName('Inscrições online') . '
                    WHEN \'preregistration.messages.initial_message\' THEN ' . $this->getSettingCategoryIdByName('Inscrições online') . '
                    WHEN \'preregistration.messages.review_message\' THEN ' . $this->getSettingCategoryIdByName('Inscrições online') . '
                    WHEN \'preregistration.messages.subtitle\' THEN ' . $this->getSettingCategoryIdByName('Inscrições online') . '
                    WHEN \'preregistration.messages.success_info\' THEN ' . $this->getSettingCategoryIdByName('Inscrições online') . '
                    WHEN \'preregistration.messages.success_message\' THEN ' . $this->getSettingCategoryIdByName('Inscrições online') . '
                    WHEN \'preregistration.messages.title\' THEN ' . $this->getSettingCategoryIdByName('Inscrições online') . '
                    WHEN \'preregistration.radius\' THEN ' . $this->getSettingCategoryIdByName('Inscrições online') . '
                    WHEN \'preregistration.state_abbreviation\' THEN ' . $this->getSettingCategoryIdByName('Inscrições online') . '
                    WHEN \'preregistration.title\' THEN ' . $this->getSettingCategoryIdByName('Inscrições online') . '
                    WHEN \'preregistration.token\' THEN ' . $this->getSettingCategoryIdByName('Inscrições online') . '
                    WHEN \'preregistration.year\' THEN ' . $this->getSettingCategoryIdByName('Inscrições online') . '
                    WHEN \'legacy.report.atestado_vaga_alternativo\' THEN ' . $this->getSettingCategoryIdByName('Validações de relatórios') . '
                    WHEN \'legacy.report.caminho_fundo_carteira_transporte\' THEN ' . $this->getSettingCategoryIdByName('Validações de relatórios') . '
                    WHEN \'legacy.report.caminho_fundo_certificado\' THEN ' . $this->getSettingCategoryIdByName('Validações de relatórios') . '
                    WHEN \'legacy.report.carteira_estudante.codigo\' THEN ' . $this->getSettingCategoryIdByName('Validações de relatórios') . '
                    WHEN \'legacy.report.diario_classe.dias_temporarios\' THEN ' . $this->getSettingCategoryIdByName('Validações de relatórios') . '
                    WHEN \'legacy.report.emitir_tabela_conversao\' THEN ' . $this->getSettingCategoryIdByName('Validações de relatórios') . '
                    WHEN \'legacy.report.header.alternativo\' THEN ' . $this->getSettingCategoryIdByName('Validações de relatórios') . '
                    WHEN \'legacy.report.header.show_data_emissao\' THEN ' . $this->getSettingCategoryIdByName('Validações de relatórios') . '
                    WHEN \'legacy.report.historico_escolar.modelo_sp\' THEN ' . $this->getSettingCategoryIdByName('Validações de relatórios') . '
                    WHEN \'legacy.report.lei_conclusao_ensino_medio\' THEN ' . $this->getSettingCategoryIdByName('Validações de relatórios') . '
                    WHEN \'legacy.report.lei_estudante\' THEN ' . $this->getSettingCategoryIdByName('Validações de relatórios') . '
                    WHEN \'legacy.report.logo_file_name\' THEN ' . $this->getSettingCategoryIdByName('Validações de relatórios') . '
                    WHEN \'legacy.report.modelo_atestado_transferencia_botucatu\' THEN ' . $this->getSettingCategoryIdByName('Validações de relatórios') . '
                    WHEN \'legacy.report.modelo_atestado_transferencia_parauapebas\' THEN ' . $this->getSettingCategoryIdByName('Validações de relatórios') . '
                    WHEN \'legacy.report.modelo_ficha_individual\' THEN ' . $this->getSettingCategoryIdByName('Validações de relatórios') . '
                    WHEN \'legacy.report.mostrar_relatorios\' THEN ' . $this->getSettingCategoryIdByName('Validações de relatórios') . '
                    WHEN \'legacy.report.portaria_aprovacao_pontos\' THEN ' . $this->getSettingCategoryIdByName('Validações de relatórios') . '
                    WHEN \'legacy.report.print_back_conclusion_certificate\' THEN ' . $this->getSettingCategoryIdByName('Validações de relatórios') . '
                    WHEN \'legacy.report.default_factory\' THEN ' . $this->getSettingCategoryIdByName('Validações de relatórios') . '
                    WHEN \'legacy.report.remote_factory.logo_name\' THEN ' . $this->getSettingCategoryIdByName('Validações de relatórios') . '
                    WHEN \'legacy.report.remote_factory.password\' THEN ' . $this->getSettingCategoryIdByName('Validações de relatórios') . '
                    WHEN \'legacy.report.remote_factory.this_app_name\' THEN ' . $this->getSettingCategoryIdByName('Validações de relatórios') . '
                    WHEN \'legacy.report.remote_factory.token\' THEN ' . $this->getSettingCategoryIdByName('Validações de relatórios') . '
                    WHEN \'legacy.report.remote_factory.url\' THEN ' . $this->getSettingCategoryIdByName('Validações de relatórios') . '
                    WHEN \'legacy.report.remote_factory.username\' THEN ' . $this->getSettingCategoryIdByName('Validações de relatórios') . '
                    WHEN \'legacy.report.show_error_details\' THEN ' . $this->getSettingCategoryIdByName('Validações de relatórios') . '
                    WHEN \'legacy.report.source_path\' THEN ' . $this->getSettingCategoryIdByName('Validações de relatórios') . '
                    WHEN \'legacy.report.reservas_de_vagas_integrais_por_escola.renda_per_capita_order\' THEN ' . $this->getSettingCategoryIdByName('Validações de relatórios') . '
                    WHEN \'legacy.app.alunos.mostrar_codigo_sistema\' THEN ' . $this->getSettingCategoryIdByName('Validações de sistema') . '
                    WHEN \'legacy.app.alunos.codigo_sistema\' THEN ' . $this->getSettingCategoryIdByName('Validações de sistema') . '
                    WHEN \'legacy.app.alunos.laudo_medico_obrigatorio\' THEN ' . $this->getSettingCategoryIdByName('Validações de sistema') . '
                    WHEN \'legacy.app.alunos.nao_apresentar_campo_alfabetizado\' THEN ' . $this->getSettingCategoryIdByName('Validações de sistema') . '
                    WHEN \'legacy.app.alunos.obrigar_recursos_tecnologicos\' THEN ' . $this->getSettingCategoryIdByName('Validações de sistema') . '
                    WHEN \'legacy.app.auditoria.notas\' THEN ' . $this->getSettingCategoryIdByName('Validações de sistema') . '
                    WHEN \'legacy.app.diario.nomenclatura_exame\' THEN ' . $this->getSettingCategoryIdByName('Validações de sistema') . '
                    WHEN \'legacy.app.faltas_notas.mostrar_botao_replicar\' THEN ' . $this->getSettingCategoryIdByName('Validações de sistema') . '
                    WHEN \'legacy.app.filaunica.criterios\' THEN ' . $this->getSettingCategoryIdByName('Validações de sistema') . '
                    WHEN \'legacy.app.filaunica.current_year\' THEN ' . $this->getSettingCategoryIdByName('Validações de sistema') . '
                    WHEN \'legacy.app.filaunica.ordenacao\' THEN ' . $this->getSettingCategoryIdByName('Validações de sistema') . '
                    WHEN \'legacy.app.filaunica.trabalho_obrigatorio\' THEN ' . $this->getSettingCategoryIdByName('Validações de sistema') . '
                    WHEN \'legacy.app.fisica.exigir_cartao_sus\' THEN ' . $this->getSettingCategoryIdByName('Validações de sistema') . '
                    WHEN \'legacy.app.matricula.dependencia\' THEN ' . $this->getSettingCategoryIdByName('Validações de sistema') . '
                    WHEN \'legacy.app.matricula.multiplas_matriculas\' THEN ' . $this->getSettingCategoryIdByName('Validações de sistema') . '
                    WHEN \'legacy.app.mostrar_aplicacao\' THEN ' . $this->getSettingCategoryIdByName('Validações de sistema') . '
                    WHEN \'legacy.app.processar_historicos_conceituais\' THEN ' . $this->getSettingCategoryIdByName('Validações de sistema') . '
                    WHEN \'legacy.app.projetos.ignorar_turno_igual_matricula\' THEN ' . $this->getSettingCategoryIdByName('Validações de sistema') . '
                    WHEN \'legacy.app.remove_obrigatorios_cadastro_pessoa\' THEN ' . $this->getSettingCategoryIdByName('Validações de sistema') . '
                    WHEN \'legacy.app.reserva_vaga.permite_indeferir_candidatura\' THEN ' . $this->getSettingCategoryIdByName('Validações de sistema') . '
                    WHEN \'legacy.app.rg_pessoa_fisica_pais_opcional\' THEN ' . $this->getSettingCategoryIdByName('Validações de sistema') . '
                    WHEN \'legacy.app.user_accounts.default_password_expiration_period\' THEN ' . $this->getSettingCategoryIdByName('Validações de sistema') . '
                    WHEN \'legacy.educacenso.enable_export\' THEN ' . $this->getSettingCategoryIdByName('Validações de sistema') . '
                    WHEN \'legacy.filaunica.obriga_certidao_nascimento\' THEN ' . $this->getSettingCategoryIdByName('Validações de sistema') . '
                    WHEN \'legacy.app.template.pdf.logo\' THEN ' . $this->getSettingCategoryIdByName('Validações de sistema') . '
                    WHEN \'legacy.app.template.vars.instituicao\' THEN ' . $this->getSettingCategoryIdByName('Validações de sistema') . '
                    WHEN \'legacy.app.entity.name\' THEN ' . $this->getSettingCategoryIdByName('Validações de sistema') . '
                    WHEN \'legacy.app.name\' THEN ' . $this->getSettingCategoryIdByName('Validações de sistema') . '
                    WHEN \'legacy.app.aws.awsacesskey\' THEN ' . $this->getSettingCategoryIdByName('AWS - S3 Armazenamento') . '
                    WHEN \'legacy.app.aws.bucketname\' THEN ' . $this->getSettingCategoryIdByName('AWS - S3 Armazenamento') . '
                    WHEN \'legacy.app.aws.awssecretkey\' THEN ' . $this->getSettingCategoryIdByName('AWS - S3 Armazenamento') . '
                    WHEN \'legacy.app.recaptcha_v3.private_key\' THEN ' . $this->getSettingCategoryIdByName('Recaptcha v3') . '
                    WHEN \'legacy.app.recaptcha_v3.public_key\' THEN ' . $this->getSettingCategoryIdByName('Recaptcha v3') . '
                    WHEN \'legacy.app.recaptcha_v3.minimum_score\' THEN ' . $this->getSettingCategoryIdByName('Recaptcha v3') . '
                    WHEN \'legacy.app.mailer.smtp.from_email\' THEN ' . $this->getSettingCategoryIdByName('SMTP - Configuração para envio de E-mail') . '
                    WHEN \'legacy.app.mailer.smtp.auth\' THEN ' . $this->getSettingCategoryIdByName('SMTP - Configuração para envio de E-mail') . '
                    WHEN \'legacy.app.mailer.smtp.from_name\' THEN ' . $this->getSettingCategoryIdByName('SMTP - Configuração para envio de E-mail') . '
                    WHEN \'legacy.app.mailer.smtp.host\' THEN ' . $this->getSettingCategoryIdByName('SMTP - Configuração para envio de E-mail') . '
                    WHEN \'legacy.app.mailer.smtp.password\' THEN ' . $this->getSettingCategoryIdByName('SMTP - Configuração para envio de E-mail') . '
                    WHEN \'legacy.app.mailer.smtp.username\' THEN ' . $this->getSettingCategoryIdByName('SMTP - Configuração para envio de E-mail') . '
                    WHEN \'legacy.app.mailer.smtp.port\' THEN ' . $this->getSettingCategoryIdByName('SMTP - Configuração para envio de E-mail') . '
                    WHEN \'legacy.app.mailer.smtp.encryption\' THEN ' . $this->getSettingCategoryIdByName('SMTP - Configuração para envio de E-mail') . '
                    WHEN \'legacy.app.mailer.debug\' THEN ' . $this->getSettingCategoryIdByName('SMTP - Configuração para envio de E-mail') . '
                    ELSE setting_category_id
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
        $settingCategoryId = $this->getSettingCategoryIdByName('Sem categoria');
        DB::table('settings')
            ->update(['setting_category_id' => $settingCategoryId]);
    }
}
