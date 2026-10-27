<?php

use App\Http\Controllers\SocialiteCallbackController;
use App\Http\Controllers\SocialiteRedirectController;
use App\Http\Controllers\TransferWebhookCallbackController;
use App\Http\Controllers\WebController;
use App\Http\Middleware\ValidToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes(['register' => false]);

Route::get('/', [WebController::class, 'home']);

Route::view('/docs-api', 'docs/api/index');

Route::get('/intranet/index.php', [WebController::class, 'home'])
    ->name('home');

Route::any('module/Api/{uri}', 'LegacyController@api')->where('uri', '.*');

Route::any('intranet/suspenso.php', 'LegacyController@intranet')
    ->defaults('uri', 'suspenso.php');

Route::group(['middleware' => ['auth']], function () {
    Route::get('alterar-senha', 'PasswordController@change')->name('change-password');
    Route::post('alterar-senha', 'PasswordController@change')->name('post-change-password');
});

Route::group(
    ['middleware' => ['ieducar.navigation', 'ieducar.footer', 'ieducar.xssbypass', 'ieducar.suspended', 'auth', 'ieducar.checkresetpassword']],
    base_path('routes/web-authenticated.php')
);

Route::middleware('guest')->get('/auth/redirect', SocialiteRedirectController::class)->name('socialite.redirect');
Route::get('/auth/callback', SocialiteCallbackController::class)->name('socialite.callback');

Route::post('/webhook/transfer/{id}', TransferWebhookCallbackController::class)
    ->name('webhook.transfer.callback')
    ->middleware(ValidToken::class);
