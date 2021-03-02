<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class PasswordController extends Controller
{
    use ResetsPasswords;

    public function change(Request $request, User $user)
    {
        if ($request->isMethod('get')) {
            return view('password.change');
        }

        $request->validate($this->rules(), $this->validationErrorMessages());

        $token = Password::createToken($user);
        $request->request->add(['token' => $token]);

        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
            $this->resetPassword($user, $password);
        });

        if ($response == Password::PASSWORD_RESET) {
            $employee = $user->employee;
            $employee->force_reset_password = false;
            $employee->save();

            return $this->sendResetResponse($request, $response);
        }

        return $this->sendResetFailedResponse($request, $response);
    }

    protected function rules()
    {
        return [
            'login' => 'required',
            'password' => 'required|confirmed|min:8',
        ];
    }

    protected function redirectTo()
    {
        return '/';
    }

    /**
     * @return array
     */
    protected function validationErrorMessages()
    {
        return [
            'password.required' => 'O campo senha é obrigatório.',
            'password.confirmed' => 'As senhas não são iguais.',
            'password.min' => 'A senha deve conter ao menos 8 caracteres.',
        ];
    }

    /**
     * @inheritdoc
     */
    protected function credentials(Request $request)
    {
        return $request->only(
            'login', 'password', 'password_confirmation', 'token'
        );
    }
}
