<?php

use App\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Setting::query()
            ->whereIn('key', [
                'legacy.code',
                'legacy.env',
                'legacy.app.administrative_pending.exist',
                'legacy.app.administrative_pending.msg',
                'legacy.app.aws.bucketname',
                'legacy.app.aws.awsacesskey',
                'legacy.app.aws.awssecretkey',
                'legacy.app.template.layout',
                'legacy.app.admin.reports.sql_tempo',
                'legacy.app.admin.reports.pagina_tempo',
                'legacy.app.admin.reports.emails',
                'legacy.app.superuser',
                'legacy.app.instituicao.data_base_deslocamento',
                'legacy.app.novoeducacao.caminho_api',
                'legacy.app.novoeducacao.url',
                'legacy.app.mailer.smtp.from_name',
                'legacy.app.mailer.smtp.from_email',
                'legacy.app.mailer.smtp.host',
                'legacy.app.mailer.smtp.port',
                'legacy.app.mailer.smtp.auth',
                'legacy.app.mailer.smtp.username',
                'legacy.app.mailer.smtp.password',
                'legacy.app.mailer.smtp.encryption',
                'legacy.app.mailer.debug',
            ])
            ->delete();
    }
};
