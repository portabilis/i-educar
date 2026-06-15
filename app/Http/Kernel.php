<?php

namespace App\Http;

use App\Http\Middleware\AcceptJson;
use App\Http\Middleware\AnnouncementMiddleware;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\Authorize;
use App\Http\Middleware\ChangeAppName;
use App\Http\Middleware\CheckResetPassword;
use App\Http\Middleware\CheckToken;
use App\Http\Middleware\ConnectTenantDatabase;
use App\Http\Middleware\Footer;
use App\Http\Middleware\LoadSettings;
use App\Http\Middleware\Navigation;
use App\Http\Middleware\PreventIframe;
use App\Http\Middleware\PreventRequestsDuringMaintenance;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\SetLayoutVariables;
use App\Http\Middleware\Suspended;
use App\Http\Middleware\TrimStrings;
use App\Http\Middleware\TrustProxies;
use App\Http\Middleware\XssByPass;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Auth\Middleware\RequirePassword;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        TrustProxies::class,
        HandleCors::class,
        PreventRequestsDuringMaintenance::class,
        ValidatePostSize::class,
        TrimStrings::class,
        ConvertEmptyStringsToNull::class,
        ChangeAppName::class,
        ConnectTenantDatabase::class,
        LoadSettings::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            SubstituteBindings::class,
            SetLayoutVariables::class,
            AnnouncementMiddleware::class,
            PreventIframe::class,
        ],

        'api' => [
            EnsureFrontendRequestsAreStateful::class,
            AcceptJson::class,
            'bindings',
            'throttle:60,1',
        ],

        'api:rest' => [
            'bindings',
            CheckToken::class,
            'throttle:60,1',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $middlewareAliases = [
        'auth' => Authenticate::class,
        'auth.basic' => AuthenticateWithBasicAuth::class,
        'bindings' => SubstituteBindings::class,
        'cache.headers' => SetCacheHeaders::class,
        'can' => Authorize::class,
        'guest' => RedirectIfAuthenticated::class,
        'password.confirm' => RequirePassword::class,
        'signed' => ValidateSignature::class,
        'throttle' => ThrottleRequests::class,
        'ieducar.navigation' => Navigation::class,
        'ieducar.setlayoutvariables' => SetLayoutVariables::class,
        'ieducar.footer' => Footer::class,
        'ieducar.xssbypass' => XssByPass::class,
        'ieducar.suspended' => Suspended::class,
        'verified' => EnsureEmailIsVerified::class,
        'ieducar.checkresetpassword' => CheckResetPassword::class,
    ];
}
