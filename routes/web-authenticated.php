<?php

use App\Http\Controllers\EnrollmentInepController;
use App\Http\Controllers\EnrollmentsPromotionController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\LegacyController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\WebController;
use App\Http\Middleware\AnnouncementMiddleware;
use App\Process;
use Illuminate\Support\Facades\Route;

/*
 * Rotas das telas para usuário autenticado, carregadas pelo routes/web.php
 * dentro do grupo com os middlewares de navegação, rodapé e sessão.
 */

Route::get('/config', [WebController::class, 'config']);
Route::get('/user', [WebController::class, 'user']);
Route::get('/institution', [WebController::class, 'institution']);
Route::get('/menus', [WebController::class, 'menus']);
Route::get('/authorization', [WebController::class, 'authorization']);

$legacyIntranet = [LegacyController::class, 'intranet'];

Route::get('/intranet/educar_matricula_turma_lst.php', $legacyIntranet)
    ->defaults('uri', 'educar_matricula_turma_lst.php')
    ->name('enrollments.index');
Route::get('/matricula/{registration}/enturmar/{schoolClass}', 'EnrollmentController@viewEnroll')
    ->name('enrollments.enroll.create');
Route::post('/matricula/{registration}/enturmar/{schoolClass}', 'EnrollmentController@enroll')
    ->middleware('can:modify:' . Process::ENROLLMENT)
    ->name('enrollments.enroll');
Route::get('/matricula/{registration}/remanejar/{schoolClass}', 'EnrollmentController@viewRelocate')
    ->middleware('can:view:' . Process::RELOCATE)
    ->name('enrollments.relocate.create');
Route::post('/matricula/{registration}/remanejar/{schoolClass}', 'EnrollmentController@relocate')
    ->middleware('can:modify:' . Process::RELOCATE)
    ->name('enrollments.relocate');
Route::get('/enrollment-history/{id}', 'EnrollmentHistoryController@show')
    ->middleware('can:view:' . Process::ENROLLMENT_HISTORY)
    ->name('enrollments.enrollment-history');
Route::get('/enrollment-inep/{enrollment}', [EnrollmentInepController::class, 'edit'])
    ->name('enrollments.enrollment-inep.edit');
Route::post('/enrollment-inep/{enrollment}', [EnrollmentInepController::class, 'update'])
    ->name('enrollments.enrollment-inep.update');

Route::get('/educacenso/consulta', 'EducacensoController@consult')
    ->middleware('can:view:' . Process::EDUCACENSO_CONSULT)
    ->name('educacenso.consult');

Route::get('/enturmacao-em-lote/{schoolClass}', 'BatchEnrollmentController@indexEnroll')
    ->middleware('can:view:' . Process::BATCH_ENROLLMENT)
    ->name('enrollments.batch.enroll.index');
Route::post('/enturmacao-em-lote/{schoolClass}', 'BatchEnrollmentController@enroll')
    ->middleware('can:modify:' . Process::BATCH_ENROLLMENT)
    ->name('enrollments.batch.enroll');

Route::prefix('/usuarios/tipos')->group(function () use ($legacyIntranet) {
    Route::get('/', $legacyIntranet)
        ->defaults('uri', 'educar_tipo_usuario_lst.php')
        ->name('usertype.index');
    Route::get('/novo', 'AccessLevelController@new')
        ->middleware('can:modify:' . Process::USER_TYPES)
        ->name('usertype.new');
    Route::get('/{userType}', 'AccessLevelController@show')
        ->middleware('can:view:' . Process::USER_TYPES)
        ->name('usertype.show');
    Route::post('/', 'AccessLevelController@create')
        ->middleware('can:modify:' . Process::USER_TYPES)
        ->name('usertype.create');
    Route::put('/{userType}', 'AccessLevelController@update')
        ->middleware('can:modify:' . Process::USER_TYPES)
        ->name('usertype.update');
    Route::delete('/{userType}', 'AccessLevelController@delete')
        ->middleware('can:remove:' . Process::USER_TYPES)
        ->name('usertype.delete');
});

Route::get('/cancelar-enturmacao-em-lote/{schoolClass}', 'BatchEnrollmentController@indexCancelEnrollments')
    ->middleware('can:view:' . Process::BATCH_ENROLLMENT)
    ->name('enrollments.batch.cancel.index');
Route::post('/cancelar-enturmacao-em-lote/{schoolClass}', 'BatchEnrollmentController@cancelEnrollments')
    ->middleware('can:modify:' . Process::BATCH_ENROLLMENT)
    ->name('enrollments.batch.cancel');

Route::get('/cancelar-matricula-em-lote/{schoolClass}', 'BatchEnrollmentController@indexCancelRegistrations')
    ->middleware(['can:modify:' . Process::REGISTRATIONS, 'can:remove:' . Process::CANCEL_REGISTRATION])
    ->name('registrations.batch.cancel.index');
Route::post('/cancelar-matricula-em-lote/{schoolClass}', 'BatchEnrollmentController@cancelRegistrations')
    ->middleware(['can:modify:' . Process::REGISTRATIONS, 'can:remove:' . Process::CANCEL_REGISTRATION])
    ->name('registrations.batch.cancel');

Route::get('/escolaridade/{schoolingDegree}', 'SchoolingDegreeController@show')
    ->name('schooling_degrees.show');

Route::get('/unificacao-aluno', 'StudentLogUnificationController@index')->middleware('can:view:' . Process::STUDENT_UNIFICATION)->name('student-log-unification.index');
Route::get('/unificacao-aluno/{unification}', 'StudentLogUnificationController@show')->middleware('can:view:' . Process::STUDENT_UNIFICATION)->name('student-log-unification.show');
Route::get('/unificacao-aluno/{unification}/undo', 'StudentLogUnificationController@undo')->middleware('can:remove:' . Process::STUDENT_UNIFICATION)->name('student-log-unification.undo');

Route::get('/unificacao-pessoa', 'PersonLogUnificationController@index')->name('person-log-unification.index');
Route::get('/unificacao-pessoa/{unification}', 'PersonLogUnificationController@show')->name('person-log-unification.show');

Route::get('intranet/educar_configuracoes_index.php', $legacyIntranet)
    ->defaults('uri', 'educar_configuracoes_index.php')
    ->name('settings');

Route::any('module/{module}/{path}/{resource}', 'LegacyModuleRewriteController@rewrite')
    ->where('module', '.*')
    ->where('path', 'imagens|scripts|styles')
    ->where('resource', '.*');

Route::any('module/{uri}', 'LegacyController@module')->where('uri', '.*');
Route::any('modules/{uri}', 'LegacyController@modules')->where('uri', '.*');
Route::any('intranet/{uri}', $legacyIntranet)->where('uri', '.*');

Route::group(['namespace' => 'Educacenso', 'prefix' => 'educacenso'], function () {
    Route::get('validar/{validator}', 'ValidatorController@validation');
});

Route::get('/consulta-dispensas', 'ExemptionListController@index')->middleware('can:view:' . Process::EXEMPTION_LIST)->name('exemption-list.index');
Route::get('/backup-download', 'BackupController@download')->name('backup.download');

Route::get('/atualiza-situacao-matriculas', 'UpdateRegistrationStatusController@index')->middleware('can:view:' . Process::UPDATE_REGISTRATION_STATUS)->name('update-registration-status.index');
Route::post('/atualiza-situacao-matriculas', 'UpdateRegistrationStatusController@updateStatus')->middleware('can:modify:' . Process::UPDATE_REGISTRATION_STATUS)->name('update-registration-status.update-status');

Route::get('/exportacao-para-o-seb', 'SebExportController@index')->middleware('can:view:' . Process::SEB_EXPORT)->name('seb-export.index');
Route::post('/exportacao-para-o-seb', 'SebExportController@export')->middleware('can:modify:' . Process::SEB_EXPORT)->name('seb-export.export');

Route::get('/abre-url-privada', 'OpenPrivateUrlController@open')->name('open_private_url.open');

Route::get('/notificacoes', 'NotificationController@index')->name('notifications.index');
Route::get('/notificacoes/retorna-notificacoes-usuario', 'NotificationController@getByLoggedUser')->withoutMiddleware(AnnouncementMiddleware::class)->name('notifications.get-by-logged-user');
Route::get('/notificacoes/quantidade-nao-lidas', 'NotificationController@getNotReadCount')->name('notifications.get-not-read-count');
Route::post('/notificacoes/marca-como-lida', 'NotificationController@markAsRead')->name('notifications.mark-as-read');
Route::post('/notificacoes/marca-todas-como-lidas', 'NotificationController@markAllRead')->name('notifications.mark-all-read');

Route::get('/exportacoes', 'ExportController@index')->middleware('can:view:' . Process::DATA_EXPORT)->name('export.index');
Route::get('/exportacoes/novo', [ExportController::class, 'form'])->middleware('can:modify:' . Process::DATA_EXPORT)->name('export.form');
Route::post('/exportacoes/exportar', 'ExportController@export')->middleware('can:modify:' . Process::DATA_EXPORT)->name('export.export');

Route::get('/arquivo/exportacoes', 'FileExportController@index')->middleware('can:view:' . Process::DOCUMENT_EXPORT)->name('file.export.index');
Route::get('/arquivo/exportacoes/novo', 'FileExportController@create')->middleware('can:modify:' . Process::DOCUMENT_EXPORT)->name('file.export.create');
Route::post('/arquivo/exportacoes/novo', 'FileExportController@store')->middleware('can:modify:' . Process::DOCUMENT_EXPORT)->name('file.export.store');

Route::get('/avisos/publicacao', 'AnnouncementPublishController@index')->middleware('can:view:' . Process::ANNOUNCEMENT)->name('announcement.publish.index');
Route::get('/avisos/publicacao/criar', 'AnnouncementPublishController@create')->middleware('can:create:' . Process::ANNOUNCEMENT)->name('announcement.publish.create');
Route::post('/avisos/publicacao/criar', 'AnnouncementPublishController@store')->middleware('can:create:' . Process::ANNOUNCEMENT)->name('announcement.publish.store');
Route::get('/avisos/publicacao/{announcement}/editar', 'AnnouncementPublishController@edit')->middleware('can:modify:' . Process::ANNOUNCEMENT)->name('announcement.publish.edit');
Route::post('/avisos/publicacao/{announcement}/editar', 'AnnouncementPublishController@update')->middleware('can:modify:' . Process::ANNOUNCEMENT)->name('announcement.publish.update');
Route::get('/avisos', 'AnnouncementUserController@show')->withoutMiddleware(AnnouncementMiddleware::class)->name('announcement.user.show');
Route::post('/avisos', 'AnnouncementUserController@confirm')->withoutMiddleware(AnnouncementMiddleware::class)->name('announcement.user.confirm');

Route::get('/importacao-situacao-final', 'FinalStatusImportController@index')->middleware('can:view:' . Process::FINAL_STATUS_IMPORT)->name('final-status-import.index');
Route::post('/importacao-situacao-final/upload', 'FinalStatusImportController@upload')->middleware('can:modify:' . Process::FINAL_STATUS_IMPORT)->name('final-status-import.upload');
Route::get('/importacao-situacao-final/analise', 'FinalStatusImportController@analysis')->middleware('can:view:' . Process::FINAL_STATUS_IMPORT)->name('final-status-import.analysis');
Route::get('/importacao-situacao-final/mapeamento', 'FinalStatusImportController@showMapping')->middleware('can:modify:' . Process::FINAL_STATUS_IMPORT)->name('final-status-import.mapping');
Route::post('/importacao-situacao-final/importar', 'FinalStatusImportController@import')->middleware('can:modify:' . Process::FINAL_STATUS_IMPORT)->name('final-status-import.import');
Route::get('/importacao-situacao-final/status', 'FinalStatusImportController@status')->middleware('can:view:' . Process::FINAL_STATUS_IMPORT)->name('final-status-import.status');

Route::get('/atualiza-data-entrada', 'UpdateRegistrationDateController@index')->middleware('can:view:' . Process::UPDATE_REGISTRATION_DATE)->name('update-registration-date.index');
Route::post('/atualiza-data-entrada', 'UpdateRegistrationDateController@updateStatus')->middleware('can:modify:' . Process::UPDATE_REGISTRATION_DATE)->name('update-registration-date.update-date');

Route::get('/atualiza-etapa', 'StageController@edit')->middleware('can:modify:' . Process::STAGE)->name('stage.edit');
Route::post('/atualiza-etapa', 'StageController@update')->middleware('can:modify:' . Process::STAGE)->name('stage.update');

Route::get('/ano-letivo-em-lote', 'AcademicYearBatchController@edit')->middleware('can:modify:' . Process::ACADEMIC_YEAR_IMPORT)->name('academic-year.edit');
Route::post('/ano-letivo-em-lote/processar', 'AcademicYearBatchController@process')->middleware('can:modify:' . Process::ACADEMIC_YEAR_IMPORT)->name('academic-year.process');
Route::get('/ano-letivo-em-lote/status', 'AcademicYearBatchController@status')->middleware('can:view:' . Process::ACADEMIC_YEAR_IMPORT)->name('academic-year.status');

Route::get('/atualizacao-em-lote-series-escola', 'SchoolGradeBatchUpdateController@index')->middleware('can:view:' . Process::SCHOOL_GRADE)->name('school-grade.batch-update.index');
Route::get('/atualizacao-em-lote-series-escola/visualizacao', 'SchoolGradeBatchUpdateController@preview')->middleware('can:modify:' . Process::SCHOOL_GRADE)->name('school-grade.batch-update.preview');
Route::post('/atualizacao-em-lote-series-escola/processo', 'SchoolGradeBatchUpdateController@process')->middleware('can:modify:' . Process::SCHOOL_GRADE)->name('school-grade.batch-update.process');
Route::get('/atualizacao-em-lote-series-escola/status', 'SchoolGradeBatchUpdateController@status')->middleware('can:view:' . Process::SCHOOL_GRADE)->name('school-grade.batch-update.status');

Route::get('/gerenciamento-componentes/api/cursos', 'ComponentBatchManagerController@apiCourses')->middleware('can:view:' . Process::COMPONENT_BATCH_MANAGER)->name('component-batch-manager.api.courses');
Route::get('/gerenciamento-componentes/api/series', 'ComponentBatchManagerController@apiGrades')->middleware('can:view:' . Process::COMPONENT_BATCH_MANAGER)->name('component-batch-manager.api.grades');
Route::get('/gerenciamento-componentes/api/componentes', 'ComponentBatchManagerController@apiDisciplines')->middleware('can:view:' . Process::COMPONENT_BATCH_MANAGER)->name('component-batch-manager.api.disciplines');
Route::get('/gerenciamento-componentes', 'ComponentBatchManagerController@index')->middleware('can:view:' . Process::COMPONENT_BATCH_MANAGER)->name('component-batch-manager.index');
Route::get('/gerenciamento-componentes/novo', 'ComponentBatchManagerController@create')->middleware('can:modify:' . Process::COMPONENT_BATCH_MANAGER)->name('component-batch-manager.create');
Route::post('/gerenciamento-componentes/preview', 'ComponentBatchManagerController@preview')->middleware('can:modify:' . Process::COMPONENT_BATCH_MANAGER)->name('component-batch-manager.preview');
Route::post('/gerenciamento-componentes/executar', 'ComponentBatchManagerController@execute')->middleware('can:modify:' . Process::COMPONENT_BATCH_MANAGER)->name('component-batch-manager.execute');
Route::get('/gerenciamento-componentes/{componentBatchOperation}', 'ComponentBatchManagerController@show')->middleware('can:view:' . Process::COMPONENT_BATCH_MANAGER)->name('component-batch-manager.show');

Route::get('/bloquear-enturmacao', 'BlockEnrollmentController@edit')->middleware('can:modify:' . Process::BLOCK_ENROLLMENT)->name('block-enrollment.edit');
Route::post('/bloquear-enturmacao', 'BlockEnrollmentController@update')->middleware('can:modify:' . Process::BLOCK_ENROLLMENT)->name('block-enrollment.update');

Route::get('/configuracoes/configuracoes-de-sistema', 'SettingController@index')->name('settings.index');
Route::post('/configuracoes/configuracoes-de-sistema', 'SettingController@saveInputs')->name('settings.update');
Route::get('/periodo-lancamento/excluir', 'ReleasePeriodController@delete')->middleware('can:remove:' . Process::RELEASE_PERIOD)->name('release-period.delete');
Route::get('/periodo-lancamento/fomulario/{releasePeriod?}', 'ReleasePeriodController@form')->middleware('can:modify:' . Process::RELEASE_PERIOD)->name('release-period.form');
Route::post('/periodo-lancamento/criar', 'ReleasePeriodController@create')->middleware('can:modify:' . Process::RELEASE_PERIOD)->name('release-period.create');
Route::post('/periodo-lancamento/atualizar/{releasePeriod}', 'ReleasePeriodController@update')->middleware('can:modify:' . Process::RELEASE_PERIOD)->name('release-period.update');
Route::get('/periodo-lancamento', 'ReleasePeriodController@index')->middleware('can:view:' . Process::RELEASE_PERIOD)->name('release-period.index');
Route::get('/periodo-lancamento/{releasePeriod}', 'ReleasePeriodController@show')->middleware('can:view:' . Process::RELEASE_PERIOD)->name('release-period.show');

Route::post('/upload', 'FileController@upload')->name('file-upload');
Route::get('/files/{file}', 'FileController@show')->name('files.show');

Route::get('/alterar-tipo-boletim-turmas', 'UpdateSchoolClassReportCardController@index')->name('update-school-class-report-card.index');
Route::post('/alterar-tipo-boletim-turmas', 'UpdateSchoolClassReportCardController@update')->name('update-school-class-report-card.update-date');

Route::get('/dispensa-lote', 'BatchExemptionController@index')->middleware('can:modify:' . Process::BATCH_EXEMPTION)->name('batch-exemption.index');
Route::post('/dispensa-lote', 'BatchExemptionController@exempt')->middleware('can:modify:' . Process::BATCH_EXEMPTION)->name('batch-exemption.exempt');

Route::post('/turma', [SchoolClassController::class, 'store'])
    ->name('schoolclass.store');
Route::delete('/turma', [SchoolClassController::class, 'delete'])
    ->name('schoolclass.delete');

Route::post('/enrollments-promotion', [EnrollmentsPromotionController::class, 'processEnrollmentsPromotionJobs'])
    ->name('enrollments.promotion');

Route::fallback([WebController::class, 'fallback']);
