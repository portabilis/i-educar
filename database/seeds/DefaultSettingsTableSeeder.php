<?php

use App\Setting;
use Illuminate\Database\Seeder;

class DefaultSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            'legacy.app.name' => 'i-Educar',
            'legacy.app.superuser' => 'admin',
            'legacy.app.database.hostname' => 'localhost',
            'legacy.app.database.port' => '5432',
            'legacy.app.database.dbname' => 'ieducar',
            'legacy.app.database.username' => 'ieducar',
            'legacy.app.database.password' => 'ieducar',
            'legacy.app.administrative_pending.exist' => '',
            'legacy.app.administrative_pending.msg' => '',
            'legacy.app.entity.name' => 'Prefeitura Municipal',
            'legacy.app.aws.bucketname' => '',
            'legacy.app.aws.awsacesskey' => '',
            'legacy.app.aws.awssecretkey' => '',
            'legacy.app.diario.nomenclatura_exame' => '0',
            'legacy.app.template.vars.instituicao' => 'i-Educar',
            'legacy.app.template.pdf.titulo' => 'Relatório i-Educar',
            'legacy.app.template.pdf.logo' => '',
            'legacy.app.template.layout' => 'login.tpl',
            'legacy.app.gtm.id' => '',
            'legacy.app.rdstation.token' => '',
            'legacy.app.rdstation.private_token' => '',
            'legacy.app.locale.country' => '45',
            'legacy.app.locale.province' => 'SP',
            'legacy.app.locale.timezone' => 'America/Sao_Paulo',
            'legacy.app.admin.reports.sql_tempo' => '3',
            'legacy.app.admin.reports.pagina_tempo' => '5',
            'legacy.app.admin.reports.emails' => '',
            'legacy.app.user_accounts.default_password_expiration_period' => '180',
            'legacy.app.auditoria.notas' => '1',
            'legacy.app.matricula.dependencia' => '1',
            'legacy.app.alunos.laudo_medico_obrigatorio' => '1',
            'legacy.app.alunos.nao_apresentar_campo_alfabetizado' => '1',
            'legacy.app.alunos.codigo_sistema' => 'Código sistema',
            'legacy.app.alunos.mostrar_codigo_sistema' => '1',
            'legacy.app.faltas_notas.mostrar_botao_replicar' => '1',
            'legacy.app.mailer.smtp.from_name' => '',
            'legacy.app.mailer.smtp.from_email' => '',
            'legacy.app.mailer.smtp.host' => '',
            'legacy.app.mailer.smtp.port' => '',
            'legacy.app.mailer.smtp.auth' => '',
            'legacy.app.mailer.smtp.username' => '',
            'legacy.app.mailer.smtp.password' => '',
            'legacy.app.mailer.smtp.encryption' => '',
            'legacy.app.mailer.debug' => '',
            'legacy.app.recaptcha.public_key' => '',
            'legacy.app.recaptcha.private_key' => '',
            'legacy.app.recaptcha.options.secure' => '1',
            'legacy.app.recaptcha.options.lang' => 'pt',
            'legacy.app.recaptcha.options.theme' => 'white',
            'legacy.apis.access_key' => '',
            'legacy.apis.secret_key' => '',
            'legacy.apis.educacao_token_header' => '',
            'legacy.apis.educacao_token_key' => '',
            'legacy.report.diario_classe.dias_temporarios' => '30',
            'legacy.report.lei_estudante' => 'Lei municipal',
            'legacy.report.lei_conclusao_ensino_medio' => '1319/99',
            'legacy.report.portaria_aprovacao_pontos' => 'Resolução n° 12/2011 - CME, Artigo 7°, § 2°',
            'legacy.report.modelo_ficha_individual' => 'todos',
            'legacy.report.mostrar_relatorios' => '',
            'legacy.report.show_error_details' => '1',
            'legacy.report.default_factory' => 'Portabilis_Report_ReportFactoryPHPJasper',
            'legacy.report.remote_factory.url' => '',
            'legacy.report.remote_factory.token' => '',
            'legacy.report.source_path' => '/var/www/ieducar/ieducar/modules/Reports/ReportSources/',
            'legacy.report.logo_file_name' => 'brasil.png',
            'legacy.modules.error.link_to_support' => 'https://forum.ieducar.org/',
            'legacy.modules.error.send_notification_email' => '1',
            'legacy.modules.error.show_details' => '1',
            'legacy.modules.error.track' => '',
            'legacy.modules.error.tracker_name' => 'EMAIL',
            'legacy.modules.error.honeybadger_key' => '',
            'legacy.modules.error.email_recipient' => '',
        ];

        collect($settings)->each(function ($value, $key) {
            Setting::query()->create([
                'key' => $key,
                'value' => $value,
            ]);
        });
    }
}
