<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
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
     * {@inheritdoc}
     */
    public function username()
    {
        return 'login';
    }

    /**
     * Validate the user login request.
     *
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
            'grecaptcha' => [new ReCaptchaV3],
        ], [
            $this->username() . '.required' => 'O campo matrícula é obrigatório.',
            $this->username() . '.string' => 'O campo matrícula é obrigatório.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.string' => 'O campo senha é obrigatório.',
        ]);
    }

    protected function authenticated(Request $request, $user)
    {
        $announcement = Announcement::query()
            ->whereHas('userTypes', fn ($q) => $q->whereKey($user->ref_cod_tipo_usuario))
            ->latest()->first();

        if (!$announcement) {
            return;
        }

        if ($announcement->repeat_on_login) {
            $this->resetAnnouncementConfirmation($announcement, $user);

            return redirect()->route('announcement.user.show');
        }

        if (!$this->userReadAnnouncement($announcement, $user)) {
            return redirect()->route('announcement.user.show');
        }
    }

    private function resetAnnouncementConfirmation(Announcement $announcement, $user): void
    {
        if ($announcement->show_confirmation) {
            $announcement->users()->updateExistingPivot($user->getKey(), ['confirmed_at' => null]);
        }
    }

    private function userReadAnnouncement(Announcement $announcement, $user): bool
    {
        return $announcement->users()
            ->whereKey($user->getKey())
            ->wherePivotNotNull('read_at')
            ->exists();
    }
}
