<?php

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

Route::redirect('/', 'intranet/index.php');

Route::get('intranet/index.php', 'LegacyController@intranet')
    ->defaults('uri', 'index.php')
    ->middleware(['ieducar.navigation', 'ieducar.menu', 'ieducar.footer'])
    ->name('home');

Route::get('intranet/educar_configuracoes_index.php', 'LegacyController@intranet')
    ->defaults('uri', 'educar_configuracoes_index.php')
    ->middleware(['ieducar.navigation', 'ieducar.menu', 'ieducar.footer'])
    ->name('settings');

Route::any('/module/{uri}', 'LegacyController@module')
    ->middleware(['ieducar.navigation', 'ieducar.menu', 'ieducar.footer'])
    ->where('uri', '.*');
Route::any('/modules/{uri}', 'LegacyController@modules')
    ->middleware(['ieducar.navigation', 'ieducar.menu', 'ieducar.footer'])
    ->where('uri', '.*');
Route::any('/intranet/{uri}', 'LegacyController@intranet')
    ->middleware(['ieducar.navigation', 'ieducar.menu', 'ieducar.footer'])
    ->where('uri', '.*');

Route::group([
    'middleware' => [
        'ieducar.authenticatesession',
        'ieducar.setlayoutvariables',
        'ieducar.navigation',
        'ieducar.menu',
        'ieducar.footer',
    ]
], function () {
    Route::namespace('Enrollment')->prefix('enrollment')->group(function () {
        Route::get('update-enrollments-status', 'UpdateEnrollmentsStatus@index');
    });
});
