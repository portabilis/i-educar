<?php

use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\DisciplineController;
use App\Http\Controllers\Api\DistrictController;
use App\Http\Controllers\Api\EmployeeWithdrawalController;
use App\Http\Controllers\Api\GradeController;
use App\Http\Controllers\Api\InstitutionController;
use App\Http\Controllers\Api\People\LegacyDeficiencyController;
use App\Http\Controllers\Api\PeriodController;
use App\Http\Controllers\Api\RegistrationController;
use App\Http\Controllers\Api\ReligionController;
use App\Http\Controllers\Api\SchoolClassController;
use App\Http\Controllers\Api\SchoolController;
use App\Http\Controllers\Api\SituationController;
use App\Http\Controllers\Api\StageController;
use App\Http\Controllers\Api\StateController;
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

Route::group(
    [
        'middleware' => 'auth:sanctum',
    ],
    static fn () => Route::apiResources([
        'country' => CountryController::class,
        'state' => StateController::class,
        'district' => DistrictController::class,
        'city' => CityController::class,
        'religion' => ReligionController::class,
        'person/deficiency' => LegacyDeficiencyController::class,
    ])
);

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

Route::group(['middleware' => 'auth:sanctum', 'namespace' => 'Api'], static function () {
    Route::resource('institution', InstitutionController::class)->only(['index']);
    Route::resource('school', SchoolController::class)->only(['index']);
    Route::resource('course', CourseController::class)->only(['index']);
    Route::resource('grade', GradeController::class)->only(['index']);
    Route::resource('school-class', SchoolClassController::class)->only(['index']);
    Route::resource('registration', RegistrationController::class)->only(['index']);
    Route::resource('situation', SituationController::class)->only(['index']);
    Route::resource('period', PeriodController::class)->only(['index']);
    Route::resource('discipline', DisciplineController::class)->only(['index']);
    Route::resource('stage', StageController::class)->only(['index']);
});

Route::group(['prefix' => 'resource', 'as' => 'api.resource.', 'namespace' => 'Api\Resource'], static function () {
    Route::get('course', 'Course\ResourceCourseController@index')->name('course');
    Route::get('grade', 'Grade\ResourceGradeController@index')->name('grade');
    Route::get('school-academic-year', 'SchoolAcademicYear\ResourceSchoolAcademicYearController@index')->name('school-academic-year');
    Route::get('school', 'School\ResourceSchoolController@index')->name('school');
    Route::get('school-class', 'SchoolClass\ResourceSchoolClassController@index')->name('school-class');
    Route::get('evaluation-rule', 'EvaluationRule\ResourceEvaluationRuleController@index')->name('evaluation-rule');
    Route::get('discipline', 'Discipline\ResourceDisciplineController@index')->name('discipline');
    Route::get('country', 'Country\ResourceCountryController@index')->name('country');
});
