<?php

use App\Http\Controllers\Api\EmployeeWithdrawalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('version', 'Api\\VersionController@version');

Route::get('/postal-code/{postalCode}', 'Api\PostalCodeController@search');

Route::post('/students/{student}/rotate-picture', 'Api\StudentRotatePictureController@rotate');
Route::group([
    'middleware' => 'api:rest',
], function () {
    Route::put('/students/{student}/update-state-registration', 'Api\StudentController@updateStateRegistration');
});

Route::get('/school-class/calendars', 'Api\SchoolClassController@getCalendars');
Route::get('/school-class/stages/{schoolClass}', 'Api\SchoolClassController@getStages');

Route::delete('/employee-withdrawal/{id}', [EmployeeWithdrawalController::class, 'remove']);
