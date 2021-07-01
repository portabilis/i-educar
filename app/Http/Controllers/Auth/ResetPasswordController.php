<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ChangeUserPasswordService;
use App\User;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ChangeUserPasswordService $changeUserPasswordService)
    {
        $this->changeUserPasswordService = $changeUserPasswordService;
        $this->middleware('guest');
    }

    /**
     * @inheritdoc
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            'login' => 'required',
            'password' => 'required|confirmed',
        ];
    }

    /**
     * @return array
     */
    protected function validationErrorMessages()
    {
        return [
            'password.required' => 'O campo senha é obrigatório.',
            'password.confirmed' => 'As senhas não são iguais.',
        ];
    }

    /**
     * @inheritdoc
     */
    protected function credentials(Request $request)
    {
        return $request->only(
            'login',
            'password',
            'password_confirmation',
            'token'
        );
    }

    public function reset(Request $request, User $user)
    {
        $request->validate($this->rules(), $this->validationErrorMessages());

        $response = $this->broker()->reset(
            $this->credentials($request),
            function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        if ($response == Password::PASSWORD_RESET) {
            $employee = $user->employee;
            $this->changeUserPasswordService->execute($employee, $request->get('password'));
            return $this->sendResetResponse($request, $response);
        }

        return $this->sendResetFailedResponse($request, $response);
    }
}
