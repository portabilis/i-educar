<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['register' => false]);

Route::redirect('/', 'intranet/index.php');

Route::any('module/Api/{uri}', 'LegacyController@api')->where('uri', '.*');

Route::any('intranet/filaunica/educar_consulta.php', 'LegacyController@intranet')
    ->defaults('uri', 'filaunica/educar_consulta.php');

Route::any('intranet/suspenso.php', 'LegacyController@intranet')
    ->defaults('uri', 'suspenso.php');

Route::group(['middleware' => ['ieducar.navigation', 'ieducar.footer', 'ieducar.xssbypass', 'ieducar.suspended', 'auth']], function () {
    Route::get('/intranet/educar_matricula_turma_lst.php', 'LegacyController@intranet')
        ->defaults('uri', 'educar_matricula_turma_lst.php')
        ->name('enrollments.index');
    Route::get('/matricula/{registration}/enturmar/{schoolClass}', 'EnrollmentController@viewEnroll')
        ->name('enrollments.enroll.create');
    Route::post('/matricula/{registration}/enturmar/{schoolClass}', 'EnrollmentController@enroll')
        ->name('enrollments.enroll');

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
    });

    Route::get('/consulta-dispensas', 'ExemptionListController@index')->name('exemption-list.index');
    Route::get('/backup-download', 'BackupController@download')->name('backup.download');

    Route::get('/atualiza-situacao-matriculas', 'UpdateRegistrationStatusController@index')->name('update-registration-status.index');
    Route::post('/atualiza-situacao-matriculas', 'UpdateRegistrationStatusController@updateStatus')->name('update-registration-status.update-status');

    Route::get('/abre-url-privada', 'OpenPrivateUrlController@open')->name('open_private_url.open');
});

Route::group(['namespace' => 'Exports', 'prefix' => 'exports'], function () {
    Route::get('students', 'StudentsController@export');
});
