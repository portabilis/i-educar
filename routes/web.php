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
Route::any('/module/{uri}', 'LegacyController@module')->where('uri', '.*');
Route::any('/modules/{uri}', 'LegacyController@modules')->where('uri', '.*');
Route::any('/intranet/{uri}', 'LegacyController@intranet')->where('uri', '.*');
