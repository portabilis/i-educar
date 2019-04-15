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

Route::group(['middleware' => ['ieducar.navigation', 'ieducar.menu', 'ieducar.footer', 'ieducar.xssbypass', 'auth']], function () {

    Route::get('/enturmacao-em-lote/{schoolClass}', 'BatchEnrollmentController@indexEnroll')
        ->name('enrollments.batch.enroll.index');
    Route::post('/enturmacao-em-lote/{schoolClass}', 'BatchEnrollmentController@enroll')
        ->name('enrollments.batch.enroll');

    Route::get('/cancelar-enturmacao-em-lote/{schoolClass}', 'BatchEnrollmentController@indexCancelEnrollments')
        ->name('enrollments.batch.cancel.index');
    Route::post('/cancelar-enturmacao-em-lote/{schoolClass}', 'BatchEnrollmentController@cancelEnrollments')
        ->name('enrollments.batch.cancel');

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

});

Route::group(['namespace' => 'Exports', 'prefix' => 'exports'], function () {
    Route::get('students', 'StudentsController@export');
});
