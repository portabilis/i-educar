<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Rules\ReCaptchaV3;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    protected int $maxAttempts = 2;
    protected int $decayMinutes = 1;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @param Request $request
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware('guest')->except('logout');

        if (empty($request->query('force'))) {
            $this->middleware('ieducar.suspended')->except('login');
        }
    }

    /**
     * @inheritdoc
     */
    public function username()
    {
        return 'login';
    }

    /**
     * Validate the user login request.
     *
     * @param Request $request
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @return void
     */
    public function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
            'grecaptcha' => [new ReCaptchaV3()],
        ]);
    }
}
