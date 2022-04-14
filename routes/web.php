<?php

use App\Process;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes(['register' => false]);

Route::redirect('/', 'intranet/index.php');

Route::any('module/Api/{uri}', 'LegacyController@api')->where('uri', '.*');

Route::any('intranet/filaunica/educar_consulta.php', 'LegacyController@intranet')
    ->defaults('uri', 'filaunica/educar_consulta.php');

Route::any('intranet/suspenso.php', 'LegacyController@intranet')
    ->defaults('uri', 'suspenso.php');

Route::group(['middleware' => ['auth']], function () {
    Route::get('alterar-senha', 'PasswordController@change')->name('change-password');
    Route::post('alterar-senha', 'PasswordController@change')->name('post-change-password');
});

Route::group(['middleware' => ['ieducar.navigation', 'ieducar.footer', 'ieducar.xssbypass', 'ieducar.suspended', 'auth', 'ieducar.checkresetpassword']], function () {
    Route::get('/intranet/educar_matricula_turma_lst.php', 'LegacyController@intranet')
        ->defaults('uri', 'educar_matricula_turma_lst.php')
        ->name('enrollments.index');
    Route::get('/matricula/{registration}/enturmar/{schoolClass}', 'EnrollmentController@viewEnroll')
        ->name('enrollments.enroll.create');
    Route::post('/matricula/{registration}/enturmar/{schoolClass}', 'EnrollmentController@enroll')
        ->name('enrollments.enroll');
    Route::get('/enrollment-history/{id}', 'EnrollmentHistoryController@show')
        ->name('enrollments.enrollment-history');

    Route::get('/educacenso/consulta', 'EducacensoController@consult')
        ->name('educacenso.consult');

    Route::get('/enturmacao-em-lote/{schoolClass}', 'BatchEnrollmentController@indexEnroll')
        ->name('enrollments.batch.enroll.index');
    Route::post('/enturmacao-em-lote/{schoolClass}', 'BatchEnrollmentController@enroll')
        ->name('enrollments.batch.enroll');

    Route::get('/usuarios/tipos', 'LegacyController@intranet')
        ->defaults('uri', 'educar_tipo_usuario_lst.php')
        ->name('usertype.index');
    Route::get('/usuarios/tipos/novo', 'AccessLevelController@new')
        ->name('usertype.new');
    Route::get('/usuarios/tipos/{userType}', 'AccessLevelController@show')
        ->name('usertype.show');
    Route::post('/usuarios/tipos', 'AccessLevelController@create')
        ->name('usertype.create');
    Route::put('/usuarios/tipos/{userType}', 'AccessLevelController@update')
        ->name('usertype.update');
    Route::delete('/usuarios/tipos/{userType}', 'AccessLevelController@delete')
        ->name('usertype.delete');

    Route::get('/cancelar-enturmacao-em-lote/{schoolClass}', 'BatchEnrollmentController@indexCancelEnrollments')
        ->name('enrollments.batch.cancel.index');
    Route::post('/cancelar-enturmacao-em-lote/{schoolClass}', 'BatchEnrollmentController@cancelEnrollments')
        ->name('enrollments.batch.cancel');

    Route::get('/escolaridade/{schoolingDegree}', 'SchoolingDegreeController@show')
        ->name('schooling_degrees.show');

    Route::get('/unificacao-aluno', 'StudentLogUnificationController@index')->name('student-log-unification.index');
    Route::get('/unificacao-aluno/{unification}', 'StudentLogUnificationController@show')->name('student-log-unification.show');
    Route::get('/unificacao-aluno/{unification}/undo', 'StudentLogUnificationController@undo')->name('student-log-unification.undo');

    Route::get('/unificacao-pessoa', 'PersonLogUnificationController@index')->name('person-log-unification.index');
    Route::get('/unificacao-pessoa/{unification}', 'PersonLogUnificationController@show')->name('person-log-unification.show');

    Route::get('intranet/index.php', 'LegacyController@intranet')
        ->defaults('uri', 'index.php')
        ->name('home');

    Route::get('intranet/educar_configuracoes_index.php', 'LegacyController@intranet')
        ->defaults('uri', 'educar_configuracoes_index.php')
        ->name('settings');

    Route::any('module/{module}/{path}/{resource}', 'LegacyModuleRewriteController@rewrite')
        ->where('module', '.*')
        ->where('path', 'imagens|scripts|styles')
        ->where('resource', '.*');

    Route::any('module/{uri}', 'LegacyController@module')->where('uri', '.*');
    Route::any('modules/{uri}', 'LegacyController@modules')->where('uri', '.*');
    Route::any('intranet/{uri}', 'LegacyController@intranet')->where('uri', '.*');

    Route::group(['namespace' => 'Educacenso', 'prefix' => 'educacenso'], function () {
        Route::get('validar/{validator}', 'ValidatorController@validation');
        Route::post('importacao', 'ImportController@import')->middleware('can:modify:' . Process::EDUCACENSO_IMPORT_HISTORY);
        Route::get('importacao/historico', 'ImportController@index')->middleware('can:view:' . Process::EDUCACENSO_IMPORT_HISTORY)->name('educacenso.history');
    });

    Route::get('/consulta-dispensas', 'ExemptionListController@index')->name('exemption-list.index');
    Route::get('/backup-download', 'BackupController@download')->name('backup.download');

    Route::get('/atualiza-situacao-matriculas', 'UpdateRegistrationStatusController@index')->middleware('can:view:' . Process::UPDATE_REGISTRATION_STATUS)->name('update-registration-status.index');
    Route::post('/atualiza-situacao-matriculas', 'UpdateRegistrationStatusController@updateStatus')->middleware('can:modify:' . Process::UPDATE_REGISTRATION_STATUS)->name('update-registration-status.update-status');

    Route::get('/exportacao-para-o-seb', 'SebExportController@index')->name('seb-export.index');
    Route::post('/exportacao-para-o-seb', 'SebExportController@export')->name('seb-export.export');

    Route::get('/abre-url-privada', 'OpenPrivateUrlController@open')->name('open_private_url.open');

    Route::get('/notificacoes', 'NotificationController@index')->name('notifications.index');
    Route::get('/notificacoes/retorna-notificacoes-usuario', 'NotificationController@getByLoggedUser')->name('notifications.get-by-logged-user');
    Route::get('/notificacoes/quantidade-nao-lidas', 'NotificationController@getNotReadCount')->name('notifications.get-not-read-count');
    Route::post('/notificacoes/marca-como-lida', 'NotificationController@markAsRead')->name('notifications.mark-as-read');
    Route::post('/notificacoes/marca-todas-como-lidas', 'NotificationController@markAllRead')->name('notifications.mark-all-read');

    Route::get('/exportacoes', 'ExportController@index')->middleware('can:view:' . Process::DATA_EXPORT)->name('export.index');
    Route::get('/exportacoes/novo', 'ExportController@form')->middleware('can:modify:' . Process::DATA_EXPORT)->name('export.form');
    Route::post('/exportacoes/exportar', 'ExportController@export')->middleware('can:modify:' . Process::DATA_EXPORT)->name('export.export');

    Route::get('/atualiza-data-entrada', 'UpdateRegistrationDateController@index')->middleware('can:view:' . Process::UPDATE_REGISTRATION_DATE)->name('update-registration-date.index');
    Route::post('/atualiza-data-entrada', 'UpdateRegistrationDateController@updateStatus')->middleware('can:modify:' . Process::UPDATE_REGISTRATION_DATE)->name('update-registration-date.update-date');

    Route::get('/configuracoes/configuracoes-de-sistema', 'SettingController@index')->name('settings.index');
    Route::post('/configuracoes/configuracoes-de-sistema', 'SettingController@saveInputs')->name('settings.update');
    Route::get('/periodo-lancamento/excluir', 'ReleasePeriodController@delete')->name('release-period.delete');
    Route::get('/periodo-lancamento/fomulario/{releasePeriod?}', 'ReleasePeriodController@form')->name('release-period.form');
    Route::post('/periodo-lancamento/criar', 'ReleasePeriodController@create')->name('release-period.create');
    Route::post('/periodo-lancamento/atualizar/{releasePeriod}', 'ReleasePeriodController@update')->name('release-period.update');
    Route::get('/periodo-lancamento', 'ReleasePeriodController@index')->name('release-period.index');
    Route::get('/periodo-lancamento/{releasePeriod}', 'ReleasePeriodController@show')->name('release-period.show');

    Route::post('/upload', 'FileController@upload')->name('file-upload');

    Route::get('/alterar-tipo-boletim-turmas', 'UpdateSchoolClassReportCardController@index')->name('update-school-class-report-card.index');
    Route::post('/alterar-tipo-boletim-turmas', 'UpdateSchoolClassReportCardController@update')->name('update-school-class-report-card.update-date');

    Route::get('/dispensa-lote', 'BatchExemptionController@index')->middleware('can:modify:' . Process::BATCH_EXEMPTION)->name('batch-exemption.index');
    Route::post('/dispensa-lote', 'BatchExemptionController@exempt')->middleware('can:modify:' . Process::BATCH_EXEMPTION)->name('batch-exemption.exempt');
});

Route::group(['namespace' => 'Exports', 'prefix' => 'exports'], function () {
    Route::get('students', 'StudentsController@export');
});
