<?php

use App\Setting;
use Illuminate\Database\Migrations\Migration;

class PopulateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $settings = [
            'legacy.code' => '1',
            'legacy.display_errors' => '0',
            'legacy.path' => 'ieducar',
            'legacy.apis.access_key' => 'ieducar-access-key',
            'legacy.apis.secret_key' => 'ieducar-secret-key',
            'legacy.apis.educacao_token_header' => null,
            'legacy.apis.educacao_token_key' => null,
            'legacy.app.name' => 'i-Educar',
            'legacy.app.diario.nomenclatura_exame' => '0',
            'legacy.app.database.hostname' => '********',
            'legacy.app.database.port' => '********',
            'legacy.app.database.dbname' => '********',
            'legacy.app.database.username' => '********',
            'legacy.app.database.password' => '********',
            'legacy.app.administrative_pending.exist' => null,
            'legacy.app.administrative_pending.msg' => '<p>Identificamos pend&ecirc;ncias administrativas da sua institui&ccedil;&atilde;o para utiliza&ccedil;&atilde;o do sistema. Sendo assim, pedimos que o respons&aacute;vel pelo sistema entre em contato com o Administrador do sistema o mais breve.</p><br/><b>Telefone:</b> (xx) xxxx-xxxx <br/> <b>E-mail:</b> contato@domain.com.br',
            'legacy.app.aws.bucketname' => null,
            'legacy.app.aws.awsacesskey' => null,
            'legacy.app.aws.awssecretkey' => null,
            'legacy.app.template.vars.instituicao' => 'Prefeitura Municipal',
            'legacy.app.template.pdf.titulo' => 'Relatório i-Educar',
            'legacy.app.template.pdf.logo' => null,
            'legacy.app.template.layout' => 'login.tpl',
            'legacy.app.gtm.id' => null,
            'legacy.app.rdstation.token' => null,
            'legacy.app.rdstation.private_token' => null,
            'legacy.app.locale.country' => '45',
            'legacy.app.locale.province' => 'SP',
            'legacy.app.locale.timezone' => 'America/Sao_Paulo',
            'legacy.app.admin.reports.sql_tempo' => '3',
            'legacy.app.admin.reports.pagina_tempo' => '5',
            'legacy.app.admin.reports.emails' => null,
            'legacy.app.entity.name' => 'i-Educar',
            'legacy.app.superuser' => 'admin',
            'legacy.app.user_accounts.default_password_expiration_period' => '180',
            'legacy.app.instituicao.data_base_deslocamento' => '1',
            'legacy.app.novoeducacao.url' => null,
            'legacy.app.novoeducacao.caminho_api' => null,
            'legacy.app.auditoria.notas' => '1',
            'legacy.app.matricula.dependencia' => '1',
            'legacy.app.matricula.multiplas_matriculas' => '0',
            'legacy.app.alunos.laudo_medico_obrigatorio' => '1',
            'legacy.app.alunos.nao_apresentar_campo_alfabetizado' => '0',
            'legacy.app.alunos.codigo_sistema' => 'Código sistema',
            'legacy.app.alunos.mostrar_codigo_sistema' => '1',
            'legacy.app.alunos.obrigar_recursos_tecnologicos' => '0',
            'legacy.app.alunos.sistema_externo.titulo' => null,
            'legacy.app.alunos.sistema_externo.link' => null,
            'legacy.app.alunos.sistema_externo.token' => null,
            'legacy.app.fisica.exigir_cartao_sus' => false,
            'legacy.app.faltas_notas.mostrar_botao_replicar' => '1',
            'legacy.app.mailer.smtp.from_name' => 'iEducar',
            'legacy.app.mailer.smtp.from_email' => 'hello@domain.com.br',
            'legacy.app.mailer.smtp.host' => 'smtp.mailtrap.io',
            'legacy.app.mailer.smtp.port' => '587',
            'legacy.app.mailer.smtp.auth' => 'tls',
            'legacy.app.mailer.smtp.username' => null,
            'legacy.app.mailer.smtp.password' => null,
            'legacy.app.mailer.smtp.encryption' => 'tls',
            'legacy.app.mailer.debug' => '0',
            'legacy.app.recaptcha.public_key' => null,
            'legacy.app.recaptcha.private_key' => null,
            'legacy.app.recaptcha.options.secure' => true,
            'legacy.app.recaptcha.options.lang' => 'pt',
            'legacy.app.recaptcha.options.theme' => 'white',
            'legacy.app.recaptcha_v3.public_key' => null,
            'legacy.app.recaptcha_v3.private_key' => null,
            'legacy.app.recaptcha_v3.minimum_score' => 0.5,
            'legacy.app.uppercase_names' => 0,
            'legacy.modules.error.link_to_support' => 'https://forum.ieducar.org/',
            'legacy.modules.error.send_notification_email' => true,
            'legacy.modules.error.notification_email' => '1',
            'legacy.modules.error.show_details' => true,
            'legacy.modules.error.track' => false,
            'legacy.modules.error.tracker_name' => 'EMAIL',
            'legacy.modules.error.honeybadger_key' => null,
            'legacy.modules.error.email_recipient' => null,
            'legacy.report.debug' => false,
            'legacy.report.caminho_fundo_certificado' => null,
            'legacy.report.caminho_fundo_carteira_transporte' => null,
            'legacy.report.lei_estudante' => 'Lei municipal',
            'legacy.report.lei_conclusao_ensino_medio' => null,
            'legacy.report.portaria_aprovacao_pontos' => 'Resolução n° 12/2011 - CME Artigo 7° § ;2°;',
            'legacy.report.modelo_ficha_individual' => 'todos',
            'legacy.report.mostrar_relatorios' => null,
            'legacy.report.logo_file_name' => 'brasil.png',
            'legacy.report.show_error_details' => true,
            'legacy.report.default_factory' => 'Portabilis_Report_ReportFactoryPHPJasper',
            'legacy.report.source_path' => '/ieducar/modules/Reports/ReportSources/',
            'legacy.report.diario_classe.dias_temporarios' => '30',
            'legacy.report.remote_factory.url' => null,
            'legacy.report.remote_factory.token' => null,
            'legacy.report.remote_factory.this_app_name' => null,
            'legacy.report.remote_factory.username' => null,
            'legacy.report.remote_factory.password' => null,
            'legacy.report.remote_factory.logo_name' => null,
            'legacy.educacenso.enable_export' => 1,
        ];

        collect($settings)->each(function ($value, $key) {
            if (Setting::query()->where('key', $key)->exists()) {
                return;
            }

            Setting::query()->create([
                'key' => $key,
                'value' => $value,
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Setting::query()->truncate();
    }
}
